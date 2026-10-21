@extends('layouts.admin')

@section('title', 'Barcodes')
@section('header', 'Item Barcodes')

@section('content')
@include('admin.comms._flash')
<div class="mb-6 flex flex-col md:flex-row gap-3 justify-between">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or barcode" class="rounded-xl border-slate-200">
        <button class="bg-slate-100 px-4 rounded-xl font-bold">Search</button>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('admin.barcodes.scan') }}" class="bg-slate-800 text-white font-bold py-2 px-5 rounded-xl">Scan / quick order</a>
        <a href="{{ route('admin.barcodes.labels') }}" target="_blank" class="bg-emerald-600 text-white font-bold py-2 px-5 rounded-xl">Print labels</a>
    </div>
</div>
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-x-auto">
<table class="w-full text-left">
    <thead><tr class="bg-slate-50/50">
        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Item</th>
        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Barcode (EAN-13 or Code 128)</th>
        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
    </tr></thead>
    <tbody class="divide-y divide-slate-100">
    @forelse($items as $item)
        <tr>
            <td class="px-6 py-4 font-bold">{{ $item->name }}<div class="text-xs text-slate-400">Rs {{ number_format($item->price) }}</div></td>
            <td class="px-6 py-4">
                <form action="{{ route('admin.barcodes.update', $item) }}" method="POST" class="flex gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="barcode" value="{{ $item->barcode }}" maxlength="40" class="rounded-xl border-slate-200 font-mono text-sm">
                    <button class="bg-slate-100 px-3 rounded-xl text-sm font-bold">Save</button>
                </form>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
                <form action="{{ route('admin.barcodes.generate', $item) }}" method="POST" class="inline">@csrf
                    <button class="text-emerald-600 font-black text-sm">Generate EAN-13</button></form>
                @if($item->barcode)<a target="_blank" href="{{ route('admin.barcodes.svg', $item) }}" class="text-blue-500 font-black text-sm">View</a>@endif
            </td>
        </tr>
    @empty
        <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400">No menu items.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
