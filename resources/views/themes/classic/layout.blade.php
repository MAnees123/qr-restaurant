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
        <nav class="classic-sb-nav">
            <a href="{{ route('admin.dashboard') }}" class="classic-sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.2" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.2" fill="currentColor"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.2" fill="currentColor"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.2" fill="currentColor"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="classic-sb-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 14v-4M6 14V6M10 14v-8M14 14V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Analytics
            </a>
            <a href="{{ route('kitchen.dashboard') }}" class="classic-sb-item {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 14V9M8 14V4M13 14V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Kitchen Dashboard
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="classic-sb-item {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="10" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M2 6h12" stroke="currentColor" stroke-width="1.4"/></svg>
                Table Reservations
            </a>
            <a href="{{ route('admin.orders.index') }}" class="classic-sb-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 12L5 8l3 3 3-4 3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Orders
            </a>
            <a href="{{ route('admin.discounts.index') }}" class="classic-sb-item {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="5" cy="5" r="1.5" stroke="currentColor" stroke-width="1.3"/><circle cx="11" cy="11" r="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M12 4L4 12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Coupons & Offers
            </a>
            <a href="{{ route('admin.banners.index') }}" class="classic-sb-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 2h12a1 1 0 011 1v10a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1z" stroke="currentColor" stroke-width="1.3"/><path d="M5 7l3 3 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Hot Deals & Ads
            </a>
            <a href="{{ route('admin.tables.index') }}" class="classic-sb-item {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M2 14c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                Tables & QR
            </a>
            <a href="{{ route('admin.menu-categories.index') }}" class="classic-sb-item {{ request()->routeIs('admin.menu-categories.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="9.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1.5" y="9.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="9.5" y="9.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
                Menu Categories
            </a>
            <a href="{{ route('admin.menu-items.index') }}" class="classic-sb-item {{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M2 8h12M2 12h8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                Menu Items
            </a>
            <a href="{{ route('admin.restaurant.index') }}" class="classic-sb-item {{ request()->routeIs('admin.restaurant.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M8 1v1.5M8 13.5V15M1 8h1.5M13.5 8H15M3.05 3.05l1.06 1.06M11.89 11.89l1.06 1.06M3.05 12.95l1.06-1.06M11.89 4.11l1.06-1.06" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Restaurant Settings
            </a>
            <a href="{{ route('admin.themes.index') }}" class="classic-sb-item {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M8 1v1.5M8 13.5V15M1 8h1.5M13.5 8H15M3.05 3.05l1.06 1.06M11.89 11.89l1.06 1.06M3.05 12.95l1.06-1.06M11.89 4.11l1.06-1.06" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Themes
            </a>
        </nav>
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
