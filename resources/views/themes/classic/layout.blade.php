<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1280">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
/* Classic Layout Styles */
.classic-shell { display:flex; min-height:100vh; font-family:'Inter',sans-serif; background:#f0f2f8; min-width:1200px; color:#111827; font-size:13px; }
.classic-sidebar { width:195px; flex-shrink:0; background:#1a2454; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; overflow-y:auto; }
.classic-sb-topbar { display:flex; align-items:center; justify-content:space-between; padding:20px 20px 18px; border-bottom:1px solid rgba(255,255,255,0.08); }
.classic-sb-icon-btn { width:34px; height:34px; border-radius:8px; background:rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; cursor:pointer; border:none; }
.classic-sb-nav { padding:12px 0; }
.classic-sb-item { display:flex; align-items:center; gap:12px; padding:11px 20px; font-size:13.5px; font-weight:500; color:rgba(255,255,255,0.65); cursor:pointer; transition:background 0.15s,color 0.15s; text-decoration:none; }
.classic-sb-item:hover { background:rgba(255,255,255,0.06); color:#fff; }
.classic-sb-item.active { background:#2d3f7c; color:#ffffff; font-weight:600; }
.classic-sb-item svg { flex-shrink:0; opacity:0.75; }
.classic-sb-item.active svg { opacity:1; }

.classic-main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.classic-topbar { background:#ffffff; height:58px; display:flex; align-items:center; padding:0 28px; border-bottom:1px solid #e5e7eb; flex-shrink:0; gap:16px; }
.classic-topbar-title { font-size:18px; font-weight:700; color:#111827; flex:1; }
.classic-topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
.classic-tb-icon { width:34px; height:34px; border-radius:50%; background:#f3f4f6; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.classic-admin-pill { display:flex; align-items:center; gap:8px; padding:5px 12px 5px 6px; border-radius:30px; background:#f3f4f6; cursor:pointer; }
.classic-admin-avatar { width:26px; height:26px; border-radius:50%; background:linear-gradient(135deg,#3b5bdb,#6b8cda); display:flex; align-items:center; justify-content:center; overflow:hidden;}
.classic-admin-name { font-size:13px; font-weight:600; color:#374151; }

.classic-content { padding:24px 24px 30px; flex:1; overflow-y:auto; }
</style>
@stack('styles')
@include('partials.pwa-head', ['restaurant' => $restaurant ?? null])
</head>
<body>
<div class="classic-shell">
    <aside class="classic-sidebar">
        <div class="classic-sb-topbar">
            <div class="classic-sb-icon-btn">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"/><path d="M9 5.5V9l2.5 1.5" stroke="rgba(255,255,255,0.8)" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div class="classic-sb-icon-btn text-white font-bold text-xs uppercase">{{ substr(auth()->user()->restaurant->name ?? 'Admin', 0, 2) }}</div>
        </div>
            <div style="--t-accent:#7aa2ff;"><x-sidebar-nav /></div>
    </aside>

    <div class="classic-main">
        <header class="classic-topbar">
            <span class="classic-topbar-title">@yield('title', 'Admin Dashboard')</span>
            <div class="classic-topbar-right">
                @include('components.header-notifications')
                
                <div x-data="{ open: false }" class="relative">
                    <div @click="open = !open" class="classic-admin-pill">
                        <div class="classic-admin-avatar">
                            @if(auth()->user()->restaurant && auth()->user()->restaurant->logo)
                                <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="6" r="2.5" fill="rgba(255,255,255,0.9)"/><path d="M2.5 14c0-3.04 2.46-5.5 5.5-5.5s5.5 2.46 5.5 5.5" stroke="rgba(255,255,255,0.9)" stroke-width="1.3" stroke-linecap="round"/></svg>
                            @endif
                        </div>
                        <span class="classic-admin-name">{{ auth()->user()->name }}</span>
                    </div>
                    <div x-show="open" @click.away="open = false" style="display: none;"
                        class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden text-left">
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
            </div>
        </header>

        <div class="classic-content">
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
