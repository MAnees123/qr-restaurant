<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1280">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
.dark-shell { display:flex; min-height:100vh; font-family:'Plus Jakarta Sans',sans-serif; background:#0d0d0d; min-width:1200px; color:#ffffff; font-size:14px; }
.dark-sidebar { width:240px; flex-shrink:0; background:#111111; display:flex; flex-direction:column; padding:28px 20px; gap:0; position:sticky; top:0; height:100vh; overflow-y:auto; }

.dark-sb-user { display:flex; align-items:center; gap:12px; margin-bottom:28px; }
.dark-sb-avatar { width:46px; height:46px; border-radius:50%; background:linear-gradient(135deg,#4a6fa5,#6b8cba); display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; border:2px solid #2a2a2a; }
.dark-sb-user-text .hi { font-size:12px; color:#888; font-weight:400; }
.dark-sb-user-text .name { font-size:14px; color:#fff; font-weight:700; line-height:1.3; }

.dark-sb-search { display:flex; align-items:center; gap:10px; background:#1e1e1e; border-radius:30px; padding:10px 16px; margin-bottom:28px; cursor:text; }
.dark-sb-search svg { flex-shrink:0; opacity:0.5; }
.dark-sb-search span { font-size:13px; color:#555; }

.dark-sb-label { font-size:10.5px; font-weight:700; letter-spacing:0.12em; color:#444; text-transform:uppercase; margin-bottom:12px; }

.dark-sb-active { display:flex; align-items:center; gap:10px; width:100%; background:#c8f135; color:#0d0d0d; border:none; border-radius:30px; padding:11px 20px; font-size:14px; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif; cursor:pointer; text-align:left; margin-bottom:6px; text-decoration:none;}
.dark-sb-active svg { stroke:#0d0d0d !important; }

.dark-sb-item { display:flex; align-items:center; gap:10px; padding:10px 6px; font-size:14px; color:#888; font-weight:500; cursor:pointer; border-radius:8px; transition:color 0.15s; text-decoration:none; }
.dark-sb-item:hover { color:#ccc; }
.dark-sb-item svg { opacity:0.5; flex-shrink:0; stroke:#888 !important;}

.dark-sb-divider { border:none; border-top:1px solid #222; margin:16px 0; }

.dark-main { flex:1; padding:32px 36px; display:flex; flex-direction:column; gap:0; overflow-y:auto; overflow-x:hidden;}
.dark-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px; }
.dark-page-title { font-size:33px; font-weight:800; color:#ffffff; letter-spacing:-0.5px; line-height:1.15; margin-bottom:6px; }
.dark-page-sub { font-size:13.5px; color:#555; font-weight:400; }
.dark-month-pill { display:inline-flex; align-items:center; gap:8px; background:#c8f135; color:#0d0d0d; padding:10px 20px; border-radius:30px; font-size:13.5px; font-weight:700; border:none; cursor:pointer; white-space:nowrap; margin-top:4px; }

/* Global overrides for inner pages to match dark theme */
.dark-main .bg-white { background-color: #1a1a1a !important; border-color: #2a2a2a !important; }
.dark-main .bg-slate-800 { background-color: #111 !important; }
.dark-main .bg-gray-50, .dark-main .bg-gray-100, .dark-main .bg-slate-50, .dark-main .bg-slate-100 { background-color: #111111 !important; border-color: #2a2a2a !important; }
.dark-main .text-gray-800, .dark-main .text-gray-900, .dark-main .text-slate-800, .dark-main .text-slate-900 { color: #ffffff !important; }
.dark-main .text-gray-500, .dark-main .text-gray-600, .dark-main .text-gray-700, .dark-main .text-slate-500, .dark-main .text-slate-600, .dark-main .text-slate-700 { color: #aaaaaa !important; }
.dark-main .border-gray-100, .dark-main .border-gray-200, .dark-main .border-gray-300, .dark-main .border-slate-100, .dark-main .border-slate-200 { border-color: #2a2a2a !important; }
.dark-main .divide-y > :not([hidden]) ~ :not([hidden]) { border-color: #2a2a2a !important; }
.dark-main .shadow, .dark-main .shadow-sm, .dark-main .shadow-md { box-shadow: none !important; }
.dark-main input, .dark-main select, .dark-main textarea { background-color: #111111 !important; color: #fff !important; border-color: #2a2a2a !important; }
</style>
@stack('styles')
</head>
<body>
<div class="dark-shell">
    <aside class="dark-sidebar">
        <div x-data="{ open: false }" class="relative">
            <div @click="open = !open" class="dark-sb-user cursor-pointer hover:bg-[#1a1a1a] p-2 -ml-2 rounded-xl transition">
                <div class="dark-sb-avatar">
                    @if(auth()->user()->restaurant && auth()->user()->restaurant->logo)
                        <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><circle cx="13" cy="10" r="4" fill="rgba(255,255,255,0.85)"/><path d="M4 24c0-4.97 4.03-9 9-9s9 4.03 9 9" stroke="rgba(255,255,255,0.85)" stroke-width="1.8" stroke-linecap="round"/></svg>
                    @endif
                </div>
                <div class="dark-sb-user-text">
                    <div class="hi">Hello!</div>
                    <div class="name">{{ auth()->user()->name }}</div>
                </div>
            </div>
            
            <div x-show="open" @click.away="open = false" style="display: none;"
                class="absolute left-0 mt-1 w-48 bg-[#1e1e1e] rounded-xl shadow-xl border border-[#2a2a2a] z-50 overflow-hidden text-left">
                <a href="{{ route('admin.restaurant.index') }}" class="block px-4 py-3 text-sm text-[#ccc] hover:bg-[#2a2a2a] border-b border-[#2a2a2a]">Restaurant Profile</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-[#ccc] hover:bg-[#2a2a2a] border-b border-[#2a2a2a]">Update Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-3 text-sm text-red-400 hover:bg-red-400/10 font-bold">Logout</button>
                </form>
            </div>
        </div>

        <div class="dark-sb-search">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="6.5" cy="6.5" r="5" stroke="#888" stroke-width="1.6"/><path d="M10.5 10.5L13 13" stroke="#888" stroke-width="1.6" stroke-linecap="round"/></svg>
            <span>Search</span>
        </div>

        <div class="dark-sb-label">Dashboard</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 4l2-2 1 1 2-2 2 2 1-1 2 2" stroke="#888" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><rect x="1" y="4" width="12" height="9" rx="1.5" stroke="#888" stroke-width="1.3"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 13v-4M6 13V6M10 13V3M13 13V8" stroke="#888" stroke-width="1.3" stroke-linecap="round"/></svg>
            Analytics
        </a>
        <a href="{{ route('kitchen.dashboard') }}" class="{{ request()->routeIs('kitchen.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 13V8M7 13V3M11 13V6" stroke="#888" stroke-width="1.5" stroke-linecap="round"/></svg>
            Kitchen Dashboard
        </a>
        <a href="{{ route('admin.reservations.index') }}" class="{{ request()->routeIs('admin.reservations.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="2" width="12" height="10" rx="2" stroke="#888" stroke-width="1.3"/><path d="M1 5h12" stroke="#888" stroke-width="1.3"/></svg>
            Table Reservations
        </a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1 10L4 6l3 3 3-4 3 2" stroke="#888" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Orders
        </a>
        <a href="{{ route('admin.discounts.index') }}" class="{{ request()->routeIs('admin.discounts.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="4" cy="4" r="1.5" stroke="#888" stroke-width="1.3"/><circle cx="10" cy="10" r="1.5" stroke="#888" stroke-width="1.3"/><path d="M11 3L3 11" stroke="#888" stroke-width="1.3" stroke-linecap="round"/></svg>
            Coupons & Offers
        </a>
        <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 2h10a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1z" stroke="#888" stroke-width="1.3"/><path d="M5 6l2 2 4-4" stroke="#888" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Hot Deals & Ads
        </a>

        <hr class="dark-sb-divider">

        <a href="{{ route('admin.tables.index') }}" class="{{ request()->routeIs('admin.tables.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v2M7 11v2M1 7h2M11 7h2" stroke="#888" stroke-width="1.3" stroke-linecap="round"/><circle cx="7" cy="7" r="3.5" stroke="#888" stroke-width="1.3"/></svg>
            Tables & QR
        </a>
        <a href="{{ route('admin.menu-categories.index') }}" class="{{ request()->routeIs('admin.menu-categories.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="1" width="5" height="5" rx="1" stroke="#888" stroke-width="1.3"/><rect x="8" y="1" width="5" height="5" rx="1" stroke="#888" stroke-width="1.3"/><rect x="1" y="8" width="5" height="5" rx="1" stroke="#888" stroke-width="1.3"/><rect x="8" y="8" width="5" height="5" rx="1" stroke="#888" stroke-width="1.3"/></svg>
            Menu Categories
        </a>
        <a href="{{ route('admin.menu-items.index') }}" class="{{ request()->routeIs('admin.menu-items.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="4" width="12" height="8" rx="1.5" stroke="#888" stroke-width="1.3"/><path d="M4 4V3a3 3 0 016 0v1" stroke="#888" stroke-width="1.3"/></svg>
            Menu Items
        </a>
        <a href="{{ route('admin.restaurant.index') }}" class="{{ request()->routeIs('admin.restaurant.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="3" stroke="#888" stroke-width="1.3"/><path d="M7 1v1.5M7 11.5V13M1 7h1.5M11.5 7H13M2.75 2.75l1.06 1.06M10.19 10.19l1.06 1.06M2.75 11.25l1.06-1.06M10.19 3.81l1.06-1.06" stroke="#888" stroke-width="1.2" stroke-linecap="round"/></svg>
            Restaurant Settings
        </a>
        <a href="{{ route('admin.themes.index') }}" class="{{ request()->routeIs('admin.themes.*') ? 'dark-sb-active' : 'dark-sb-item' }}">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="#888" stroke-width="1.3"/><path d="M7 4v3l2 1.5" stroke="#888" stroke-width="1.3" stroke-linecap="round"/></svg>
            Themes
        </a>
    </aside>

    <div class="dark-main">
        <div class="dark-page-header">
            <div>
                <h1 class="dark-page-title">@yield('title', 'Dashboard Overview')</h1>
                <p class="dark-page-sub">Here's your overview for today.</p>
            </div>
            <div style="display:flex; align-items:center; gap:16px;">
                @include('components.header-notifications')
                <a href="{{ route('admin.orders.index') }}" class="dark-month-pill" style="text-decoration:none;">
                    View All Orders
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 5.5L7 9.5L11 5.5" stroke="#0d0d0d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-900 border border-emerald-500 text-emerald-100 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @yield('content')
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
