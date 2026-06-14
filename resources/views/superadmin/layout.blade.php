<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin • @yield('title', 'Dashboard')</title>

    <!-- Tailwind CSS (via Vite & CDN Fallback) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false, // Turn off preflight to prevent conflict with custom dashboard styles
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ── Reset & Base ───────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Dark Theme (Default) ───────────────────────────── */
        [data-theme="dark"] {
            --bg-main:    #0f1117;
            --bg-card:    #1a1d27;
            --bg-sidebar: #13161f;
            --border:     rgba(255,255,255,.07);
            --accent:     #6c63ff;
            --accent2:    #a78bfa;
            --text:       #e2e8f0;
            --muted:      #94a3b8;
            --success:    #22c55e;
            --warning:    #f59e0b;
            --danger:     #ef4444;
            --radius:     12px;
            --topbar-bg:  #1a1d27;
        }

        /* ── Light Theme ────────────────────────────────────── */
        [data-theme="light"] {
            --bg-main:    #f1f5f9;
            --bg-card:    #ffffff;
            --bg-sidebar: #ffffff;
            --border:     rgba(0,0,0,.08);
            --accent:     #6c63ff;
            --accent2:    #7c3aed;
            --text:       #1e293b;
            --muted:      #64748b;
            --success:    #16a34a;
            --warning:    #d97706;
            --danger:     #dc2626;
            --radius:     12px;
            --topbar-bg:  #ffffff;
        }

        html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text); transition: background .3s, color .3s; }

        /* ── Layout Shell ───────────────────────────────────── */
        .shell { display: flex; min-height: 100vh; }

        /* ── Sidebar ────────────────────────────────────────── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 50;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
            transition: background .3s, border-color .3s;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-logo .icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }
        .sidebar-logo span { font-weight: 700; font-size: 15px; color: var(--text); }
        .sidebar-logo small { display: block; font-size: 10px; color: var(--muted); font-weight: 400; }

        .sidebar-nav { padding: 12px 0; flex: 1; }

        .nav-section { padding: 18px 20px 6px; font-size: 10px; font-weight: 600; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s ease;
        }
        .nav-link:hover { color: var(--text); background: rgba(108,99,255,.06); }
        .nav-link.active { color: var(--accent); background: rgba(108,99,255,.1); border-left-color: var(--accent); font-weight: 600; }
        .nav-link i { width: 18px; text-align: center; font-size: 14px; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }
        .sidebar-footer a {
            display: flex; align-items: center; gap: 8px;
            color: var(--muted); font-size: 13px; text-decoration: none;
            transition: color .15s;
        }
        .sidebar-footer a:hover { color: var(--danger); }

        /* ── Main Content ───────────────────────────────────── */
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Topbar ─────────────────────────────────────────── */
        .topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 40;
            transition: background .3s, border-color .3s;
        }
        .topbar-title { font-size: 18px; font-weight: 700; color: var(--text); }
        .topbar-actions { display: flex; align-items: center; gap: 14px; }
        .topbar-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
        }
        .topbar-name { font-size: 13px; color: var(--muted); }

        /* ── Theme Toggle Button ────────────────────────────── */
        .theme-toggle {
            width: 40px; height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: rgba(108,99,255,.08);
            color: var(--accent2);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            transition: all .2s ease;
        }
        .theme-toggle:hover {
            background: rgba(108,99,255,.18);
            transform: rotate(15deg);
        }
        [data-theme="dark"] .theme-toggle .fa-moon { display: none; }
        [data-theme="dark"] .theme-toggle .fa-sun  { display: inline; }
        [data-theme="light"] .theme-toggle .fa-sun  { display: none; }
        [data-theme="light"] .theme-toggle .fa-moon { display: inline; }

        /* ── Impersonation Banner ───────────────────────────── */
        .impersonate-bar {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            color: #1c1917;
            padding: 10px 32px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .impersonate-bar a { color: #1c1917; font-weight: 700; text-decoration: underline; }

        /* ── Page Content ───────────────────────────────────── */
        .page-content { padding: 32px; flex: 1; }

        /* ── Alert Flashes ──────────────────────────────────── */
        .flash {
            padding: 12px 18px; border-radius: var(--radius);
            margin-bottom: 20px; font-size: 13.5px; font-weight: 500;
        }
        .flash-success { background: rgba(34,197,94,.15); color: #4ade80; border: 1px solid rgba(34,197,94,.2); }
        .flash-error   { background: rgba(239,68,68,.15);  color: #f87171; border: 1px solid rgba(239,68,68,.2); }
        .flash-info    { background: rgba(108,99,255,.15); color: var(--accent2); border: 1px solid rgba(108,99,255,.2); }

        /* ── Cards ──────────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            transition: background .3s, border-color .3s;
        }
        .card-header { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; }

        /* ── Buttons ────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            border: none; text-decoration: none; transition: all .15s ease;
        }
        .btn-primary   { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #574fd6; }
        .btn-danger    { background: rgba(239,68,68,.15); color: #f87171; border: 1px solid rgba(239,68,68,.25); }
        .btn-danger:hover { background: rgba(239,68,68,.25); }
        .btn-secondary { background: rgba(108,99,255,.06); color: var(--muted); border: 1px solid var(--border); }
        .btn-secondary:hover { background: rgba(108,99,255,.12); color: var(--text); }
        .btn-success   { background: rgba(34,197,94,.15); color: #4ade80; border: 1px solid rgba(34,197,94,.25); }
        .btn-success:hover { background: rgba(34,197,94,.25); }
        .btn-sm { padding: 5px 11px; font-size: 12px; }

        /* ── Tables ─────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th { padding: 10px 14px; background: rgba(108,99,255,.04); color: var(--muted); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; text-align: left; border-bottom: 1px solid var(--border); }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(108,99,255,.04); }
        tbody td { padding: 12px 14px; color: var(--text); vertical-align: middle; }

        /* ── Badges ─────────────────────────────────────────── */
        .badge { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: rgba(34,197,94,.15); color: #22c55e; }
        .badge-danger  { background: rgba(239,68,68,.15);  color: #ef4444; }
        .badge-warning { background: rgba(245,158,11,.15); color: #f59e0b; }
        .badge-muted   { background: rgba(108,99,255,.06); color: var(--muted); }
        .badge-purple  { background: rgba(108,99,255,.15); color: var(--accent2); }

        /* ── Forms ──────────────────────────────────────────── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
        .form-control {
            width: 100%; padding: 10px 14px;
            background: rgba(108,99,255,.04); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text); font-size: 13.5px;
            outline: none; font-family: 'Inter', sans-serif;
            transition: border-color .15s, background .3s;
        }
        .form-control:focus { border-color: var(--accent); background: rgba(108,99,255,.08); }
        .form-control option { background: var(--bg-card); color: var(--text); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .is-invalid { border-color: var(--danger) !important; }
        .invalid-feedback { color: #ef4444; font-size: 11.5px; margin-top: 4px; }

        /* ── Stat Grid ──────────────────────────────────────── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s, background .3s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.15); }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
        }
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px; margin-bottom: 12px; }
        .stat-value { font-size: 26px; font-weight: 800; color: var(--text); }
        .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; margin-top: 2px; }

        /* ── Pagination ─────────────────────────────────────── */
        .pagination { display: flex; gap: 4px; margin-top: 16px; justify-content: center; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 7px; font-size: 12px; color: var(--muted); border: 1px solid var(--border); text-decoration: none; }
        .pagination a:hover { border-color: var(--accent); color: var(--text); }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* ── Toggle Switch ──────────────────────────────────── */
        .toggle { position: relative; display: inline-block; width: 42px; height: 22px; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer;
            inset: 0; background: rgba(108,99,255,.15);
            border-radius: 22px; transition: .3s;
        }
        .toggle-slider::before {
            content: ''; position: absolute;
            width: 16px; height: 16px; left: 3px; bottom: 3px;
            background: var(--muted); border-radius: 50%; transition: .3s;
        }
        .toggle input:checked + .toggle-slider { background: var(--accent); }
        .toggle input:checked + .toggle-slider::before { transform: translateX(20px); background: #fff; }

        /* ── Empty State ────────────────────────────────────── */
        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--muted);
        }
        .empty-state i { font-size: 40px; margin-bottom: 16px; opacity: .3; display: block; }
        .empty-state a { color: var(--accent2); }

        /* ── Responsive ─────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { width: 220px; transform: translateX(-100%); }
            .main { margin-left: 0; }
            .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Impersonation Banner ───────────────────────── --}}
@if(session()->has('impersonator_id'))
<div class="impersonate-bar">
    <span><i class="fa-solid fa-user-secret"></i> You are currently impersonating a restaurant admin.</span>
    <a href="{{ route('superadmin.tenants.leave-impersonate') }}">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> Return to Super Admin
    </a>
</div>
@endif

<div class="shell">

    {{-- ── Sidebar ──────────────────────────────────── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="icon"><i class="fa-solid fa-shield-halved" style="color:#fff"></i></div>
            <div>
                <span>Super Admin</span>
                <small>SaaS Control Panel</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Main</div>
            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>

            <div class="nav-section">Tenants</div>
            <a href="{{ route('superadmin.tenants.index') }}" class="nav-link {{ request()->routeIs('superadmin.tenants.index') || request()->routeIs('superadmin.tenants.show') || request()->routeIs('superadmin.tenants.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i> Restaurants
            </a>
            <a href="{{ route('superadmin.tenants.create') }}" class="nav-link {{ request()->routeIs('superadmin.tenants.create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus-circle"></i> Add Restaurant
            </a>

            <div class="nav-section">Subscriptions</div>
            <a href="{{ route('superadmin.plans.index') }}" class="nav-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Plans & Pricing
            </a>
            <a href="{{ route('superadmin.billing.index') }}" class="nav-link {{ request()->routeIs('superadmin.billing.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar"></i> Billing
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('superadmin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('superadmin.activity-logs.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Activity Logs
            </a>
            <a href="{{ route('superadmin.analytics.index') }}" class="nav-link {{ request()->routeIs('superadmin.analytics.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Analytics
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </aside>

    {{-- ── Main Area ────────────────────────────────── --}}
    <div class="main">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="topbar-title">@yield('title', 'Super Admin Dashboard')</div>
            <div class="topbar-actions">
                {{-- Dark / Light Mode Toggle --}}
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light mode" id="themeBtn">
                    <i class="fa-solid fa-sun"></i>
                    <i class="fa-solid fa-moon"></i>
                </button>

                <a href="{{ route('superadmin.profile.edit') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;" class="topbar-profile-link">
                    <span class="topbar-name">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border);">
                    @else
                        <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    @endif
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || session('info'))
        <div class="page-content" style="padding-bottom:0;">
            @if(session('success'))
                <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash flash-error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="flash flash-info"><i class="fa-solid fa-circle-info"></i> {{ session('info') }}</div>
            @endif
        </div>
        @endif

        {{-- Page Content --}}
        <div class="page-content">
            @yield('content')
        </div>
    </div>

</div>

<script>
    // ── Theme Toggle ──────────────────────────────────
    function toggleTheme() {
        const html = document.documentElement;
        const current = html.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('sa-theme', next);
    }

    // Restore saved theme on page load
    (function() {
        const saved = localStorage.getItem('sa-theme');
        if (saved) {
            document.documentElement.setAttribute('data-theme', saved);
        }
    })();

    // Auto-hide flash messages after 4 seconds
    setTimeout(() => {
        document.querySelectorAll('.flash').forEach(el => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);
</script>

@stack('scripts')
</body>
</html>
