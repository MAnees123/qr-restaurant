@extends('layouts.admin')
@section('title', 'Suppliers')
@section('header', 'Suppliers')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-center">
    <form method="GET" class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search name, phone, contact" class="rounded-xl border-slate-200 text-sm"><button class="bg-slate-800 text-white font-bold px-4 rounded-xl text-sm">Search</button></form>
    <a href="{{ route('admin.suppliers.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg shadow-emerald-100">New Supplier</a>
</div>
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
            @foreach(['Supplier','Contact','Terms','Balance owed','Status','Actions'] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($suppliers as $s)
            <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-4 font-bold text-slate-800">{{ $s->name }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $s->contact_person }} {{ $s->phone }}<br>{{ $s->email }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $s->payment_terms ?: '-' }}</td>
                <td class="px-6 py-4 font-black text-slate-800">Rs {{ number_format($s->balance, 2) }}</td>
                <td class="px-6 py-4">@if($s->is_active)<span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase px-2 py-1 rounded-md">Active</span>@else<span class="bg-slate-100 text-slate-500 text-[10px] font-black uppercase px-2 py-1 rounded-md">Inactive</span>@endif</td>
                <td class="px-6 py-4 space-x-2 whitespace-nowrap">
                    <a href="{{ route('admin.suppliers.edit', $s) }}" class="text-blue-500 font-black text-sm">Edit</a>
                    <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" class="inline" data-confirm="Delete this supplier and its purchase orders?">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">No suppliers yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $suppliers->links() }}</div>
</div>
@endsection
