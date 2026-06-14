@extends('superadmin.layout')

@section('title', 'Tenant Details — ' . $restaurant->name)

@section('content')
<div class="mb-6 flex flex-wrap gap-4 items-center justify-between">
    <a href="{{ route('superadmin.tenants.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Tenants
    </a>
    
    <div class="flex items-center gap-3">
        <!-- Impersonate -->
        <form action="{{ route('superadmin.tenants.impersonate', $restaurant) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-200 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Login as Restaurant Admin
            </button>
        </form>

        <a href="{{ route('superadmin.tenants.edit', $restaurant) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
            Edit Tenant Settings
        </a>

        <!-- Suspension -->
        <form action="{{ route('superadmin.tenants.toggle-suspension', $restaurant) }}" method="POST">
            @csrf
            @if($restaurant->is_suspended)
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition">
                    Activate Tenant
                </button>
            @else
                <button type="submit" onclick="return confirm('Suspend this tenant?')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition">
                    Suspend Tenant
                </button>
            @endif
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Tenant Identity & Password Reset -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center">
            @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" class="w-24 h-24 rounded-2xl mx-auto mb-4 object-cover border">
            @else
                <div class="w-24 h-24 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mx-auto mb-4 font-bold text-3xl">
                    {{ substr($restaurant->name, 0, 2) }}
                </div>
            @endif
            <h2 class="text-xl font-bold text-slate-800">{{ $restaurant->name }}</h2>
            <p class="text-sm text-slate-500 mb-4">{{ $restaurant->cuisine_type ?? 'Restaurant' }}</p>
            
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $restaurant->is_suspended ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                {{ $restaurant->is_suspended ? 'Suspended' : 'Active' }}
            </div>
        </div>
        
        <!-- Subscription Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-3">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2 border-b pb-2">Subscription Details</h3>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Plan</span>
                <span class="font-medium text-slate-800 capitalize">{{ $restaurant->plan->name ?? ($restaurant->subscription_plan ?? 'Free') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Cycle</span>
                <span class="font-medium text-slate-800 capitalize">{{ $restaurant->billing_cycle ?? 'Monthly' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Payment Status</span>
                <span class="font-medium text-slate-800 capitalize">{{ $restaurant->payment_status ?? 'Unpaid' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Expiry Date</span>
                <span class="font-medium text-slate-800">
                    @if($restaurant->billing_cycle === 'lifetime')
                        Never (Lifetime)
                    @else
                        {{ $restaurant->subscription_ends_at ? $restaurant->subscription_ends_at->format('M d, Y') : 'N/A' }}
                    @endif
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Joined Date</span>
                <span class="font-medium text-slate-800">{{ $restaurant->created_at ? $restaurant->created_at->format('M d, Y') : '—' }}</span>
            </div>
        </div>

        <!-- Limits -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-3">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2 border-b pb-2">Plan Limits</h3>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Max Branches</span>
                <span class="font-medium text-slate-800">{{ $restaurant->max_branches }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Max Staff/Users</span>
                <span class="font-medium text-slate-800">{{ $restaurant->max_users }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Max Tables</span>
                <span class="font-medium text-slate-800">{{ $restaurant->max_tables }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Max Storage (MB)</span>
                <span class="font-medium text-slate-800">{{ $restaurant->max_storage_mb ?? 100 }} MB</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Theme</span>
                <span class="font-medium text-slate-800 capitalize">{{ $restaurant->theme ?? 'Default' }}</span>
            </div>
        </div>

        <!-- Password Reset Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 border-b pb-2">Reset Owner Password</h3>
            <form action="{{ route('superadmin.tenants.reset-password', $restaurant) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">New Password</label>
                    <input type="password" name="password" required placeholder="Minimum 8 characters"
                           class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Re-enter password"
                           class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full py-2 bg-slate-850 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition">
                    Reset Owner Password
                </button>
            </form>
        </div>
    </div>
    
    <!-- Right Column: Stats, Features, Billing Invoices & Activity Logs -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 text-center">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Users</p>
                <p class="text-2xl font-bold text-slate-800">{{ $restaurant->users_count }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 text-center">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Tables</p>
                <p class="text-2xl font-bold text-slate-800">{{ $restaurant->tables_count }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 text-center">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Menu Items</p>
                <p class="text-2xl font-bold text-slate-800">{{ $restaurant->menu_items_count }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 text-center">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Orders</p>
                <p class="text-2xl font-bold text-slate-800">{{ $restaurant->orders_count }}</p>
            </div>
        </div>
        
        <!-- Enabled Features / Modules -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-3 mb-4">Enabled Features & Modules</h3>
            <div class="flex flex-wrap gap-2">
                @forelse($restaurant->granted_features ?? [] as $code)
                    @php
                        $label = collect($features)->flatten(1)->firstWhere('code', $code)['label'] ?? $code;
                    @endphp
                    <span class="px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg">
                        {{ $label }}
                    </span>
                @empty
                    <span class="text-sm text-slate-400">No premium modules enabled.</span>
                @endforelse
            </div>
        </div>

        <!-- Billing & Invoices -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Invoices & Billing History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3">Invoice #</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Cycle</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-600">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-semibold text-slate-800">{{ $invoice->invoice_number }}</td>
                                <td class="px-5 py-3">${{ number_format($invoice->amount, 2) }}</td>
                                <td class="px-5 py-3 capitalize">{{ $invoice->billing_cycle }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $invoice->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($invoice->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">{{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y H:i') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">No invoice history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tenant Users list -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tenant Users</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-600">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                                <td class="px-5 py-3">{{ $user->email }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 capitalize">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-400 text-xs">{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Recent Activity Logs</h3>
            </div>
            <div class="p-5 space-y-4">
                @forelse($logs as $log)
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-slate-850 font-semibold leading-tight">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </p>
                            <p class="text-slate-500 text-xs">{{ $log->description }}</p>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                <span>{{ $log->created_at ? $log->created_at->format('M d, Y H:i') : '—' }}</span>
                                <span>•</span>
                                <span>IP: {{ $log->ip_address }}</span>
                                @if($log->user)
                                    <span>•</span>
                                    <span>By: {{ $log->user->name }} ({{ $log->user->email }})</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-4">No recent activity logs.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
