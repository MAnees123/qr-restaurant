@extends('layouts.admin')
@section('title', 'Mobile App (PWA)')
@section('header', 'Mobile App (PWA)')

@section('content')
@include('platform._errors')
<div class="max-w-3xl space-y-6">
    <div class="card p-6 sm:p-8">
        <h2 class="font-semibold text-slate-800 text-lg mb-2">Installable web app</h2>
        <p class="text-sm text-slate-500 mb-4">Guests and staff can install your menu on their phone home screen. This is a Progressive Web App (manifest, service worker, offline page), not a store-published native app.</p>
        <dl class="text-sm space-y-2">
            <div><dt class="font-medium text-slate-500 text-xs uppercase tracking-wide">Install page (share / put on a QR)</dt><dd><a class="text-indigo-600 font-semibold break-all hover:underline" href="{{ route('pwa.install', $restaurant->id) }}" target="_blank">{{ route('pwa.install', $restaurant->id) }}</a></dd></div>
            <div><dt class="font-medium text-slate-500 text-xs uppercase tracking-wide">Manifest</dt><dd class="break-all">{{ route('pwa.manifest', $restaurant->id) }}</dd></div>
        </dl>
        <p class="text-xs text-slate-400 mt-3">Installation prompts only appear on HTTPS pages that include the PWA head partial.</p>
    </div>
    <div class="card p-6 sm:p-8">
        <form action="{{ route('admin.pwa.update') }}" method="POST" class="grid sm:grid-cols-3 gap-4 items-end">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Short name</label>
                <input type="text" name="short_name" maxlength="30" value="{{ old('short_name', $setting->short_name) }}" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Theme color</label>
                <input type="color" name="theme_color" value="{{ old('theme_color', $setting->theme_color) }}" class="input !p-1 h-11">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Background</label>
                <input type="color" name="background_color" value="{{ old('background_color', $setting->background_color) }}" class="input !p-1 h-11">
            </div>
            <button class="sm:col-span-3 btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
