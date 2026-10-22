@extends('layouts.admin')

@section('title', 'Custom Branding')
@section('header', 'Custom Branding')

@section('content')
@include('admin.comms._flash')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="{ primary: '{{ old('primary', $settings['primary']) }}', secondary: '{{ old('secondary', $settings['secondary']) }}', tagline: @js(old('tagline', $settings['tagline'])) }">
    <div class="card p-6 md:p-8">
        <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Primary colour</label>
                    <input type="color" name="primary" x-model="primary" class="input !p-1 h-12">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Secondary colour</label>
                    <input type="color" name="secondary" x-model="secondary" class="input !p-1 h-12">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tagline</label>
                <input type="text" name="tagline" x-model="tagline" maxlength="120" class="input" placeholder="Fresh. Fast. Local.">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Logo</label>
                @if($settings['logo'])<img src="{{ asset('storage/'.$settings['logo']) }}" class="h-14 mb-2 rounded"><label class="text-xs text-slate-600 flex items-center gap-1 mb-2"><input type="checkbox" name="remove_logo" value="1"> Remove logo</label>@endif
                <input type="file" name="logo" accept="image/*" class="input text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                @if($settings['favicon'])<img src="{{ asset('storage/'.$settings['favicon']) }}" class="h-8 mb-2"><label class="text-xs text-slate-600 flex items-center gap-1 mb-2"><input type="checkbox" name="remove_favicon" value="1"> Remove favicon</label>@endif
                <input type="file" name="favicon" accept="image/*" class="input text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
            </div>
            <div class="pt-2"><button class="btn btn-primary">Save branding</button></div>
        </form>
    </div>

    <div class="card p-6 md:p-8">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Live preview (guest menu)</h3>
        <div class="rounded-2xl overflow-hidden border max-w-xs mx-auto" style="background:#1c1c1c">
            <div class="p-4 text-white">
                <div class="font-bold">Hi, Table 5</div>
                <div class="flex items-center gap-2 mt-1 text-xs text-gray-300">
                    @if($settings['logo'])<img src="{{ asset('storage/'.$settings['logo']) }}" class="h-6 rounded">@endif
                    <span x-text="tagline || 'Ready to order from {{ addslashes($restaurant->name) }}?'" :style="'color:' + primary"></span>
                </div>
            </div>
            <div class="p-4 space-y-3 bg-[#252525]">
                <div class="text-white text-sm">Chicken Karahi <b :style="'color:' + primary">Rs 1200</b></div>
                <button type="button" class="w-full text-white text-xs font-bold py-2 rounded-lg uppercase" :style="'background:' + secondary">Add to basket</button>
                <button type="button" class="w-full text-white text-xs font-bold py-2 rounded-lg uppercase" :style="'background:' + primary">Place order</button>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-4">Branding is shown on the guest QR menu, on printed receipts and as accent colours in this admin area.</p>
    </div>
</div>
@endsection
