<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Panel') - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 min-h-screen">
    @php $u = auth()->user(); $isWaiter = $u->staff_role === 'waiter'; @endphp
    <header class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-lg font-black text-amber-400">{{ $isWaiter ? 'Waiter Panel' : 'Cashier' }}</div>
                <div class="text-[11px] uppercase tracking-widest text-slate-400 font-bold">{{ $u->restaurant->name ?? '' }} &middot; {{ $u->name }}</div>
            </div>
            <nav class="flex items-center gap-2 text-sm font-bold">
                @if ($isWaiter)
                    <a href="{{ route('staff.waiter.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('staff.waiter.*') ? 'bg-slate-800' : '' }}">Tables &amp; Orders</a>
                @else
                    <a href="{{ route('staff.cashier.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('staff.cashier.index', 'staff.cashier.orders.*') ? 'bg-slate-800' : '' }}">Open Bills</a>
                    <a href="{{ route('staff.cashier.summary') }}" class="px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('staff.cashier.summary') ? 'bg-slate-800' : '' }}">Daily Summary</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700">Logout</button>
                </form>
            </nav>
        </div>
    </header>
    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
        @endif
        @include('operations._alerts')
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
