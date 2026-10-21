<?php

namespace Tests\Feature\Platform;

use App\Models\Platform\PwaSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenants;
use Tests\TestCase;

class MobileAppTest extends TestCase
{
    use RefreshDatabase, CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_manifest_service_worker_offline_and_install_page_when_granted(): void
    {
        $r = $this->makeRestaurant(['mobile_app'], ['name' => 'Pasta Place']);
        PwaSetting::create(['restaurant_id' => $r->id, 'short_name' => 'Pasta', 'theme_color' => '#112233', 'background_color' => '#ffffff']);

        $m = $this->get(route('pwa.manifest', $r->id))->assertOk();
        $m->assertJsonPath('name', 'Pasta Place')->assertJsonPath('short_name', 'Pasta')->assertJsonPath('theme_color', '#112233');
        $this->assertStringContainsString('manifest+json', $m->headers->get('Content-Type'));

        $sw = $this->get(route('pwa.sw', $r->id))->assertOk();
        $this->assertStringContainsString('javascript', $sw->headers->get('Content-Type'));
        $this->assertSame('/', $sw->headers->get('Service-Worker-Allowed'));
        $this->assertStringContainsString("/pwa/{$r->id}/offline", $sw->getContent());

        $this->get(route('pwa.offline', $r->id))->assertOk()->assertSee('You are offline');
        $this->get(route('pwa.icon', $r->id))->assertOk()->assertSee('<svg', false);
        $this->get(route('pwa.install', $r->id))->assertOk()->assertSee('Pasta Place');
    }

    public function test_public_pwa_routes_404_without_feature(): void
    {
        $r = $this->makeRestaurant(['discounts']);
        foreach (['pwa.manifest', 'pwa.sw', 'pwa.offline', 'pwa.icon', 'pwa.install'] as $name) {
            $this->get(route($name, $r->id))->assertStatus(404);
        }
        $this->get('/pwa/99999/manifest.webmanifest')->assertStatus(404);
    }

    public function test_head_partial_renders_only_with_feature(): void
    {
        $on = $this->makeRestaurant(['mobile_app']);
        $off = $this->makeRestaurant([]);
        $this->assertStringContainsString('rel="manifest"', view('partials.pwa-head', ['restaurant' => $on])->render());
        $this->assertStringNotContainsString('manifest', view('partials.pwa-head', ['restaurant' => $off])->render());
    }

    public function test_admin_settings_saved_scoped_and_validated(): void
    {
        $admin = $this->adminWith(['mobile_app']);
        $other = $this->makeRestaurant(['mobile_app']);
        $this->actingAs($admin)->get(route('admin.pwa.index'))->assertOk();

        $this->actingAs($admin)->put(route('admin.pwa.update'), ['short_name' => 'Hi', 'theme_color' => '#abcdef', 'background_color' => '#000000'])
            ->assertRedirect(route('admin.pwa.index'));
        $this->assertDatabaseHas('pwa_settings', ['restaurant_id' => $admin->restaurant_id, 'theme_color' => '#abcdef']);
        $this->assertDatabaseMissing('pwa_settings', ['restaurant_id' => $other->id]);

        $this->actingAs($admin)->put(route('admin.pwa.update'), ['theme_color' => 'red', 'background_color' => ''])
            ->assertSessionHasErrors(['theme_color', 'background_color']);
    }

    public function test_admin_screen_forbidden_without_feature(): void
    {
        $admin = $this->adminWith([]);
        $this->actingAs($admin)->get(route('admin.pwa.index'))->assertStatus(403);
        $this->actingAs($admin)->put(route('admin.pwa.update'), ['theme_color' => '#abcdef', 'background_color' => '#000000'])->assertStatus(403);
    }
}
