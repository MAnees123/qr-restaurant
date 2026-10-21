@extends('layouts.admin')
@section('title', 'Tax Rates')
@section('header', 'Tax Rates')
@section('content')
@include('finance.partials.errors')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-[2rem] border shadow-sm overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-slate-50/50 border-b">@foreach(['Name','Rate','Type','Status',''] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
            <tbody class="divide-y">
            @forelse($rates as $r)
                <tr><td class="px-6 py-3 font-bold">{{ $r->name }} @if($r->is_default)<span class="bg-amber-100 text-amber-700 text-[10px] font-black uppercase px-2 py-1 rounded-md">Default</span>@endif</td>
                    <td class="px-6 py-3">{{ rtrim(rtrim($r->rate, '0'), '.') }}%</td><td class="px-6 py-3 capitalize text-sm">{{ $r->type }}</td>
                    <td class="px-6 py-3 text-xs font-black uppercase {{ $r->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $r->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="px-6 py-3 space-x-2 whitespace-nowrap"><a href="{{ route('admin.taxes.edit', $r) }}" class="text-blue-500 font-black text-sm">Edit</a>
                        <form action="{{ route('admin.taxes.destroy', $r) }}" method="POST" class="inline" data-confirm="Delete this tax rate?">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm">Delete</button></form></td></tr>
            @empty<tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No tax rates. Invoices are issued without tax until a default active rate exists.</td></tr>@endforelse
            </tbody>
        </table>
        <div class="p-4"><a href="{{ route('admin.taxes.summary') }}" class="text-blue-500 font-black text-sm">View tax summary report &rarr;</a></div>
    </div>
    <div class="bg-white rounded-[2rem] border shadow-sm p-6">
        <p class="font-black mb-4">Add tax rate</p>
        <form method="POST" action="{{ route('admin.taxes.store') }}">@csrf @include('finance.taxes.fields')<button class="w-full bg-emerald-600 text-white font-black py-3 rounded-2xl">Add</button></form>
    </div>
</div>
@endsection
