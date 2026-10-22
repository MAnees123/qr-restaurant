@extends('layouts.admin')
@section('title', 'Suppliers')
@section('header', 'Suppliers')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-center">
    <form method="GET" class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search name, phone, contact" class="input"><button class="btn btn-secondary">Search</button></form>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">New Supplier</a>
</div>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr>
            @foreach(['Supplier','Contact','Terms','Balance owed','Status','Actions'] as $h)<th class="px-6 py-4 {{ $h === 'Balance owed' ? 'text-right' : '' }}">{{ $h }}</th>@endforeach
        </tr></thead>
        <tbody>
        @forelse($suppliers as $s)
            <tr>
                <td class="px-6 py-4 font-semibold text-slate-800">{{ $s->name }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $s->contact_person }} {{ $s->phone }}<br>{{ $s->email }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $s->payment_terms ?: '-' }}</td>
                <td class="px-6 py-4 text-right font-semibold text-slate-800 whitespace-nowrap">Rs {{ number_format($s->balance, 2) }}</td>
                <td class="px-6 py-4">@if($s->is_active)<x-ui.badge type="success">Active</x-ui.badge>@else<x-ui.badge>Inactive</x-ui.badge>@endif</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2"><a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" class="inline" data-confirm="Delete this supplier and its purchase orders?">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600">Delete</button></form></div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><x-ui.empty-state title="No suppliers yet" /></td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $suppliers->links() }}</div>
</div>
@endsection
