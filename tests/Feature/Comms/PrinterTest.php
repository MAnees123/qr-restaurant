<?php

namespace Tests\Feature\Comms;

use App\Models\Comms\CommsSetting;
use App\Models\Comms\NotificationLog;
use App\Models\OrderItem;
use App\Services\Comms\EscPos;

class PrinterTest extends CommsCase
{
    public function test_settings_saved_and_receipts_render_for_both_widths(): void
    {
        $admin = $this->adminWith(['printer']);
        $r = $admin->restaurant;
        $item = $this->makeItem($r, 'Zinger Burger');
        $order = $this->makeOrder($r, ['notes' => 'extra spicy'], $this->makeTable($r, 'T-7'));
        OrderItem::create(['order_id' => $order->id, 'menu_item_id' => $item->id, 'quantity' => 2, 'unit_price' => 10, 'subtotal' => 20, 'special_instructions' => 'no onion']);

        $this->actingAs($admin)->get(route('admin.printer.index'))->assertOk();
        $this->actingAs($admin)->put(route('admin.printer.update'), [
            'paper_width' => 58, 'header' => 'HEADER-X', 'footer' => 'FOOTER-Y', 'auto_print' => 1,
        ])->assertSessionHas('success');
        $s = CommsSetting::read($r->id, 'printer');
        $this->assertSame(58, $s['paper_width']);
        $this->assertTrue($s['auto_print']);

        $kitchen = $this->actingAs($admin)->get(route('admin.printer.receipt', [$order, 'kitchen']))->assertOk();
        $kitchen->assertSee('KITCHEN TICKET')->assertSee('Zinger Burger')->assertSee('no onion')->assertSee('HEADER-X')->assertSee('size: 58mm auto', false)->assertSee('T-7');
        $kitchen->assertDontSee('TOTAL');

        $bill = $this->actingAs($admin)->get(route('admin.printer.receipt', [$order, 'bill']))->assertOk();
        $bill->assertSee('TOTAL')->assertSee('FOOTER-Y')->assertSee('25.00');

        $this->actingAs($admin)->put(route('admin.printer.update'), ['paper_width' => 80]);
        $this->actingAs($admin)->get(route('admin.printer.receipt', [$order, 'bill']))->assertSee('size: 80mm auto', false);

        $this->actingAs($admin)->get(route('admin.printer.test', ['type' => 'bill']))->assertOk()->assertSee('TEST PRINT')->assertSee('Sample Burger');
        $this->actingAs($admin)->get(route('admin.printer.receipt', [$order, 'bogus']))->assertStatus(404);
    }

    public function test_forbidden_without_feature_and_cross_tenant_orders_hidden(): void
    {
        $a = $this->adminWith(['printer']);
        $b = $this->adminWith(['printer']);
        $order = $this->makeOrder($a->restaurant);

        $this->actingAs($this->adminWith([]))->get(route('admin.printer.index'))->assertStatus(403);
        $this->actingAs($this->adminWith([]))->get(route('admin.printer.receipt', [$order, 'bill']))->assertStatus(403);
        $this->actingAs($b)->get(route('admin.printer.receipt', [$order, 'bill']))->assertStatus(404);
        $this->actingAs($b)->get(route('admin.printer.index'))->assertDontSee($order->order_number);
        $this->actingAs($a)->get(route('admin.printer.index'))->assertSee($order->order_number);
        $this->assertSame([$order->id], $this->actingAs($a)->getJson(route('admin.printer.poll', ['after' => 0]))->json('ids'));
        $this->assertSame([], $this->actingAs($b)->getJson(route('admin.printer.poll', ['after' => 0]))->json('ids'));
    }

    public function test_validation(): void
    {
        $admin = $this->adminWith(['printer']);
        $this->actingAs($admin)->put(route('admin.printer.update'), ['paper_width' => 70])->assertSessionHasErrors('paper_width');
        $this->actingAs($admin)->put(route('admin.printer.update'), ['paper_width' => 80, 'printer_host' => 'bad host;rm', 'printer_port' => 99999])
            ->assertSessionHasErrors(['printer_host', 'printer_port']);
    }

    public function test_network_test_fails_gracefully_on_closed_port(): void
    {
        $admin = $this->adminWith(['printer']);
        $this->actingAs($admin)->post(route('admin.printer.network-test'))->assertSessionHas('error');   // no host configured
        $this->actingAs($admin)->put(route('admin.printer.update'), ['paper_width' => 80, 'printer_host' => '127.0.0.1', 'printer_port' => 1]);
        $res = $this->actingAs($admin)->post(route('admin.printer.network-test'));
        $res->assertSessionHas('error');
        $this->assertStringContainsString('Could not connect', session('error'));
    }

    public function test_escpos_sends_to_listening_socket_and_auto_print_logs_failure(): void
    {
        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        $this->assertNotFalse($server);
        $port = (int) substr(strrchr(stream_socket_get_name($server, false), ':'), 1);

        $r = $this->makeRestaurant(['printer']);
        CommsSetting::write($r->id, 'printer', ['auto_print' => true, 'printer_host' => '127.0.0.1', 'printer_port' => $port]);
        $this->makeOrder($r);
        $conn = @stream_socket_accept($server, 2);
        $this->assertNotFalse($conn);
        $data = stream_get_contents($conn, -1, -1);
        $this->assertStringContainsString('KITCHEN TICKET', $data);
        $this->assertSame('sent', NotificationLog::where('channel', 'printer')->first()->status);
        fclose($conn);
        fclose($server);

        // port now closed => failed status, order flow unaffected
        $this->makeOrder($r);
        $this->assertSame('failed', NotificationLog::where('channel', 'printer')->latest('id')->first()->status);
        $this->assertNotNull(EscPos::send('127.0.0.1', $port, 'x', 1.0));
    }

    public function test_no_auto_print_without_feature(): void
    {
        $r = $this->makeRestaurant([]);
        CommsSetting::write($r->id, 'printer', ['auto_print' => true, 'printer_host' => '127.0.0.1', 'printer_port' => 1]);
        $this->makeOrder($r);
        $this->assertSame(0, NotificationLog::count());
    }
}
