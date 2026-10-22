@extends('layouts.admin')

@section('header', 'Manage Discount Coupons')

@section('content')
<div class="mb-6 flex flex-wrap gap-4 justify-between items-center">
    <p class="text-slate-500">Create and manage discount codes for your customers.</p>
    <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        New Coupon
    </a>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Coupon Code</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Type</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Value</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Usage</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Expires</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($discounts as $discount)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-6 py-4">
                    <span class="badge badge-warning font-semibold tracking-wide">{{ $discount->code }}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-slate-600 capitalize">{{ $discount->type }}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm font-semibold text-slate-800">
                        {{ $discount->type === 'percentage' ? $discount->value . '%' : 'Rs ' . number_format($discount->value) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-slate-500 font-medium">{{ $discount->used_count }} / {{ $discount->usage_limit ?? '∞' }}</span>
                </td>
                <td class="px-6 py-4">
                    <x-ui.badge :type="$discount->is_active ? 'success' : 'warning'">{{ $discount->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-slate-500 font-medium">{{ $discount->expires_at ? $discount->expires_at : 'Never' }}</span>
                </td>
                <td class="px-6 py-4 text-right whitespace-nowrap">
                    <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ghost text-red-600" data-confirm="Delete this coupon?">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7"><x-ui.empty-state title="No coupons yet" message="Create a coupon to offer discounts." /></td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
        {{ $discounts->links() }}
    </div>
</div>
@endsection
