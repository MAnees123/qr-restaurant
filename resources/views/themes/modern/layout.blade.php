<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
    body { font-family:'DM Sans',sans-serif; background:#edf2f7; min-height:100vh; min-width:1200px; color:#0f172a; margin:0; }
    .modern-shell { display:flex; min-height:100vh; }
    .modern-sidebar { width:240px; flex-shrink:0; background:#0D49AB; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; overflow-y:auto; }
    .modern-sb-logo { display:flex; align-items:center; gap:12px; padding:24px 20px 20px; border-bottom:1px solid rgba(255,255,255,0.15); }
    .modern-sb-diamond { width:34px; height:34px; background:rgba(255,255,255,0.2); border-radius:8px; transform:rotate(45deg); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .modern-sb-diamond svg { transform:rotate(-45deg); }
    .modern-sb-brand { font-size:14px; font-weight:800; letter-spacing:1.5px; color:#fff; text-transform:uppercase; white-space:nowrap; }
    .modern-sb-nav { flex:1; padding:16px 0; }
    .modern-sb-item { display:flex; align-items:center; gap:12px; padding:10px 20px; font-size:13.5px; font-weight:700; color:#ffffff; cursor:pointer; transition:all 0.15s; text-decoration:none; border-left:3px solid transparent; }
    .modern-sb-item:hover { background:rgba(255,255,255,0.1); }
    .modern-sb-item.active { background:rgba(255,255,255,0.15); font-weight:800; border-left-color:#fff; }
    .modern-sb-item svg { flex-shrink:0; opacity:0.7; }
    .modern-sb-item.active svg { opacity:1; }

    .modern-sb-user { padding:16px 20px; border-top:1px solid rgba(255,255,255,0.15); display:flex; align-items:center; gap:12px; margin-top:auto; }
    .modern-sb-avatar { width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; border:2px solid rgba(255,255,255,0.3); }
    .modern-sb-uname .hi { font-size:10.5px; color:rgba(255,255,255,0.6); font-weight:400; }
    .modern-sb-uname .nm { font-size:13px; color:#fff; font-weight:700; }

    .modern-main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
    .modern-topbar { background:#ffffff; height:60px; display:flex; align-items:center; padding:0 28px; border-bottom:1px solid #e8edf4; flex-shrink:0; }
    .modern-topbar-title { font-size:18px; font-weight:700; color:#0f172a; flex:1; }
    .modern-topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
    .modern-content { padding:28px 28px 40px; flex:1; overflow-y:auto; }
    </style>
    @stack('styles')
@include('partials.pwa-head', ['restaurant' => $restaurant ?? null])
</head>
<body>
<div class="modern-shell">
    <aside class="modern-sidebar">
        <div class="modern-sb-logo">
            <div class="modern-sb-diamond">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"><path d="M7 1.5L12.5 7L7 12.5L1.5 7Z" fill="white"/></svg>
            </div>
            <span class="modern-sb-brand">{{ auth()->user()->restaurant?->name ?? 'Admin' }}</span>
        </div>
        @php $u = auth()->user(); @endphp
            <div style="--t-accent:#ffffff;--t-fg:rgba(255,255,255,.88);--t-hover:rgba(255,255,255,.12);--t-active-bg:rgba(255,255,255,.2);"><x-sidebar-nav /></div>

        <div class="modern-sb-user" x-data="{ open: false }">
            <div class="relative">
                <div @click="open = !open" class="modern-sb-avatar cursor-pointer">
                    @if(auth()->user()->restaurant?->logo)
                        <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="7.5" r="3" fill="rgba(255,255,255,0.93)"/><path d="M3.5 18c0-3.59 2.91-6.5 6.5-6.5s6.5 2.91 6.5 6.5" stroke="rgba(255,255,255,0.93)" stroke-width="1.5" stroke-linecap="round"/></svg>
                    @endif
                </div>
                <div x-show="open" @click.away="open = false" style="display: none;"
                    class="absolute left-0 bottom-full mb-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden text-left">
                    <a href="{{ route('admin.restaurant.index') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-100">Restaurant Profile</a>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-100">Update Password</a>
                    @if(auth()->user()->is_super_admin)
                        <a href="{{ route('superadmin.dashboard') }}" class="block px-4 py-3 text-sm text-indigo-700 hover:bg-indigo-50 border-b border-slate-100 font-bold">Super Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-bold">Logout</button>
                    </form>
                </div>
            </div>
            <div class="modern-sb-uname">
                <div class="hi">Hello!</div>
                <div class="nm">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </aside>

    <div class="modern-main">
        <header class="modern-topbar">
            <span class="modern-topbar-title">@yield('title', 'Admin Dashboard')</span>
            <div class="modern-topbar-right">
                @include('components.header-notifications')
                <span class="text-sm text-slate-500 ml-4">{{ now()->format('l, d M Y') }}</span>
            </div>
        </header>
        <div class="modern-content">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    (function() {
        var token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    })();
</script>
@stack('scripts')
@include('components.order-modal')
@include('components.global-notifications')
</body>
</html>
