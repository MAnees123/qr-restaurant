<?php

namespace Tests\Feature\Comms;

use App\Models\Comms\CommsSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandingDarkModeTest extends CommsCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private function png(string $name = 'logo.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(self::PNG));
    }

    public function test_branding_saved_with_uploads(): void
    {
        Storage::fake('public');
        $admin = $this->adminWith(['custom_branding']);
        $this->actingAs($admin)->get(route('admin.branding.edit'))->assertOk();
        $this->actingAs($admin)->put(route('admin.branding.update'), [
            'primary' => '#112233', 'secondary' => '#445566', 'tagline' => 'Best in town', 'logo' => $this->png(),
        ])->assertSessionHas('success');
        $s = CommsSetting::read($admin->restaurant_id, 'branding');
        $this->assertSame('#112233', $s['primary']);
        $this->assertSame('Best in town', $s['tagline']);
        Storage::disk('public')->assertExists($s['logo']);

        $this->actingAs($admin)->put(route('admin.branding.update'), ['primary' => '#112233', 'secondary' => '#445566', 'remove_logo' => 1]);
        $this->assertNull(CommsSetting::read($admin->restaurant_id, 'branding')['logo']);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_branding_forbidden_validation_and_isolation(): void
    {
        $no = $this->adminWith([]);
        $this->actingAs($no)->get(route('admin.branding.edit'))->assertStatus(403);
        $this->actingAs($no)->put(route('admin.branding.update'), ['primary' => '#112233', 'secondary' => '#445566'])->assertStatus(403);

        $a = $this->adminWith(['custom_branding']);
        $b = $this->adminWith(['custom_branding']);
        $this->actingAs($a)->put(route('admin.branding.update'), ['primary' => 'red', 'secondary' => '#445566'])->assertSessionHasErrors('primary');
        $this->actingAs($a)->put(route('admin.branding.update'), ['primary' => '#112233', 'secondary' => '#445566', 'logo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])->assertSessionHasErrors('logo');

        $this->actingAs($a)->put(route('admin.branding.update'), ['primary' => '#112233', 'secondary' => '#445566', 'tagline' => 'Only A']);
        $this->actingAs($b)->get(route('admin.branding.edit'))->assertDontSee('Only A');
        $this->assertSame('#e8890c', CommsSetting::read($b->restaurant_id, 'branding')['primary']);
    }

    public function test_guest_menu_shows_branding_only_when_granted(): void
    {
        $on = $this->makeRestaurant(['custom_branding']);
        $off = $this->makeRestaurant([]);
        foreach ([$on, $off] as $r) {
            CommsSetting::write($r->id, 'branding', ['primary' => '#123abc', 'tagline' => 'TAGLINE-' . $r->id]);
        }
        $tOn = $this->makeTable($on);
        $tOff = $this->makeTable($off);

        $this->get(route('menu.show', $tOn->secure_token))->assertOk()
            ->assertSee('TAGLINE-' . $on->id)->assertSee('--brand-primary: #123abc', false);
        $this->get(route('menu.show', $tOff->secure_token))->assertOk()
            ->assertDontSee('TAGLINE-' . $off->id)->assertDontSee('--brand-primary', false);
    }

    public function test_admin_pages_receive_branding_css_when_granted(): void
    {
        $admin = $this->adminWith(['custom_branding']);
        CommsSetting::write($admin->restaurant_id, 'branding', ['primary' => '#0a0b0c']);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('--brand-primary: #0a0b0c', false);
        $plain = $this->adminWith([]);
        $this->actingAs($plain)->get(route('admin.dashboard'))->assertDontSee('--brand-primary', false);
    }

    public function test_dark_mode_toggle_persists_per_user_and_is_feature_gated(): void
    {
        $admin = $this->adminWith(['dark_mode']);
        $other = $this->makeUser($admin->restaurant, 'admin');

        $this->actingAs($admin)->postJson(route('admin.dark-mode.toggle'))->assertOk()->assertJson(['dark_mode' => true]);
        $this->assertTrue((bool) $admin->fresh()->dark_mode);
        $this->assertFalse((bool) $other->fresh()->dark_mode);
        $this->actingAs($admin)->postJson(route('admin.dark-mode.toggle'), ['dark_mode' => false])->assertJson(['dark_mode' => false]);
        $this->assertFalse((bool) $admin->fresh()->dark_mode);

        $this->actingAs($admin)->postJson(route('admin.dark-mode.toggle'), ['dark_mode' => true]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSee('comms-dark-toggle', false)->assertSee('var on = true', false);

        $no = $this->adminWith([]);
        $this->actingAs($no)->postJson(route('admin.dark-mode.toggle'))->assertStatus(403);
        $this->assertFalse((bool) $no->fresh()->dark_mode);
        $this->actingAs($no)->get(route('admin.dashboard'))->assertDontSee('comms-dark-toggle', false);
    }

    public function test_dark_mode_validation(): void
    {
        $admin = $this->adminWith(['dark_mode']);
        // non-boolean input is treated as boolean false rather than crashing
        $this->actingAs($admin)->postJson(route('admin.dark-mode.toggle'), ['dark_mode' => 'garbage'])->assertOk()->assertJson(['dark_mode' => false]);
    }

    public function test_guest_menu_gets_dark_mode_script_only_when_granted(): void
    {
        $on = $this->makeRestaurant(['dark_mode']);
        $off = $this->makeRestaurant([]);
        $this->get(route('menu.show', $this->makeTable($on)->secure_token))->assertSee('comms_guest_dark', false);
        $this->get(route('menu.show', $this->makeTable($off)->secure_token))->assertDontSee('comms_guest_dark', false);
    }
}
