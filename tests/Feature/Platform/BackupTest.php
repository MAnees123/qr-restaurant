<?php

namespace Tests\Feature\Platform;

use App\Models\Discount;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use App\Services\Platform\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    private array $dirs = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        foreach ($this->dirs as $id) File::deleteDirectory(storage_path('app/backups/' . $id));
        parent::tearDown();
    }

    private function admin(array $f = ['backup'])
    {
        $a = $this->adminWith($f);
        $this->dirs[] = $a->restaurant_id;
        return $a;
    }

    private function seedTenant($r): array
    {
        $cat = MenuCategory::create(['restaurant_id' => $r->id, 'name' => 'Mains']);
        $item = MenuItem::create(['restaurant_id' => $r->id, 'menu_category_id' => $cat->id, 'name' => 'Pizza', 'price' => 9]);
        $t = Table::create(['restaurant_id' => $r->id, 'table_number' => 'T1']);
        $o = Order::create(['restaurant_id' => $r->id, 'table_id' => $t->id, 'order_number' => 'O-' . $r->id, 'status' => 'pending', 'total_amount' => 9]);
        DB::table('order_items')->insert(['order_id' => $o->id, 'menu_item_id' => $item->id, 'quantity' => 1, 'unit_price' => 9, 'subtotal' => 9]);
        Discount::create(['restaurant_id' => $r->id, 'code' => 'D' . $r->id, 'type' => 'fixed', 'value' => 1]);
        return [$cat, $item, $t, $o];
    }

    public function test_backup_mutate_restore_round_trip(): void
    {
        $admin = $this->admin();
        $other = $this->makeRestaurant(['backup']);
        [$cat, $item, $t, $o] = $this->seedTenant($admin->restaurant);
        $this->seedTenant($other);

        $this->actingAs($admin)->post(route('admin.backups.store'))->assertRedirect(route('admin.backups.index'));
        $files = File::glob(storage_path('app/backups/' . $admin->restaurant_id . '/backup-*.json'));
        $this->assertCount(1, $files);
        $name = basename($files[0]);
        $this->actingAs($admin)->get(route('admin.backups.index'))->assertOk()->assertSee($name);
        $this->actingAs($admin)->get(route('admin.backups.download', $name))->assertOk();

        // mutate: delete order, change price, add junk, delete table
        $rid = $admin->restaurant_id;
        DB::table('order_items')->where('order_id', $o->id)->delete();
        $o->delete();
        $item->update(['price' => 99]);
        MenuItem::create(['restaurant_id' => $rid, 'menu_category_id' => $cat->id, 'name' => 'Junk', 'price' => 1]);
        Discount::where('restaurant_id', $rid)->delete();
        $otherCount = Order::where('restaurant_id', $other->id)->count();

        $this->actingAs($admin)->post(route('admin.backups.restore', $name), ['confirm' => 'RESTORE'])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(1, Order::where('restaurant_id', $rid)->count());
        $this->assertSame(1, DB::table('order_items')->where('order_id', $o->id)->count());
        $this->assertEquals(9, $item->fresh()->price);
        $this->assertSame(['Pizza'], MenuItem::where('restaurant_id', $rid)->pluck('name')->all());
        $this->assertSame(1, Discount::where('restaurant_id', $rid)->count());
        // other tenant untouched
        $this->assertSame($otherCount, Order::where('restaurant_id', $other->id)->count());
        $this->assertSame(1, MenuItem::where('restaurant_id', $other->id)->count());
        $this->assertSame(1, DB::table('order_items')->whereIn('order_id', Order::where('restaurant_id', $other->id)->pluck('id'))->count());
    }

    public function test_restore_requires_confirmation_and_delete_works(): void
    {
        $admin = $this->admin();
        $this->seedTenant($admin->restaurant);
        $name = app(BackupService::class)->create($admin->restaurant_id)['name'];

        $this->actingAs($admin)->post(route('admin.backups.restore', $name), [])->assertSessionHasErrors('confirm');
        $this->actingAs($admin)->post(route('admin.backups.restore', $name), ['confirm' => 'yes'])->assertSessionHasErrors('confirm');

        $this->actingAs($admin)->delete(route('admin.backups.destroy', $name))->assertRedirect();
        $this->assertNull(app(BackupService::class)->path($admin->restaurant_id, $name));
        $this->actingAs($admin)->delete(route('admin.backups.destroy', $name))->assertStatus(404);
    }

    public function test_other_tenant_cannot_access_backup_files(): void
    {
        $a = $this->admin();
        $b = $this->admin();
        $name = app(BackupService::class)->create($a->restaurant_id)['name'];

        $this->actingAs($b)->get(route('admin.backups.download', $name))->assertStatus(404);
        $this->actingAs($b)->post(route('admin.backups.restore', $name), ['confirm' => 'RESTORE'])->assertStatus(404);
        $this->actingAs($b)->delete(route('admin.backups.destroy', $name))->assertStatus(404);
        $this->actingAs($b)->get(route('admin.backups.index'))->assertOk()->assertDontSee($name);
        $this->actingAs($b)->get('/admin/backups/..%2F..%2F.env/download')->assertStatus(404);
    }

    public function test_foreign_backup_content_is_rejected_on_restore(): void
    {
        $a = $this->admin();
        $b = $this->admin();
        $svc = app(BackupService::class);
        $name = $svc->create($a->restaurant_id)['name'];
        // copy A's file into B's dir (tampering)
        File::ensureDirectoryExists($svc->dir($b->restaurant_id));
        File::copy($svc->dir($a->restaurant_id) . '/' . $name, $svc->dir($b->restaurant_id) . '/' . $name);
        $this->seedTenant($b->restaurant);

        $this->actingAs($b)->post(route('admin.backups.restore', $name), ['confirm' => 'RESTORE'])->assertSessionHasErrors('restore');
        $this->assertSame(1, MenuItem::where('restaurant_id', $b->restaurant_id)->count());
    }

    public function test_forbidden_without_feature(): void
    {
        $admin = $this->admin([]);
        $this->actingAs($admin)->get(route('admin.backups.index'))->assertStatus(403);
        $this->actingAs($admin)->post(route('admin.backups.store'))->assertStatus(403);
        $this->assertSame([], app(BackupService::class)->list($admin->restaurant_id));
    }

    public function test_artisan_command_backs_up_only_granted_restaurants(): void
    {
        $on = $this->admin();
        $off = $this->admin([]);
        $this->artisan('backups:run')->assertSuccessful();
        $this->assertCount(1, app(BackupService::class)->list($on->restaurant_id));
        $this->assertCount(0, app(BackupService::class)->list($off->restaurant_id));
    }
}
