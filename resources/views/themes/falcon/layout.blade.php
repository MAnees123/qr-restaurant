<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1280">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Load Tailwind for other pages -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
    body { font-family:'Poppins',sans-serif; background:#eef0f5; min-height:100vh; min-width:1280px; color:#344050; font-size:13px; }
    .theme-shell { display:flex; min-height:100vh; min-width:1280px; }
    .theme-sidebar { width:220px; flex-shrink:0; background:#ffffff; display:flex; flex-direction:column; border-right:1px solid #e3e6ea; overflow-y:auto; position:sticky; top:0; height:100vh; }
    .theme-sb-logo { display:flex; align-items:center; gap:10px; padding:18px 20px 16px; border-bottom:1px solid #eef0f5; text-decoration: none; }
    .theme-sb-logo-icon { width:32px; height:32px; background:#2c7be5; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .theme-sb-logo-text { font-size:20px; font-weight:700; color:#2c7be5; letter-spacing:-0.3px; }
    .theme-sb-search { padding:12px 14px; border-bottom:1px solid #eef0f5; }
    .theme-sb-search input { width:100%; padding:7px 10px; border-radius:6px; border:1px solid #dce0e6; background:#f9fafb; font-size:12px; color:#344050; font-family:'Poppins',sans-serif; outline:none; }
    .theme-sb-nav { flex:1; padding:8px 0 16px; }
    .theme-sb-section-label { font-size:10px; font-weight:600; color:#9da9bb; letter-spacing:0.08em; text-transform:uppercase; padding:14px 20px 6px; }
    .theme-sb-item { display:flex; align-items:center; gap:10px; padding:8px 20px; cursor:pointer; font-size:13px; font-weight:400; color:#5e6e82; transition:background 0.12s,color 0.12s; text-decoration:none; }
    .theme-sb-item:hover { background:#f5f7fa; color:#2c7be5; }
    .theme-sb-item.active { color:#344050; font-weight:600; background:#f5f7fa; }
    .theme-sb-item svg { flex-shrink:0; opacity:0.7; }
    .theme-sb-item.active svg, .theme-sb-item:hover svg { opacity:1; }
    .theme-main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
    .theme-topbar { background:#ffffff; height:58px; display:flex; align-items:center; padding:0 24px; gap:16px; border-bottom:1px solid #e3e6ea; flex-shrink:0; position:sticky; top:0; z-index:10; }
    .theme-topbar-search { flex:1; max-width:420px; position:relative; }
    .theme-topbar-search input { width:100%; padding:8px 14px 8px 36px; border-radius:20px; border:1px solid #dce0e6; background:#f5f7fa; font-size:13px; font-family:'Poppins',sans-serif; color:#344050; outline:none; }
    .theme-topbar-search svg { position:absolute; left:11px; top:50%; transform:translateY(-50%); opacity:0.45; }
    .theme-topbar-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
    .theme-tb-icon-btn { width:34px; height:34px; border-radius:50%; background:#f5f7fa; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; position:relative; }
    .theme-tb-badge { position:absolute; top:4px; right:4px; width:8px; height:8px; border-radius:50%; background:#e63757; border:1.5px solid #fff; }
    .theme-tb-avatar { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,#92400e,#d97706); overflow:hidden; display:flex; align-items:center; justify-content:center; cursor:pointer; }
    .theme-content { padding:24px; flex:1; overflow-y:auto; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="theme-shell">
        <aside class="theme-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="theme-sb-logo">
                <div class="theme-sb-logo-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 4h12M3 9h8M3 14h10" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <span class="theme-sb-logo-text">{{ auth()->user()->restaurant->name ?? 'Falcon' }}</span>
            </a>
            <div class="theme-sb-search"><input type="text" placeholder="Quick search..."></div>
            <nav class="theme-sb-nav">
                <div class="theme-sb-section-label">Main Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="theme-sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="1" width="5" height="5" rx="1" fill="currentColor"/><rect x="8" y="1" width="5" height="5" rx="1" fill="currentColor"/><rect x="1" y="8" width="5" height="5" rx="1" fill="currentColor"/><rect x="8" y="8" width="5" height="5" rx="1" fill="currentColor"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="theme-sb-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 13v-4M6 13V6M10 13V3M13 13V8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Analytics
                </a>
                <a href="{{ route('kitchen.dashboard') }}" class="theme-sb-item {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 13V8M7 13V3M11 13V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    Kitchen Dashboard
                </a>
                <a href="{{ route('admin.reservations.index') }}" class="theme-sb-item {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="2" width="12" height="10" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M1 5h12" stroke="currentColor" stroke-width="1.3"/></svg>
                    Table Reservations
                </a>
                <a href="{{ route('admin.orders.index') }}" class="theme-sb-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 3h10M2 7h10M2 11h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Orders
                </a>
                <a href="{{ route('admin.discounts.index') }}" class="theme-sb-item {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="4" cy="4" r="1.5" stroke="currentColor" stroke-width="1.3"/><circle cx="10" cy="10" r="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M11 3L3 11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Coupons & Offers
                </a>
                <a href="{{ route('admin.banners.index') }}" class="theme-sb-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 2h10a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1z" stroke="currentColor" stroke-width="1.3"/><path d="M5 6l2 2 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Hot Deals & Ads
                </a>
                <a href="{{ route('admin.tables.index') }}" class="theme-sb-item {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 2h10a1 1 0 011 1v6a1 1 0 01-1 1H7l-3 2v-2H2a1 1 0 01-1-1V3a1 1 0 011-1z" stroke="currentColor" stroke-width="1.3"/></svg>
                    Tables & QR
                </a>
                <a href="{{ route('admin.menu-categories.index') }}" class="theme-sb-item {{ request()->routeIs('admin.menu-categories.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1" y="8" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8" y="8" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
                    Menu Categories
                </a>
                <a href="{{ route('admin.menu-items.index') }}" class="theme-sb-item {{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 3h10M2 7h10M2 11h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Menu Items
                </a>

                <div class="theme-sb-section-label">Settings</div>
                <a href="{{ route('admin.restaurant.index') }}" class="theme-sb-item {{ request()->routeIs('admin.restaurant.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="3" stroke="currentColor" stroke-width="1.3"/><path d="M7 1v1.5M7 11.5V13M1 7h1.5M11.5 7H13M2.75 2.75l1.06 1.06M10.19 10.19l1.06 1.06M2.75 11.25l1.06-1.06M10.19 3.81l1.06-1.06" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                    Restaurant Settings
                </a>
                <a href="{{ route('admin.themes.index') }}" class="theme-sb-item {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.3"/><path d="M7 4v3l2 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Themes
                </a>
            </nav>
        </aside>
        <div class="theme-main">
            <header class="theme-topbar">
                <div class="theme-topbar-search">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="6" cy="6" r="4.5" stroke="#9da9bb" stroke-width="1.5"/><path d="M9.5 9.5L12 12" stroke="#9da9bb" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="theme-topbar-right">
                    @include('components.header-notifications')
                    
                    <div x-data="{ open: false }" class="relative">
                        <div @click="open = !open" class="theme-tb-avatar overflow-hidden">
                            @if(auth()->user()->restaurant && auth()->user()->restaurant->logo)
                                <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="7.5" r="3" fill="rgba(255,255,255,0.92)"/><path d="M3.5 18c0-3.59 2.91-6.5 6.5-6.5s6.5 2.91 6.5 6.5" stroke="rgba(255,255,255,0.92)" stroke-width="1.5" stroke-linecap="round"/></svg>
                            @endif
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
            <div class="theme-content">
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
