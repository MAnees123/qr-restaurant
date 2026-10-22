@extends('layouts.admin')

@section('title', 'Themes')
@section('header', 'Dashboard Themes')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h2 class="page-title">Select your dashboard theme</h2>
        <p class="page-subtitle">Customize your workspace. Changes will only apply to your account and preserve all your data.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($themes as $t)
        <div class="card card-hover overflow-hidden group flex flex-col">
            <!-- Preview Image -->
            <div class="h-56 bg-slate-50 relative overflow-hidden border-b border-slate-100 p-2">
                <div class="w-full h-full rounded-2xl overflow-hidden bg-slate-200 border border-slate-200">
                    <img src="{{ $t['preview'] }}" alt="{{ $t['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 opacity-90 group-hover:opacity-100">
                </div>
                @if(auth()->user()->theme === $t['id'] || (empty(auth()->user()->theme) && $t['id'] === 'default'))
                    <div class="absolute top-5 right-5 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-lg border-2 border-white">
                        Active Theme
                    </div>
                @endif
            </div>
            <!-- Info -->
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ $t['name'] }}</h3>
                <p class="text-sm text-slate-500 mb-6 flex-1 leading-relaxed">{{ $t['description'] }}</p>
                
                <form method="POST" action="{{ route('admin.themes.apply') }}" class="mt-auto">
                    @csrf
                    <input type="hidden" name="theme" value="{{ $t['id'] }}">
                    <button type="submit" 
                            class="btn w-full {{ (auth()->user()->theme === $t['id'] || (empty(auth()->user()->theme) && $t['id'] === 'default')) ? 'btn-secondary' : 'btn-primary' }}" 
                            {{ (auth()->user()->theme === $t['id'] || (empty(auth()->user()->theme) && $t['id'] === 'default')) ? 'disabled' : '' }}>
                        {{ (auth()->user()->theme === $t['id'] || (empty(auth()->user()->theme) && $t['id'] === 'default')) ? 'Currently Active' : 'Apply Theme' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
