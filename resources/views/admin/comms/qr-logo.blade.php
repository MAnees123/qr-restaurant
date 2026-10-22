@extends('layouts.admin')

@section('title', 'QR Logo')
@section('header', 'QR Code Logo')

@section('content')
@include('admin.comms._flash')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card p-6 md:p-8">
        <form action="{{ route('admin.qr-logo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')
            <label class="flex items-center gap-3"><input type="checkbox" name="enabled" value="1" class="rounded text-indigo-600" {{ old('enabled', $settings['enabled']) ? 'checked' : '' }}><span class="text-sm font-medium text-slate-700">Overlay logo in the QR centre</span></label>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Logo source</label>
                <select name="source" class="input">
                    <option value="restaurant" @selected(old('source', $settings['source']) === 'restaurant')>Restaurant logo {{ $restaurant->logo ? '' : '(none uploaded yet)' }}</option>
                    <option value="custom" @selected(old('source', $settings['source']) === 'custom')>Custom QR logo (upload below)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Custom logo</label>
                @if($settings['logo'])<img src="{{ asset('storage/'.$settings['logo']) }}" class="h-12 mb-2 rounded">@endif
                <input type="file" name="logo" accept="image/*" class="input text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div><label class="block text-sm font-medium text-slate-700 mb-2">Size % (10-30)</label>
                    <input type="number" name="size_percent" min="10" max="30" value="{{ old('size_percent', $settings['size_percent']) }}" class="input"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-2">QR colour</label>
                    <input type="color" name="foreground" value="{{ old('foreground', $settings['foreground']) }}" class="input !p-1 h-11"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-2">Logo background</label>
                    <input type="color" name="background" value="{{ old('background', $settings['background']) }}" class="input !p-1 h-11"></div>
            </div>
            <label class="flex items-center gap-2"><input type="checkbox" name="rounded" value="1" class="rounded text-indigo-600" {{ old('rounded', $settings['rounded']) ? 'checked' : '' }}><span class="text-sm font-medium text-slate-700">Rounded logo plate</span></label>
            <p class="text-xs text-slate-400">Use dark QR colours on a light background and keep the logo at 30% or less so scanners can still read the code (high error correction is used).</p>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
    <div class="card p-6 text-center self-start">
        <h3 class="text-base font-semibold text-slate-800 mb-3">Preview</h3>
        @if($preview)<div class="inline-block w-56">{!! $preview !!}</div>@else<p class="text-slate-400 text-sm">Add a table to see a preview.</p>@endif
    </div>
</div>

<div class="card mt-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 text-base font-semibold text-slate-800">Table QR codes with logo</div>
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-3">
        @forelse($tables as $t)
            <div class="card card-hover p-3 text-center">
                <div class="font-semibold text-slate-800">{{ $t->table_number }}</div>
                <a class="text-xs text-indigo-600 font-semibold hover:underline" target="_blank" href="{{ route('admin.qr-logo.svg', $t) }}">View</a> ·
                <a class="text-xs text-indigo-600 font-semibold hover:underline" href="{{ route('admin.qr-logo.svg', [$t, 'download' => 1]) }}">Download SVG</a>
            </div>
        @empty
            <div class="col-span-full"><x-ui.empty-state title="No tables yet" /></div>
        @endforelse
    </div>
</div>
@endsection
