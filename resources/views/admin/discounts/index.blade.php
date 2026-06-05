@extends('layouts.admin')

@section('header', 'Manage Discount Coupons')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-slate-500">Create and manage discount codes for your customers.</p>
    <a href="{{ route('admin.discounts.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl transition shadow-lg shadow-emerald-100 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        New Coupon
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Coupon Code</th>
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Type</th>
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Value</th>
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Usage</th>
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Status</th>
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Expires</th>
                <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($discounts as $discount)
            <tr class="hover:bg-slate-50/50 transition">
                <td class="px-8 py-5">
                    <span class="bg-amber-100 text-amber-700 font-black px-3 py-1 rounded-lg text-sm">{{ $discount->code }}</span>
                </td>
                <td class="px-8 py-5">
                    <span class="text-sm font-bold text-slate-600 capitalize">{{ $discount->type }}</span>
                </td>
                <td class="px-8 py-5">
                    <span class="text-sm font-black text-slate-800">
                        {{ $discount->type === 'percentage' ? $discount->value . '%' : 'Rs ' . number_format($discount->value) }}
                    </span>
                </td>
                <td class="px-8 py-5">
                    <span class="text-sm text-slate-500 font-bold">{{ $discount->used_count }} / {{ $discount->usage_limit ?? '∞' }}</span>
                </td>
                <td class="px-8 py-5">
                    @if($discount->is_active)
                        <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase px-2 py-1 rounded-md tracking-wider">Active</span>
                    @else
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-black uppercase px-2 py-1 rounded-md tracking-wider">Inactive</span>
                    @endif
                </td>
                <td class="px-8 py-5">
                    <span class="text-sm text-slate-500 font-medium">{{ $discount->expires_at ? $discount->expires_at : 'Never' }}</span>
                </td>
                <td class="px-8 py-5 text-right space-x-2">
                    <a href="{{ route('admin.discounts.edit', $discount) }}" class="text-blue-500 hover:text-blue-700 font-black text-sm">Edit</a>
                    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 font-black text-sm" onclick="return confirm('Delete this coupon?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
        {{ $discounts->links() }}
    </div>
</div>
@endsection
