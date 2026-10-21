<?php

namespace Tests\Feature\Comms;

use App\Models\Comms\CommsSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class QrLogoTest extends CommsCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    public function test_settings_saved_and_svg_has_logo_overlay(): void
    {
        Storage::fake('public');
        $admin = $this->adminWith(['qr_logo']);
        $table = $this->makeTable($admin->restaurant);

        $this->actingAs($admin)->get(route('admin.qr-logo.edit'))->assertOk();
        $this->actingAs($admin)->put(route('admin.qr-logo.update'), [
            'enabled' => 1, 'source' => 'custom', 'size_percent' => 25, 'background' => '#ffffff', 'foreground' => '#112233', 'rounded' => 1,
            'logo' => UploadedFile::fake()->createWithContent('l.png', base64_decode(self::PNG)),
        ])->assertSessionHas('success');
        $s = CommsSetting::read($admin->restaurant_id, 'qr_logo');
        $this->assertSame(25, $s['size_percent']);
        Storage::disk('public')->assertExists($s['logo']);

        $res = $this->actingAs($admin)->get(route('admin.qr-logo.svg', $table));
        $res->assertOk();
        $this->assertStringContainsString('image/svg+xml', $res->headers->get('Content-Type'));
        $this->assertStringContainsString('data-qr-logo="1"', $res->getContent());
        $this->assertStringContainsString('data:image/png;base64,', $res->getContent());

        // disabled => plain QR without overlay
        $this->actingAs($admin)->put(route('admin.qr-logo.update'), ['source' => 'custom', 'size_percent' => 25, 'background' => '#ffffff', 'foreground' => '#112233']);
        $this->assertStringNotContainsString('data-qr-logo', $this->actingAs($admin)->get(route('admin.qr-logo.svg', $table))->getContent());

        $this->actingAs($admin)->get(route('admin.qr-logo.svg', [$table, 'download' => 1]))->assertHeader('Content-Disposition');
    }

    public function test_forbidden_validation_and_cross_tenant(): void
    {
        $this->actingAs($this->adminWith([]))->get(route('admin.qr-logo.edit'))->assertStatus(403);

        $a = $this->adminWith(['qr_logo']);
        $b = $this->adminWith(['qr_logo']);
        $tableA = $this->makeTable($a->restaurant);
        $this->actingAs($b)->get(route('admin.qr-logo.svg', $tableA))->assertStatus(404);
        $this->actingAs($this->adminWith([]))->get(route('admin.qr-logo.svg', $tableA))->assertStatus(403);

        $ok = ['source' => 'restaurant', 'size_percent' => 20, 'background' => '#ffffff', 'foreground' => '#000000'];
        $this->actingAs($a)->put(route('admin.qr-logo.update'), array_merge($ok, ['size_percent' => 80]))->assertSessionHasErrors('size_percent');
        $this->actingAs($a)->put(route('admin.qr-logo.update'), array_merge($ok, ['source' => 'weird']))->assertSessionHasErrors('source');
        $this->actingAs($a)->put(route('admin.qr-logo.update'), array_merge($ok, ['foreground' => 'black']))->assertSessionHasErrors('foreground');
    }

    public function test_b_does_not_get_a_logo_in_preview(): void
    {
        Storage::fake('public');
        $a = $this->adminWith(['qr_logo']);
        $b = $this->adminWith(['qr_logo']);
        CommsSetting::write($a->restaurant_id, 'qr_logo', ['source' => 'custom', 'logo' => 'x/a.png']);
        Storage::disk('public')->put('x/a.png', base64_decode(self::PNG));
        $tb = $this->makeTable($b->restaurant);
        $this->assertStringNotContainsString('data-qr-logo', $this->actingAs($b)->get(route('admin.qr-logo.svg', $tb))->getContent());
        $ta = $this->makeTable($a->restaurant);
        $this->assertStringContainsString('data-qr-logo', $this->actingAs($a)->get(route('admin.qr-logo.svg', $ta))->getContent());
    }
}
