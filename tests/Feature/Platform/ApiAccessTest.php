<?php

namespace Tests\Feature\Platform;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Platform\ApiToken;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function seedOrder($r, string $no = 'ORD-1'): Order
    {
        return Order::create(['restaurant_id' => $r->id, 'order_number' => $no, 'status' => 'pending', 'total_amount' => 10]);
    }

    public function test_admin_creates_token_shown_once_and_stored_hashed(): void
    {
        $admin = $this->adminWith(['api_access']);
        $resp = $this->actingAs($admin)->post(route('admin.api-tokens.store'), ['name' => 'POS', 'scope' => 'write']);
        $resp->assertRedirect(route('admin.api-tokens.index'));
        $plain = session('new_token');
        $this->assertStringStartsWith('qrk_', $plain);
        $row = ApiToken::first();
        $this->assertSame($admin->restaurant_id, $row->restaurant_id);
        $this->assertNotSame($plain, $row->token_hash);
        $this->assertSame(hash('sha256', $plain), $row->token_hash);
        $this->assertSame('read,write', $row->abilities);

        // revealed on the redirect target only; a later page view does not show it again
        $this->actingAs($admin)->get(route('admin.api-tokens.index'))->assertOk()->assertSee($plain);
        $this->actingAs($admin)->get(route('admin.api-tokens.index'))->assertOk()->assertDontSee($plain);
        $this->actingAs($admin)->get(route('admin.api-tokens.docs'))->assertOk()->assertSee('/api/v1/orders');
    }

    public function test_token_creation_validation_fails(): void
    {
        $admin = $this->adminWith(['api_access']);
        $this->actingAs($admin)->post(route('admin.api-tokens.store'), ['name' => '', 'scope' => 'admin'])
            ->assertSessionHasErrors(['name', 'scope']);
        $this->assertSame(0, ApiToken::count());
    }

    public function test_admin_screens_blocked_without_feature(): void
    {
        $admin = $this->adminWith(['discounts']);
        $this->actingAs($admin)->get(route('admin.api-tokens.index'))->assertStatus(403);
        $this->actingAs($admin)->post(route('admin.api-tokens.store'), ['name' => 'x', 'scope' => 'read'])->assertStatus(403);
        $this->assertSame(0, ApiToken::count());
    }

    public function test_api_returns_only_own_restaurant_data(): void
    {
        $a = $this->makeRestaurant(['api_access']);
        $b = $this->makeRestaurant(['api_access']);
        $catA = MenuCategory::create(['restaurant_id' => $a->id, 'name' => 'A cat']);
        $catB = MenuCategory::create(['restaurant_id' => $b->id, 'name' => 'B cat']);
        MenuItem::create(['restaurant_id' => $a->id, 'menu_category_id' => $catA->id, 'name' => 'A item', 'price' => 5]);
        MenuItem::create(['restaurant_id' => $b->id, 'menu_category_id' => $catB->id, 'name' => 'B item', 'price' => 5]);
        Table::create(['restaurant_id' => $a->id, 'table_number' => 'A1']);
        Table::create(['restaurant_id' => $b->id, 'table_number' => 'B1']);
        $oa = $this->seedOrder($a, 'A-1');
        $ob = $this->seedOrder($b, 'B-1');

        [, $plain] = ApiToken::issue($a->id, 'ta', false);
        $h = ['Authorization' => "Bearer $plain", 'Accept' => 'application/json'];

        $this->getJson('/api/v1/categories', $h)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'A cat');
        $this->getJson('/api/v1/items', $h)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'A item');
        $this->getJson('/api/v1/tables', $h)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.table_number', 'A1');
        $this->getJson('/api/v1/orders', $h)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.order_number', 'A-1');
        $this->getJson("/api/v1/orders/{$oa->id}", $h)->assertOk()->assertJsonPath('data.order_number', 'A-1');
        $this->getJson("/api/v1/orders/{$ob->id}", $h)->assertStatus(404);
        $this->assertNotNull(ApiToken::first()->last_used_at);
    }

    public function test_auth_failures_are_json(): void
    {
        $r = $this->makeRestaurant(['api_access']);
        $this->getJson('/api/v1/orders')->assertStatus(401)->assertJsonStructure(['error']);
        $this->getJson('/api/v1/orders', ['Authorization' => 'Bearer qrk_nope'])->assertStatus(401);

        [$t, $plain] = ApiToken::issue($r->id, 't', false);
        $t->update(['revoked_at' => now()]);
        $this->getJson('/api/v1/orders', ['Authorization' => "Bearer $plain"])->assertStatus(401);
    }

    public function test_token_of_restaurant_without_feature_is_forbidden(): void
    {
        $r = $this->makeRestaurant(['discounts']);
        [, $plain] = ApiToken::issue($r->id, 't', false);
        $this->getJson('/api/v1/orders', ['Authorization' => "Bearer $plain"])->assertStatus(403);
    }

    public function test_write_scope_needed_to_update_status_and_validation(): void
    {
        $r = $this->makeRestaurant(['api_access']);
        $other = $this->makeRestaurant(['api_access']);
        $o = $this->seedOrder($r);
        $foreign = $this->seedOrder($other, 'X-9');
        [, $ro] = ApiToken::issue($r->id, 'ro', false);
        [, $rw] = ApiToken::issue($r->id, 'rw', true);

        $this->patchJson("/api/v1/orders/{$o->id}/status", ['status' => 'ready'], ['Authorization' => "Bearer $ro"])->assertStatus(403);
        $this->assertSame('pending', $o->fresh()->status);

        $h = ['Authorization' => "Bearer $rw"];
        $this->patchJson("/api/v1/orders/{$o->id}/status", ['status' => 'bogus'], $h)->assertStatus(422);
        $this->patchJson("/api/v1/orders/{$o->id}/status", ['status' => 'ready'], $h)->assertOk()->assertJsonPath('data.status', 'ready');
        $this->assertSame('ready', $o->fresh()->status);
        $this->patchJson("/api/v1/orders/{$foreign->id}/status", ['status' => 'ready'], $h)->assertStatus(404);
        $this->assertSame('pending', $foreign->fresh()->status);
    }

    public function test_rate_limit_returns_429(): void
    {
        $r = $this->makeRestaurant(['api_access']);
        [$t, $plain] = ApiToken::issue($r->id, 't', false);
        $key = 'api-token:' . $t->id;
        for ($i = 0; $i < \App\Http\Middleware\ApiTokenAuth::PER_MINUTE; $i++) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
        }
        $this->getJson('/api/v1/tables', ['Authorization' => "Bearer $plain"])->assertStatus(429);
    }

    public function test_revoke_token_and_cross_tenant_revoke_is_404(): void
    {
        $admin = $this->adminWith(['api_access']);
        $other = $this->makeRestaurant(['api_access']);
        [$mine] = ApiToken::issue($admin->restaurant_id, 'mine', false);
        [$theirs] = ApiToken::issue($other->id, 'theirs', false);

        $this->actingAs($admin)->delete(route('admin.api-tokens.destroy', $theirs))->assertStatus(404);
        $this->assertNull($theirs->fresh()->revoked_at);
        $this->actingAs($admin)->delete(route('admin.api-tokens.destroy', $mine))->assertRedirect();
        $this->assertNotNull($mine->fresh()->revoked_at);
    }
}
