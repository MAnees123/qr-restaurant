@extends('layouts.admin')
@section('title', 'Mobile App (PWA)')
@section('header', 'Mobile App (PWA)')

@section('content')
@include('platform._errors')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
        <h2 class="font-black text-slate-800 text-lg mb-2">Installable web app</h2>
        <p class="text-sm text-slate-500 mb-4">Guests and staff can install your menu on their phone home screen. This is a Progressive Web App (manifest, service worker, offline page), not a store-published native app.</p>
        <dl class="text-sm space-y-2">
            <div><dt class="font-black text-slate-400 text-xs uppercase">Install page (share / put on a QR)</dt><dd><a class="text-emerald-600 font-bold break-all" href="{{ route('pwa.install', $restaurant->id) }}" target="_blank">{{ route('pwa.install', $restaurant->id) }}</a></dd></div>
            <div><dt class="font-black text-slate-400 text-xs uppercase">Manifest</dt><dd class="break-all">{{ route('pwa.manifest', $restaurant->id) }}</dd></div>
        </dl>
        <p class="text-xs text-slate-400 mt-3">Installation prompts only appear on HTTPS pages that include the PWA head partial.</p>
    </div>
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
        <form action="{{ route('admin.pwa.update') }}" method="POST" class="grid sm:grid-cols-3 gap-4 items-end">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Short name</label>
                <input type="text" name="short_name" maxlength="30" value="{{ old('short_name', $setting->short_name) }}" class="w-full rounded-xl border-slate-200 font-bold">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Theme color</label>
                <input type="color" name="theme_color" value="{{ old('theme_color', $setting->theme_color) }}" class="w-full h-11 rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Background</label>
                <input type="color" name="background_color" value="{{ old('background_color', $setting->background_color) }}" class="w-full h-11 rounded-xl border-slate-200">
            </div>
            <button class="sm:col-span-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl transition">Save</button>
        </form>
    </div>
</div>
@endsection
