@extends('layouts.admin')

@section('title', 'Themes')
@section('header', 'Dashboard Themes')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-6 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl flex items-center justify-between">
            <span class="block sm:inline font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Select your dashboard theme</h2>
        <p class="text-slate-500 mt-2">Customize your workspace. Changes will only apply to your account and preserve all your data.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($themes as $t)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition duration-500 flex flex-col">
            <!-- Preview Image -->
            <div class="h-56 bg-slate-50 relative overflow-hidden border-b border-slate-100 p-2">
                <div class="w-full h-full rounded-2xl overflow-hidden bg-slate-200 border border-slate-200">
                    <img src="{{ $t['preview'] }}" alt="{{ $t['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 opacity-90 group-hover:opacity-100">
                </div>
                @if(auth()->user()->theme === $t['id'] || (empty(auth()->user()->theme) && $t['id'] === 'default'))
                    <div class="absolute top-6 right-6 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-lg border-2 border-white shadow-emerald-500/30">
                        Active Theme
                    </div>
                @endif
            </div>
            <!-- Info -->
            <div class="p-8 flex-1 flex flex-col">
                <h3 class="text-xl font-black text-slate-800 mb-2">{{ $t['name'] }}</h3>
                <p class="text-sm text-slate-500 mb-8 flex-1 leading-relaxed">{{ $t['description'] }}</p>
                
                <form method="POST" action="{{ route('admin.themes.apply') }}" class="mt-auto">
                    @csrf
                    <input type="hidden" name="theme" value="{{ $t['id'] }}">
                    <button type="submit" 
                            class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition duration-300 {{ (auth()->user()->theme === $t['id'] || (empty(auth()->user()->theme) && $t['id'] === 'default')) ? 'bg-slate-50 text-slate-400 border border-slate-200 cursor-not-allowed' : 'bg-slate-900 hover:bg-slate-800 text-white shadow-xl shadow-slate-900/20 hover:shadow-slate-900/40 border border-slate-900' }}" 
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
