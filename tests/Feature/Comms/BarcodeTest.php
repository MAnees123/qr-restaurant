<?php

namespace Tests\Feature\Comms;

use App\Services\Comms\Barcode;

class BarcodeTest extends CommsCase
{
    public function test_ean13_math_and_known_vector(): void
    {
        $this->assertSame(7, Barcode::ean13CheckDigit('590123412345'));      // 5901234123457 is a standard sample EAN-13 (check digit 7)
        $this->assertTrue(Barcode::isValidEan13('5901234123457'));
        $this->assertFalse(Barcode::isValidEan13('5901234123458'));
        $bits = Barcode::ean13Bits('5901234123457');
        $this->assertSame(95, strlen($bits));
        $this->assertStringStartsWith('101', $bits);
        $this->assertStringEndsWith('101', $bits);
        $this->assertSame('01010', substr($bits, 45, 5));
    }

    public function test_code128_patterns_are_well_formed_and_checksum_known_vector(): void
    {
        $bits = Barcode::code128Bits('Hello');
        // every symbol is 11 modules + 13 for stop => (start + 5 data + check) * 11 + 13
        $this->assertSame(7 * 11 + 13, strlen($bits));
        // known checksum: START B(104) + H(40)*1 + e(69)*2 + l(76)*3 + l(76)*4 + o(79)*5 = 1209 % 103 = 70... computed independently
        $vals = Barcode::code128Values('Hello');
        $expected = (104 + 40 * 1 + 69 * 2 + 76 * 3 + 76 * 4 + 79 * 5) % 103;
        $this->assertSame($expected, $vals[6]);
        $this->assertSame(106, end($vals));
        $this->expectException(\InvalidArgumentException::class);
        Barcode::code128Values("tab\t");
    }

    public function test_svg_output_types(): void
    {
        $this->assertStringContainsString('data-barcode-type="ean13"', Barcode::svg('5901234123457'));
        $this->assertStringContainsString('data-barcode-type="code128"', Barcode::svg('ABC-123'));
    }

    public function test_assign_generate_and_unique_per_restaurant(): void
    {
        $admin = $this->adminWith(['barcode']);
        $other = $this->adminWith(['barcode']);
        $a1 = $this->makeItem($admin->restaurant, 'A1');
        $a2 = $this->makeItem($admin->restaurant, 'A2');
        $b1 = $this->makeItem($other->restaurant, 'B1');

        $this->actingAs($admin)->get(route('admin.barcodes.index'))->assertOk()->assertSee('A1')->assertDontSee('B1');

        $this->actingAs($admin)->put(route('admin.barcodes.update', $a1), ['barcode' => 'SKU-001'])->assertSessionHas('success');
        $this->assertSame('SKU-001', $a1->fresh()->barcode);
        // duplicate within restaurant rejected
        $this->actingAs($admin)->put(route('admin.barcodes.update', $a2), ['barcode' => 'SKU-001'])->assertSessionHasErrors('barcode');
        // same code allowed in another restaurant
        $this->actingAs($other)->put(route('admin.barcodes.update', $b1), ['barcode' => 'SKU-001'])->assertSessionHas('success');
        // bad EAN check digit
        $this->actingAs($admin)->put(route('admin.barcodes.update', $a2), ['barcode' => '5901234123458'])->assertSessionHasErrors('barcode');
        $this->actingAs($admin)->put(route('admin.barcodes.update', $a2), ['barcode' => '5901234123457'])->assertSessionHas('success');
        // clear
        $this->actingAs($admin)->put(route('admin.barcodes.update', $a2), ['barcode' => ''])->assertSessionHas('success');
        $this->assertNull($a2->fresh()->barcode);

        $this->actingAs($admin)->post(route('admin.barcodes.generate', $a2))->assertSessionHas('success');
        $code = $a2->fresh()->barcode;
        $this->assertTrue(Barcode::isValidEan13($code));
    }

    public function test_svg_labels_and_lookup(): void
    {
        $admin = $this->adminWith(['barcode']);
        $item = $this->makeItem($admin->restaurant, 'Pizza', ['barcode' => 'PZ-77']);
        $this->makeItem($admin->restaurant, 'NoCode');

        $this->actingAs($admin)->get(route('admin.barcodes.svg', $item))->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
        $this->actingAs($admin)->get(route('admin.barcodes.labels'))->assertOk()->assertSee('Pizza')->assertDontSee('NoCode')->assertSee('<svg', false);
        $this->actingAs($admin)->get(route('admin.barcodes.scan'))->assertOk();

        $this->actingAs($admin)->getJson(route('admin.barcodes.lookup', ['code' => 'PZ-77']))
            ->assertOk()->assertJson(['found' => true, 'item' => ['id' => $item->id, 'name' => 'Pizza']]);
        $this->actingAs($admin)->getJson(route('admin.barcodes.lookup', ['code' => 'NOPE']))->assertStatus(404)->assertJson(['found' => false]);
        $this->actingAs($admin)->getJson(route('admin.barcodes.lookup'))->assertStatus(422);
    }

    public function test_forbidden_without_feature_and_cross_tenant(): void
    {
        $a = $this->adminWith(['barcode']);
        $b = $this->adminWith(['barcode']);
        $item = $this->makeItem($a->restaurant, 'Secret', ['barcode' => 'SECRET-1']);
        $no = $this->adminWith([]);

        $this->actingAs($no)->get(route('admin.barcodes.index'))->assertStatus(403);
        $this->actingAs($no)->getJson(route('admin.barcodes.lookup', ['code' => 'SECRET-1']))->assertStatus(403);
        $this->actingAs($no)->put(route('admin.barcodes.update', $item), ['barcode' => 'X'])->assertStatus(403);

        $this->actingAs($b)->put(route('admin.barcodes.update', $item), ['barcode' => 'HACK'])->assertStatus(404);
        $this->actingAs($b)->post(route('admin.barcodes.generate', $item))->assertStatus(404);
        $this->actingAs($b)->get(route('admin.barcodes.svg', $item))->assertStatus(404);
        $this->actingAs($b)->getJson(route('admin.barcodes.lookup', ['code' => 'SECRET-1']))->assertStatus(404);
        $this->actingAs($b)->get(route('admin.barcodes.labels'))->assertDontSee('Secret');
        $this->assertSame('SECRET-1', $item->fresh()->barcode);
    }
}
