@extends('layouts.admin')

@section('header', 'Tables & QR Codes')

@section('content')
<div class="mb-6 flex justify-end">
    <form action="{{ route('admin.tables.store') }}" method="POST" class="flex gap-4 items-center bg-white p-4 rounded-xl shadow-sm border w-full max-w-2xl">
        @csrf
        <input type="text" name="table_number" placeholder="Table Number (e.g. T-1)" class="rounded-lg border-gray-300 flex-1" required>
        <input type="number" name="capacity" placeholder="Capacity" class="rounded-lg border-gray-300 w-24">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded text-amber-500"> Active
        </label>
        <button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-amber-600">Add Table</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tables as $table)
        <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col items-center text-center">
            <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $table->table_number }}</h3>
            <div class="mb-3">
                <span class="px-2 py-1 text-xs font-bold rounded-full 
                    {{ $table->status === 'free' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $table->status === 'occupied' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $table->status === 'reserved' ? 'bg-blue-100 text-blue-700' : '' }}">
                    {{ strtoupper($table->status) }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-6">Capacity: {{ $table->capacity ?? 'N/A' }} • {{ $table->is_active ? 'Active' : 'Inactive' }}</p>
            
            <div class="mb-6 p-4 bg-white border-2 border-dashed border-gray-200 rounded-xl">
                @if($table->qrCode)
                    <!-- Generate SVG on the fly using simple-qrcode -->
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(route('menu.show', $table->qrCode->code)) !!}
                    <div class="mt-4 text-xs text-gray-500 break-all">
                        <a href="{{ route('menu.show', $table->qrCode->code) }}" target="_blank" class="text-amber-500 hover:underline">View Menu</a>
                    </div>
                @else
                    <div class="w-[150px] h-[150px] bg-gray-100 flex items-center justify-center text-gray-400">No QR</div>
                @endif
            </div>
            
            <div class="flex gap-2 w-full mb-2">
                <form action="{{ route('admin.tables.update', $table) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="table_number" value="{{ $table->table_number }}">
                    <input type="hidden" name="status" value="free">
                    <button type="submit" class="w-full py-2 text-xs font-bold border rounded-lg {{ $table->status === 'free' ? 'bg-emerald-500 text-white' : 'text-emerald-600 bg-white hover:bg-emerald-50' }}">FREE</button>
                </form>
                <form action="{{ route('admin.tables.update', $table) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="table_number" value="{{ $table->table_number }}">
                    <input type="hidden" name="status" value="occupied">
                    <button type="submit" class="w-full py-2 text-xs font-bold border rounded-lg {{ $table->status === 'occupied' ? 'bg-amber-500 text-white' : 'text-amber-600 bg-white hover:bg-amber-50' }}">BUSY</button>
                </form>
            </div>
            
            <div class="flex gap-2 w-full">
                <!-- Delete -->
                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this table?')" class="w-full py-2 text-sm text-slate-400 hover:text-red-600 font-medium transition-colors">Delete Table</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection