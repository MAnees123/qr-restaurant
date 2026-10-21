<?php

namespace Tests\Feature\Platform;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Platform\AiSetting;
use App\Services\Platform\SmartTools;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class AiFeaturesTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function item($rid, string $name, float $price = 5): MenuItem
    {
        $cat = MenuCategory::firstOrCreate(['restaurant_id' => $rid, 'name' => 'Cat']);
        return MenuItem::create(['restaurant_id' => $rid, 'menu_category_id' => $cat->id, 'name' => $name, 'price' => $price]);
    }

    private function order($rid, array $items, string $no, $when = null, float $total = 10, string $status = 'served'): Order
    {
        $o = Order::create(['restaurant_id' => $rid, 'order_number' => $no, 'status' => $status, 'total_amount' => $total]);
        if ($when) DB::table('orders')->where('id', $o->id)->update(['created_at' => $when]);
        foreach ($items as $i) {
            DB::table('order_items')->insert(['order_id' => $o->id, 'menu_item_id' => $i->id, 'quantity' => 1, 'unit_price' => $i->price, 'subtotal' => $i->price]);
        }
        return $o;
    }

    public function test_template_description_without_llm(): void
    {
        Http::fake();
        $admin = $this->adminWith(['ai_features']);
        $resp = $this->actingAs($admin)->post(route('admin.ai.describe'), ['name' => 'Chocolate Cake', 'category' => 'Dessert']);
        $resp->assertRedirect();
        $d = session('ai_description');
        $this->assertSame('template', $d['source']);
        $this->assertStringContainsString('Chocolate Cake', $d['text']);
        Http::assertNothingSent();
        $this->actingAs($admin)->get(route('admin.ai.index'))->assertOk()->assertSee('nothing is sent to any external AI service');
    }

    public function test_llm_used_only_when_configured_and_falls_back_on_error(): void
    {
        $admin = $this->adminWith(['ai_features']);
        $this->actingAs($admin)->put(route('admin.ai.settings'), [
            'llm_url' => 'https://llm.example.test/v1/chat/completions', 'llm_key' => 'sk-secret', 'llm_model' => 'm1', 'target_margin' => 60,
        ])->assertRedirect();
        $s = AiSetting::first();
        $this->assertSame('sk-secret', $s->llm_key);
        $this->assertNotSame('sk-secret', DB::table('ai_settings')->value('llm_key'), 'key must be encrypted at rest');

        Http::fake(['llm.example.test/*' => Http::sequence()
            ->push(['choices' => [['message' => ['content' => 'LLM wrote this.']]]])
            ->push('boom', 500)]);
        $this->actingAs($admin)->post(route('admin.ai.describe'), ['name' => 'Soup']);
        $this->assertSame(['text' => 'LLM wrote this.', 'source' => 'llm'], session('ai_description'));
        Http::assertSent(fn ($r) => $r->hasHeader('Authorization', 'Bearer sk-secret') && $r['model'] === 'm1');

        $this->actingAs($admin)->post(route('admin.ai.describe'), ['name' => 'Soup']);
        $this->assertSame('template', session('ai_description')['source']);

        // page never prints the key
        $this->actingAs($admin)->get(route('admin.ai.index'))->assertOk()->assertDontSee('sk-secret');
    }

    public function test_settings_validation(): void
    {
        $admin = $this->adminWith(['ai_features']);
        $this->actingAs($admin)->put(route('admin.ai.settings'), ['llm_url' => 'http://insecure.test', 'target_margin' => 500])
            ->assertSessionHasErrors(['llm_url', 'target_margin']);
        $this->assertSame(0, AiSetting::count());
        $this->actingAs($admin)->post(route('admin.ai.describe'), [])->assertSessionHasErrors('name');
    }

    public function test_price_suggestion_math_and_history(): void
    {
        $admin = $this->adminWith(['ai_features']);
        $rid = $admin->restaurant_id;
        $item = $this->item($rid, 'Pasta', 8);
        $this->order($rid, [$item], 'P1');
        $this->order($rid, [$item], 'P2');

        $this->actingAs($admin)->post(route('admin.ai.price'), ['cost' => 3, 'margin' => 60, 'item_id' => $item->id])->assertRedirect();
        $p = session('ai_price');
        $this->assertSame(7.5, $p['suggested']);
        $this->assertSame(7.5, $p['rounded']);
        $this->assertSame(2, $p['history']['units_30d']);

        $this->actingAs($admin)->post(route('admin.ai.price'), ['cost' => 0, 'margin' => 99])->assertSessionHasErrors(['cost', 'margin']);
    }

    public function test_price_for_other_tenants_item_is_404(): void
    {
        $admin = $this->adminWith(['ai_features']);
        $other = $this->makeRestaurant(['ai_features']);
        $foreign = $this->item($other->id, 'Foreign');
        $this->actingAs($admin)->post(route('admin.ai.price'), ['cost' => 3, 'margin' => 50, 'item_id' => $foreign->id])->assertStatus(404);
        $this->actingAs($admin)->getJson(route('admin.ai.recommendations', ['item_id' => $foreign->id]))->assertStatus(404);
    }

    public function test_forecast_is_moving_average_of_recent_days(): void
    {
        $r = $this->makeRestaurant(['ai_features']);
        foreach (range(1, 7) as $d) {
            $this->order($r->id, [], "F$d", now()->subDays($d), 70);
        }
        $this->order($r->id, [], 'OLD', now()->subDays(60), 9999);
        $this->order($r->id, [], 'CANC', now()->subDays(1), 5000, 'cancelled');

        $fc = app(SmartTools::class)->forecast($r->id);
        $this->assertTrue($fc['has_data']);
        $this->assertCount(7, $fc['forecast']);
        // last 7 days: day-0 has 0, days 1..6 have 70 => 420/7 = 60
        $this->assertSame(60.0, $fc['forecast'][0]['revenue']);
        $this->assertSame(7 * 0 + array_sum(array_column($fc['forecast'], 'revenue')), $fc['total']);
    }

    public function test_recommendations_json_from_cooccurrence_scoped_to_tenant(): void
    {
        $admin = $this->adminWith(['ai_features']);
        $rid = $admin->restaurant_id;
        $other = $this->makeRestaurant(['ai_features']);
        $burger = $this->item($rid, 'Burger');
        $fries = $this->item($rid, 'Fries');
        $cola = $this->item($rid, 'Cola');
        $this->order($rid, [$burger, $fries], 'R1');
        $this->order($rid, [$burger, $fries, $cola], 'R2');
        $this->order($rid, [$burger], 'R3');
        $this->order($rid, [$burger, $cola], 'R4', null, 10, 'cancelled');
        // foreign tenant has same shaped data and must not leak
        $ob = $this->item($other->id, 'OtherBurger');
        $of = $this->item($other->id, 'OtherSide');
        $this->order($other->id, [$ob, $of], 'X1');

        $json = $this->actingAs($admin)->getJson(route('admin.ai.recommendations', ['item_id' => $burger->id]))->assertOk()->json();
        $this->assertSame('Fries', $json['bought_together'][0]['name']);
        $this->assertSame(2, $json['bought_together'][0]['orders_together']);
        $this->assertEquals(0.67, $json['bought_together'][0]['confidence']);
        $this->assertSame('Cola', $json['bought_together'][1]['name']);
        $this->assertSame(1, $json['bought_together'][1]['orders_together']);
        $this->assertCount(2, $json['bought_together']);

        $this->actingAs($admin)->get(route('admin.ai.index'))->assertOk()->assertSee('Burger + Fries')->assertDontSee('OtherBurger');
        $this->actingAs($admin)->getJson(route('admin.ai.recommendations'))->assertStatus(404);
    }

    public function test_forbidden_without_feature(): void
    {
        $admin = $this->adminWith([]);
        $this->actingAs($admin)->get(route('admin.ai.index'))->assertStatus(403);
        $this->actingAs($admin)->post(route('admin.ai.describe'), ['name' => 'x'])->assertStatus(403);
        $this->actingAs($admin)->getJson(route('admin.ai.recommendations', ['item_id' => 1]))->assertStatus(403);
        $this->actingAs($admin)->put(route('admin.ai.settings'), ['target_margin' => 50])->assertStatus(403);
    }
}
