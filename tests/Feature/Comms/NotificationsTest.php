<?php

namespace Tests\Feature\Comms;

use App\Models\Comms\CommsSetting;
use App\Models\Comms\NotificationLog;
use App\Models\Reservation;
use Illuminate\Support\Facades\Http;

class NotificationsTest extends CommsCase
{
    private const CH = ['sms' => 'sms_notif', 'whatsapp' => 'whatsapp_notif'];

    public function test_settings_can_be_saved_and_token_is_encrypted_for_sms_and_whatsapp(): void
    {
        foreach (self::CH as $channel => $feature) {
            $admin = $this->adminWith([$feature]);
            $this->actingAs($admin)->get(route("admin.comms.$channel.index"))->assertOk();

            $this->actingAs($admin)->put(route("admin.comms.$channel.update"), [
                'enabled' => 1, 'driver' => 'http', 'provider_url' => 'https://gw.example.com/send', 'token' => 'sekret',
                'sender' => 'RESTO', 'events' => ['order_placed', 'order_status_changed'],
                'templates' => ['order_placed' => 'Order {order_number} thanks'],
            ])->assertRedirect(route("admin.comms.$channel.index"))->assertSessionHas('success');

            $raw = CommsSetting::where('restaurant_id', $admin->restaurant_id)->where('section', $channel)->first()->data;
            $this->assertTrue($raw['enabled']);
            $this->assertNotSame('sekret', $raw['token']);
            $read = CommsSetting::read($admin->restaurant_id, $channel);
            $this->assertSame('sekret', $read['token']);
            $this->assertSame(['order_placed', 'order_status_changed'], $read['events']);

            // blank token keeps the existing one
            $this->actingAs($admin)->put(route("admin.comms.$channel.update"), [
                'driver' => 'http', 'provider_url' => 'https://gw.example.com/send', 'events' => ['order_placed'],
            ]);
            $this->assertSame('sekret', CommsSetting::read($admin->restaurant_id, $channel)['token']);
            $this->assertFalse(CommsSetting::read($admin->restaurant_id, $channel)['enabled']);
        }
    }

    public function test_email_settings_save(): void
    {
        $admin = $this->adminWith(['email_notif']);
        $this->actingAs($admin)->put(route('admin.comms.email.update'), [
            'enabled' => 1, 'driver' => 'mail', 'from_name' => 'Resto', 'subject' => 'Hi {order_number}', 'events' => ['order_placed'],
        ])->assertSessionHas('success');
        $s = CommsSetting::read($admin->restaurant_id, 'email');
        $this->assertSame('mail', $s['driver']);
        $this->assertSame('Hi {order_number}', $s['subject']);
    }

    public function test_pages_forbidden_without_feature(): void
    {
        foreach (['sms', 'whatsapp', 'email'] as $channel) {
            $admin = $this->adminWith(['reports']);
            $this->actingAs($admin)->get(route("admin.comms.$channel.index"))->assertStatus(403);
            $this->actingAs($admin)->put(route("admin.comms.$channel.update"), ['driver' => 'log'])->assertStatus(403);
            $this->actingAs($admin)->post(route("admin.comms.$channel.test"), ['recipient' => 'a@b.com'])->assertStatus(403);
        }
        // the sms feature does not unlock whatsapp
        $this->actingAs($this->adminWith(['sms_notif']))->get(route('admin.comms.whatsapp.index'))->assertStatus(403);
    }

    public function test_validation_failures(): void
    {
        $admin = $this->adminWith(['sms_notif', 'email_notif']);
        $this->actingAs($admin)->put(route('admin.comms.sms.update'), ['driver' => 'http'])->assertSessionHasErrors('provider_url');
        $this->actingAs($admin)->put(route('admin.comms.sms.update'), ['driver' => 'mail'])->assertSessionHasErrors('driver');
        $this->actingAs($admin)->put(route('admin.comms.sms.update'), ['driver' => 'http', 'provider_url' => 'ftp://x.test'])->assertSessionHasErrors('provider_url');
        $this->actingAs($admin)->put(route('admin.comms.sms.update'), ['driver' => 'log', 'events' => ['nonsense']])->assertSessionHasErrors('events.0');
        $this->actingAs($admin)->post(route('admin.comms.email.test'), ['recipient' => 'not-an-email'])->assertSessionHasErrors('recipient');
        $this->actingAs($admin)->post(route('admin.comms.sms.test'), [])->assertSessionHasErrors('recipient');
    }

    public function test_other_restaurant_cannot_see_settings_or_logs(): void
    {
        $a = $this->adminWith(['sms_notif']);
        $b = $this->adminWith(['sms_notif']);
        CommsSetting::write($a->restaurant_id, 'sms', ['enabled' => true, 'sender' => 'ONLY-A', 'driver' => 'log']);
        NotificationLog::create(['restaurant_id' => $a->restaurant_id, 'channel' => 'sms', 'event' => 'order_placed', 'status' => 'logged', 'message' => 'SECRET-A-MSG']);

        $this->actingAs($a)->get(route('admin.comms.sms.index'))->assertSee('SECRET-A-MSG');
        $this->actingAs($b)->get(route('admin.comms.sms.index'))->assertDontSee('SECRET-A-MSG')->assertDontSee('ONLY-A');

        $this->actingAs($b)->put(route('admin.comms.sms.update'), ['driver' => 'log', 'sender' => 'B-SENDER']);
        $this->assertSame('ONLY-A', CommsSetting::read($a->restaurant_id, 'sms')['sender']);
    }

    public function test_http_driver_posts_to_provider_and_marks_sent_or_failed(): void
    {
        Http::fake([
            'gw.example.com/ok' => Http::response(['id' => 1], 200),
            'gw.example.com/bad' => Http::response('nope', 500),
        ]);
        $r = $this->makeRestaurant(['sms_notif']);
        $table = $this->makeTable($r, 'T-9');
        CommsSetting::write($r->id, 'sms', [
            'enabled' => true, 'driver' => 'http', 'provider_url' => 'https://gw.example.com/ok', 'token' => 'tok123',
            'sender' => 'RESTO', 'events' => ['order_placed', 'order_status_changed'],
            'templates' => ['order_placed' => 'Order {order_number} total {total} at {restaurant} table {table}'],
        ]);

        $order = $this->makeOrder($r, ['customer_phone' => '+923001112222'], $table);

        $log = NotificationLog::where('restaurant_id', $r->id)->first();
        $this->assertSame('sent', $log->status);
        $this->assertSame('+923001112222', $log->recipient);
        $this->assertStringContainsString($order->order_number, $log->message);
        $this->assertStringContainsString('table T-9', $log->message);
        Http::assertSent(fn ($req) => $req->url() === 'https://gw.example.com/ok'
            && $req->hasHeader('Authorization', 'Bearer tok123')
            && $req['to'] === '+923001112222' && $req['from'] === 'RESTO' && str_contains($req['message'], $order->order_number));

        // status change uses the default template; failing provider => failed
        CommsSetting::write($r->id, 'sms', ['provider_url' => 'https://gw.example.com/bad']);
        $order->update(['status' => 'preparing']);
        $failed = NotificationLog::where('event', 'order_status_changed')->first();
        $this->assertSame('failed', $failed->status);
        $this->assertStringContainsString('500', $failed->response);
        $this->assertStringContainsString('Preparing', $failed->message);
    }

    public function test_http_driver_connection_error_is_recorded_as_failed(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('timeout'));
        $r = $this->makeRestaurant(['whatsapp_notif']);
        CommsSetting::write($r->id, 'whatsapp', ['enabled' => true, 'driver' => 'http', 'provider_url' => 'https://gw.example.com/x', 'events' => ['order_placed']]);
        $this->makeOrder($r, ['customer_phone' => '+1555']);
        $this->assertSame('failed', NotificationLog::where('channel', 'whatsapp')->first()->status);
    }

    public function test_log_driver_records_logged_and_skips_without_recipient(): void
    {
        Http::fake();
        $r = $this->makeRestaurant(['sms_notif']);
        CommsSetting::write($r->id, 'sms', ['enabled' => true, 'driver' => 'log', 'events' => ['order_placed']]);
        $this->makeOrder($r, ['customer_phone' => '+1555']);
        $this->makeOrder($r);
        $this->assertSame(['logged', 'skipped'], NotificationLog::orderBy('id')->pluck('status')->all());
        Http::assertNothingSent();
    }

    public function test_nothing_fires_when_feature_not_granted_or_event_not_selected(): void
    {
        Http::fake();
        $off = $this->makeRestaurant([]);
        CommsSetting::write($off->id, 'sms', ['enabled' => true, 'driver' => 'http', 'provider_url' => 'https://gw.example.com/ok', 'events' => ['order_placed']]);
        $this->makeOrder($off, ['customer_phone' => '+1555']);

        $on = $this->makeRestaurant(['sms_notif']);
        CommsSetting::write($on->id, 'sms', ['enabled' => true, 'driver' => 'log', 'events' => ['order_status_changed']]);
        $this->makeOrder($on, ['customer_phone' => '+1555']);

        $this->assertSame(0, NotificationLog::count());
        Http::assertNothingSent();
    }

    public function test_email_mail_driver_sends_through_laravel_mailer(): void
    {
        config(['mail.default' => 'array']);
        $r = $this->makeRestaurant(['email_notif']);
        CommsSetting::write($r->id, 'email', ['enabled' => true, 'driver' => 'mail', 'subject' => 'Order {order_number}', 'events' => ['order_placed']]);
        $order = $this->makeOrder($r, ['customer_email' => 'guest@example.com']);

        $log = NotificationLog::where('channel', 'email')->first();
        $this->assertSame('sent', $log->status);
        $messages = app('mail.manager')->mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $sent = $messages[0]->getOriginalMessage();
        $this->assertSame('guest@example.com', $sent->getTo()[0]->getAddress());
        $this->assertSame('Order ' . $order->order_number, $sent->getSubject());
    }

    public function test_reservation_confirmed_notification(): void
    {
        $r = $this->makeRestaurant(['sms_notif']);
        CommsSetting::write($r->id, 'sms', ['enabled' => true, 'driver' => 'log', 'events' => ['reservation_confirmed']]);
        $res = Reservation::create(['restaurant_id' => $r->id, 'customer_name' => 'Ali', 'customer_phone' => '+92300',
            'reservation_date' => '2026-12-01', 'reservation_time' => '19:00', 'guests' => 2, 'status' => 'pending']);
        $this->assertSame(0, NotificationLog::count());
        $res->update(['status' => 'confirmed']);
        $log = NotificationLog::first();
        $this->assertSame('reservation_confirmed', $log->event);
        $this->assertStringContainsString('Ali', $log->message);
    }

    public function test_test_message_endpoint_logs_result_for_own_restaurant(): void
    {
        Http::fake(['gw.example.com/*' => Http::response('ok', 200)]);
        $admin = $this->adminWith(['sms_notif']);
        CommsSetting::write($admin->restaurant_id, 'sms', ['driver' => 'http', 'provider_url' => 'https://gw.example.com/send']);
        $this->actingAs($admin)->post(route('admin.comms.sms.test'), ['recipient' => '+923001234567'])->assertSessionHas('success');
        $this->assertSame('sent', NotificationLog::where('restaurant_id', $admin->restaurant_id)->first()->status);
    }
}
