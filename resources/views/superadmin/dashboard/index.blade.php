@extends('superadmin.layout')
@section('title', 'SaaS Platform Dashboard')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    @php
    $statCards = [
        ['label'=>'Total Restaurants', 'value'=>$stats['total_restaurants'],  'color'=>'indigo',  'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        ['label'=>'Active Tenants',    'value'=>$stats['active_restaurants'], 'color'=>'emerald', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Suspended',         'value'=>$stats['suspended'],          'color'=>'red',     'icon'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        ['label'=>'Total Users',       'value'=>$stats['total_users'],        'color'=>'blue',    'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['label'=>'Platform Revenue',  'value'=>'$'.number_format($stats['total_revenue'],0), 'color'=>'amber', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-500 leading-tight">{{ $card['label'] }}</span>
            <div class="w-8 h-8 rounded-lg bg-{{ $card['color'] }}-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-{{ $card['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $card['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Subscription breakdown + Plans --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Subscription breakdown --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-4">Subscription Breakdown</h3>
        <div class="space-y-3">
            @php
            $breakdown = [
                ['label'=>'Paid',  'value'=>$stats['paid_tenants'],  'color'=>'emerald'],
                ['label'=>'Trial', 'value'=>$stats['trial_tenants'], 'color'=>'blue'],
                ['label'=>'Free',  'value'=>$stats['free_tenants'],  'color'=>'slate'],
            ];
            $total = max($stats['total_restaurants'], 1);
            @endphp
            @foreach($breakdown as $b)
            <div>
                <div class="flex justify-between text-xs text-slate-600 mb-1">
                    <span>{{ $b['label'] }}</span>
                    <span class="font-semibold">{{ $b['value'] }}</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $b['color'] }}-400 rounded-full transition-all" style="width:{{ round(($b['value']/$total)*100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-t">
            <h3 class="text-sm font-bold text-slate-700 mb-3">Plans Overview</h3>
            <div class="space-y-2">
                @foreach($plans as $plan)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">{{ $plan->name }}</span>
                    <span class="font-semibold text-slate-800">{{ $plan->restaurants_count }} restaurants</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Restaurants --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="p-5 border-b flex justify-between items-center">
            <h2 class="text-sm font-bold text-slate-700">Recent Restaurants</h2>
            <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b bg-slate-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Restaurant</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Plan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Users</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recent_restaurants as $r)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-slate-800">{{ $r->name }}</p>
                            <p class="text-xs text-slate-400">{{ $r->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                                {{ $r->plan->name ?? ($r->subscription_plan ?? 'Free') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $r->users_count }}</td>
                        <td class="px-5 py-3">
                            @if($r->is_suspended)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Suspended</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('superadmin.tenants.show', $r) }}" class="text-xs font-medium text-indigo-600 hover:underline">Manage</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">No restaurants yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
