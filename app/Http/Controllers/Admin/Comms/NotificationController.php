<?php

namespace App\Http\Controllers\Admin\Comms;

use App\Http\Controllers\Controller;
use App\Models\Comms\CommsSetting;
use App\Models\Comms\NotificationLog;
use App\Services\Comms\Notifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    private function channel(Request $request): string
    {
        $channel = $request->route('channel');
        abort_unless(isset(Notifier::CHANNELS[$channel]), 404);
        return $channel;
    }

    public function index(Request $request)
    {
        $channel = $this->channel($request);
        $rid = auth()->user()->restaurant_id;
        $settings = CommsSetting::read($rid, $channel);
        $settings['has_token'] = !empty($settings['token']);
        unset($settings['token']);
        $logs = NotificationLog::where('restaurant_id', $rid)->where('channel', $channel)->latest('id')->paginate(15);

        return view('admin.comms.notifications', [
            'channel' => $channel, 'settings' => $settings, 'logs' => $logs,
            'events' => Notifier::EVENTS, 'defaults' => Notifier::DEFAULT_TEMPLATES,
        ]);
    }

    public function update(Request $request)
    {
        $channel = $this->channel($request);
        $drivers = $channel === 'email' ? ['log', 'mail'] : ['log', 'http'];

        $data = $request->validate([
            'driver'         => ['required', Rule::in($drivers)],
            'provider_url'   => [Rule::requiredIf($request->input('driver') === 'http'), 'nullable', 'url', 'starts_with:http://,https://', 'max:500'],
            'token'          => 'nullable|string|max:500',
            'sender'         => 'nullable|string|max:100',
            'from_name'      => 'nullable|string|max:100',
            'subject'        => 'nullable|string|max:200',
            'events'         => 'nullable|array',
            'events.*'       => Rule::in(array_keys(Notifier::EVENTS)),
            'templates'      => 'nullable|array',
            'templates.*'    => 'nullable|string|max:1000',
        ]);

        $values = [
            'enabled'   => $request->boolean('enabled'),
            'driver'    => $data['driver'],
            'events'    => array_values($data['events'] ?? []),
            'templates' => array_filter($data['templates'] ?? [], fn ($t) => trim((string) $t) !== ''),
        ];
        if ($channel === 'email') {
            $values['from_name'] = $data['from_name'] ?? '';
            $values['subject'] = $data['subject'] ?? '';
        } else {
            $values['provider_url'] = $data['provider_url'] ?? '';
            $values['sender'] = $data['sender'] ?? '';
            if (!empty($data['token'])) {          // blank keeps the stored token
                $values['token'] = $data['token'];
            }
            if ($request->boolean('clear_token')) {
                $values['token'] = '';
            }
        }
        CommsSetting::write(auth()->user()->restaurant_id, $channel, $values);

        return redirect()->route("admin.comms.$channel.index")->with('success', ucfirst($channel) . ' notification settings saved.');
    }

    public function test(Request $request, Notifier $notifier)
    {
        $channel = $this->channel($request);
        $request->validate([
            'recipient' => $channel === 'email' ? 'required|email|max:190' : ['required', 'string', 'max:40', 'regex:/^\+?[0-9 \-()]{5,}$/'],
        ]);
        $log = $notifier->sendTest(auth()->user()->restaurant, $channel, $request->recipient);

        $flash = $log->status === 'failed' ? 'error' : 'success';
        return redirect()->route("admin.comms.$channel.index")
            ->with($flash, "Test message status: {$log->status}. " . $log->response);
    }
}
