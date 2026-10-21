<?php

namespace Tests\Feature\Platform;

use App\Models\Discount;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class ExportImportTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    private array $rids = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        foreach ($this->rids as $id) File::deleteDirectory(storage_path('app/imports/' . $id));
        parent::tearDown();
    }

    private function admin(array $f = ['export_import'])
    {
        $a = $this->adminWith($f);
        $this->rids[] = $a->restaurant_id;
        return $a;
    }

    private function csv(string $content): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('data.csv', $content);
    }

    private function token(string $html): string
    {
        preg_match('/name="token" value="([0-9a-f]{32})"/', $html, $m);
        return $m[1] ?? '';
    }

    public function test_export_contains_only_own_rows_and_template_downloads(): void
    {
        $admin = $this->admin();
        $other = $this->makeRestaurant(['export_import']);
        $cat = MenuCategory::create(['restaurant_id' => $admin->restaurant_id, 'name' => 'Mains']);
        MenuItem::create(['restaurant_id' => $admin->restaurant_id, 'menu_category_id' => $cat->id, 'name' => 'Pizza', 'price' => 9]);
        $oc = MenuCategory::create(['restaurant_id' => $other->id, 'name' => 'Secret']);
        MenuItem::create(['restaurant_id' => $other->id, 'menu_category_id' => $oc->id, 'name' => 'Hidden', 'price' => 1]);

        $csv = $this->actingAs($admin)->get(route('admin.data.export', 'items'))->assertOk()->getContent();
        $this->assertStringContainsString('Pizza', $csv);
        $this->assertStringNotContainsString('Hidden', $csv);

        $this->actingAs($admin)->get(route('admin.data.template', 'tables'))->assertOk()->assertSee('table_number');
        $this->actingAs($admin)->get(route('admin.data.export', 'nonsense'))->assertStatus(404);
        $this->actingAs($admin)->get(route('admin.data.index'))->assertOk()->assertSee('Menu items');
    }

    public function test_preview_shows_row_errors_then_commit_imports_valid_rows_only(): void
    {
        $admin = $this->admin();
        $rid = $admin->restaurant_id;
        $content = "category,name,description,price,is_available\nStarters,Soup,Hot,5.50,1\nStarters,,No name,3,1\nMains,Steak,Grilled,abc,1\nMains,Burger,,12,yes\n";
        $resp = $this->actingAs($admin)->post(route('admin.data.preview', 'items'), ['file' => $this->csv($content)])->assertOk();
        $resp->assertSee("&#039;name&#039; is required.", false)->assertSee("&#039;price&#039; must be a non-negative number.", false);
        $this->assertSame(0, MenuItem::where('restaurant_id', $rid)->count(), 'preview must not write');
        $token = $this->token($resp->getContent());
        $this->assertNotSame('', $token);

        $this->actingAs($admin)->post(route('admin.data.commit', 'items'), ['token' => $token])->assertRedirect(route('admin.data.index'));
        $this->assertSame(['Burger', 'Soup'], MenuItem::where('restaurant_id', $rid)->orderBy('name')->pluck('name')->all());
        $this->assertEquals(5.5, MenuItem::where('name', 'Soup')->first()->price);
        $this->assertSame(2, MenuCategory::where('restaurant_id', $rid)->count());

        // token is single use
        $this->actingAs($admin)->post(route('admin.data.commit', 'items'), ['token' => $token])->assertStatus(404);
    }

    public function test_reimport_updates_existing_rows(): void
    {
        $admin = $this->admin();
        Table::create(['restaurant_id' => $admin->restaurant_id, 'table_number' => 'T1', 'capacity' => 2]);
        $resp = $this->actingAs($admin)->post(route('admin.data.preview', 'tables'), ['file' => $this->csv("table_number,capacity,is_active\nT1,6,1\nT2,4,0\n")]);
        $this->actingAs($admin)->post(route('admin.data.commit', 'tables'), ['token' => $this->token($resp->getContent())]);
        $this->assertSame(2, Table::where('restaurant_id', $admin->restaurant_id)->count());
        $this->assertSame(6, (int) Table::where('table_number', 'T1')->first()->capacity);
        $this->assertFalse((bool) Table::where('table_number', 'T2')->first()->is_active);
    }

    public function test_discount_import_validates_and_blocks_foreign_codes(): void
    {
        $admin = $this->admin();
        $other = $this->makeRestaurant(['export_import']);
        Discount::create(['restaurant_id' => $other->id, 'code' => 'TAKEN', 'type' => 'fixed', 'value' => 1]);
        $content = "code,type,value,is_active,starts_at,expires_at,usage_limit\nsave10,percentage,10,1,2026-01-01,2026-12-31,5\nTAKEN,fixed,2,1,,,\nBAD,percentage,150,1,,,\n";
        $resp = $this->actingAs($admin)->post(route('admin.data.preview', 'discounts'), ['file' => $this->csv($content)])->assertOk()
            ->assertSee('already used by another restaurant')->assertSee('Percentage cannot exceed 100');
        $this->actingAs($admin)->post(route('admin.data.commit', 'discounts'), ['token' => $this->token($resp->getContent())]);
        $this->assertSame(['SAVE10'], Discount::where('restaurant_id', $admin->restaurant_id)->pluck('code')->all());
        $this->assertSame(1, Discount::where('restaurant_id', $other->id)->count());
    }

    public function test_bad_files_are_rejected(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.data.preview', 'items'), [])->assertSessionHasErrors('file');
        $this->actingAs($admin)->post(route('admin.data.preview', 'items'), ['file' => $this->csv("foo,bar\n1,2\n")])
            ->assertSessionHasErrors('file');
        $this->actingAs($admin)->post(route('admin.data.preview', 'items'), ['file' => UploadedFile::fake()->create('x.exe', 5)])
            ->assertSessionHasErrors('file');
        $this->assertSame(0, MenuItem::where('restaurant_id', $admin->restaurant_id)->count());
    }

    public function test_other_tenant_cannot_commit_someone_elses_preview(): void
    {
        $a = $this->admin();
        $b = $this->admin();
        $before = MenuCategory::count();
        $resp = $this->actingAs($a)->post(route('admin.data.preview', 'categories'), ['file' => $this->csv("name\nSoups\n")]);
        $token = $this->token($resp->getContent());
        $this->actingAs($b)->post(route('admin.data.commit', 'categories'), ['token' => $token])->assertStatus(404);
        $this->assertSame($before, MenuCategory::count());
    }

    public function test_forbidden_without_feature(): void
    {
        $admin = $this->admin([]);
        $this->actingAs($admin)->get(route('admin.data.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('admin.data.export', 'items'))->assertStatus(403);
        $this->actingAs($admin)->post(route('admin.data.preview', 'items'), ['file' => $this->csv("category,name,price\nA,B,1\n")])->assertStatus(403);
    }
}
