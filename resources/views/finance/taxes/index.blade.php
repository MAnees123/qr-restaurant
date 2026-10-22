@extends('layouts.admin')
@section('title', 'Tax Rates')
@section('header', 'Tax Rates')
@section('content')
@include('finance.partials.errors')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card overflow-x-auto self-start">
        <table class="w-full text-left">
            <thead><tr>@foreach(['Name','Rate','Type','Status',''] as $h)<th class="px-6 py-4 {{ $h === 'Rate' ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
            <tbody>
            @forelse($rates as $r)
                <tr><td class="px-6 py-4 font-medium">{{ $r->name }} @if($r->is_default)<x-ui.badge type="warning">Default</x-ui.badge>@endif</td>
                    <td class="px-6 py-4 text-right font-semibold">{{ rtrim(rtrim($r->rate, '0'), '.') }}%</td><td class="px-6 py-4 capitalize text-sm">{{ $r->type }}</td>
                    <td class="px-6 py-4">@if($r->is_active)<x-ui.badge type="success">Active</x-ui.badge>@else<x-ui.badge>Inactive</x-ui.badge>@endif</td>
                    <td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center justify-end gap-2"><a href="{{ route('admin.taxes.edit', $r) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('admin.taxes.destroy', $r) }}" method="POST" class="inline" data-confirm="Delete this tax rate?">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600">Delete</button></form></div></td></tr>
            @empty<tr><td colspan="5"><x-ui.empty-state title="No tax rates" message="Invoices are issued without tax until a default active rate exists." /></td></tr>@endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100"><a href="{{ route('admin.taxes.summary') }}" class="btn btn-sm btn-secondary">View tax summary report &rarr;</a></div>
    </div>
    <div class="card p-6 self-start">
        <p class="font-semibold text-slate-800 mb-4">Add tax rate</p>
        <form method="POST" action="{{ route('admin.taxes.store') }}">@csrf @include('finance.taxes.fields')<button class="btn btn-primary w-full">Add</button></form>
    </div>
</div>
@endsection
