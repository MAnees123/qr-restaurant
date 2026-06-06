<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kitchen Panel - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="h-screen flex overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-emerald-900 text-white flex-shrink-0 sticky top-0 self-start h-screen overflow-y-auto">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-amber-400">Kitchen KOT</h1>
                <p class="text-xs text-emerald-300 mt-1 uppercase tracking-widest font-bold">
                    {{ auth()->user()->restaurant->name }}</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('kitchen.dashboard') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->routeIs('kitchen.dashboard') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">Kitchen
                    Overview</a>
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->routeIs('kitchen.dashboard') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">Admin
                    Dashboard</a>

                <div class="px-6 py-2 text-xs font-bold text-emerald-400 uppercase mt-4">Order Status</div>
                <a href="{{ route('kitchen.orders.status', 'pending') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->is('kitchen/orders/pending') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Pending Orders
                    </span>
                </a>
                <a href="{{ route('kitchen.orders.status', 'preparing') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->is('kitchen/orders/preparing') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Preparing
                    </span>
                </a>
                <a href="{{ route('kitchen.orders.status', 'ready') }}"
                    class="block px-6 py-3 hover:bg-emerald-800 {{ request()->is('kitchen/orders/ready') ? 'bg-emerald-800 border-l-4 border-amber-400' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Ready to Serve
                    </span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-12">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-6 py-3 hover:bg-emerald-800 text-red-300 font-medium">Log
                        Out</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 h-screen overflow-x-hidden overflow-y-auto">
            <header class="sticky top-0 z-20 bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h2 class="font-bold text-xl text-emerald-900 leading-tight">
                        @yield('header', 'hboard')
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <span class="text-sm font-bold text-emerald-600">LIVE CONNECTION</span>
                    </div>
                </div>
            </header>

            <div class="p-6">
                @if (session('success'))
                    <div
                        class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Set axios CSRF header from meta token
        (function() {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        })();
    </script>
    @yield('scripts')
</body>

</html>
