@extends('layouts.admin')

@section('title', 'Barcodes')
@section('header', 'Item Barcodes')

@section('content')
@include('admin.comms._flash')
<div class="space-y-6">
<div class="page-header !mb-0">
    <div><h1 class="page-title">Item Barcodes</h1><p class="page-subtitle">Assign, generate and print barcodes for menu items.</p></div>
</div>
<div class="flex flex-col md:flex-row gap-3 justify-between">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or barcode" class="input">
        <button class="btn btn-secondary">Search</button>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('admin.barcodes.scan') }}" class="btn btn-secondary">Scan / quick order</a>
        <a href="{{ route('admin.barcodes.labels') }}" target="_blank" class="btn btn-primary">Print labels</a>
    </div>
</div>
<div class="card overflow-x-auto">
<table class="w-full text-left">
    <thead><tr>
        <th class="px-6 py-4">Item</th>
        <th class="px-6 py-4">Barcode (EAN-13 or Code 128)</th>
        <th class="px-6 py-4 text-right">Actions</th>
    </tr></thead>
    <tbody>
    @forelse($items as $item)
        <tr>
            <td class="px-6 py-4 font-semibold text-slate-800">{{ $item->name }}<div class="text-xs text-slate-400">Rs {{ number_format($item->price) }}</div></td>
            <td class="px-6 py-4">
                <form action="{{ route('admin.barcodes.update', $item) }}" method="POST" class="flex gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="barcode" value="{{ $item->barcode }}" maxlength="40" class="input font-mono">
                    <button class="btn btn-sm btn-secondary">Save</button>
                </form>
            </td>
            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                <form action="{{ route('admin.barcodes.generate', $item) }}" method="POST" class="inline">@csrf
                    <button class="btn btn-sm btn-ghost text-indigo-600">Generate EAN-13</button></form>
                @if($item->barcode)<a target="_blank" href="{{ route('admin.barcodes.svg', $item) }}" class="btn btn-sm btn-ghost">View</a>@endif
            </td>
        </tr>
    @empty
        <tr><td colspan="3"><x-ui.empty-state title="No menu items" /></td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div>{{ $items->links() }}</div>
</div>
@endsection
