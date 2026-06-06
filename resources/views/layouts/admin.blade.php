<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="h-screen flex">
        <!-- Sidebar -->

        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 min-h-screen border-r border-amber-500/20">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-amber-500">QR Order</h1>
                <p class="text-sm text-slate-400 mt-1">{{ auth()->user()->restaurant->name ?? 'Admin Panel' }}</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Dashboard</a>
                <a href="{{ route('kitchen.dashboard') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('kitchen.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Kitchen
                    Dashboard</a>
                {{-- <a href="{{ route('admin.reservations.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.reservations.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Table
                    Reservations</a> --}}
                <a href="{{ route('admin.orders.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.orders.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Orders</a>
                <a href="{{ route('admin.discounts.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.discounts.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Coupons
                    & Offers</a>
                <a href="{{ route('admin.tables.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.tables.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Tables
                    & QR</a>
                <a href="{{ route('admin.menu-categories.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.menu-categories.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Menu
                    Categories</a>
                <a href="{{ route('admin.menu-items.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.menu-items.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Menu
                    Items</a>
                <a href="{{ route('admin.restaurant.index') }}"
                    class="block px-6 py-3 hover:bg-slate-700 {{ request()->routeIs('admin.restaurant.*') ? 'bg-slate-700 border-l-4 border-amber-500' : '' }}">Restaurant
                    Settings</a>

                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="w-full text-left px-6 py-3 hover:bg-slate-700 text-red-400">Log
                        Out</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 h-screen overflow-x-hidden overflow-y-auto">
            <header class="sticky top-0 z-50 bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-3 items-center">
                        <div class="justify-self-start">
                            @if (!request()->routeIs('admin.dashboard'))
                                <button onclick="window.history.back()"
                                    class="flex items-center gap-2 text-slate-600 hover:text-amber-600 transition bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    <span class="font-medium">Back</span>
                                </button>
                            @endif
                        </div>
                        <div class="justify-self-center text-center">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                @yield('header', 'Admin Dashboard')
                            </h2>
                        </div>
                        <div class="justify-self-end flex items-center gap-4">
                            <!-- Waiter Call Notification Icon -->
                            <div x-data="globalNotifications()" x-init="initPolling()" class="relative">
                                <!-- Bell Trigger -->
                                <button @click="dropdownOpen = !dropdownOpen"
                                    class="relative p-2 rounded-xl transition-all duration-300"
                                    :class="calls.some(c => c.status === 'pending') ?
                                        'bg-amber-100 text-amber-600 animate-vibrate' : 'bg-slate-100 text-slate-400'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                        </path>
                                    </svg>

                                    <template x-if="calls.filter(c => c.status === 'pending').length > 0">
                                        <span
                                            class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-sm ring-2 ring-white"
                                            x-text="calls.filter(c => c.status === 'pending').length"></span>
                                    </template>
                                </button>

                                <!-- Notifications Dropdown -->
                                <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">

                                    <div
                                        class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Waiter
                                            Requests</h3>
                                        <span
                                            class="text-[10px] font-black px-2 py-0.5 rounded bg-amber-100 text-amber-700 uppercase"
                                            x-text="calls.length + ' Active'"></span>
                                    </div>

                                    <div class="max-h-[300px] overflow-y-auto">
                                        <template x-if="calls.length === 0">
                                            <div class="p-8 text-center">
                                                <div
                                                    class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 text-slate-200" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <p class="text-xs text-slate-400 font-bold italic">Not any request yet
                                                </p>
                                            </div>
                                        </template>

                                        <template x-for="call in calls" :key="call.id">
                                            <div
                                                class="p-4 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs"
                                                        :class="call.status === 'pending' ? 'bg-amber-100 text-amber-700' :
                                                            'bg-emerald-100 text-emerald-700'"
                                                        x-text="call.table.table_number"></div>
                                                    <div>
                                                        <p class="text-xs font-black text-slate-800"
                                                            x-text="call.status === 'pending' ? 'Assistance Needed' : 'Waiter on way'">
                                                        </p>
                                                        <p class="text-[10px] text-slate-400 font-bold uppercase"
                                                            x-text="formatTime(call.created_at)"></p>
                                                    </div>
                                                </div>
                                                <div class="flex gap-1">
                                                    <template x-if="call.status === 'pending'">
                                                        <button @click="acceptCall(call.id)" title="Accept Call"
                                                            class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </button>
                                                    </template>
                                                    <button @click="completeCall(call.id)" title="Mark as Completed"
                                                        class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ open: false }">
                                <div class="relative">
                                    <!-- Profile Image Trigger -->
                                    <button @click="open = !open"
                                        class="w-10 h-10 rounded-full border-2 border-white shadow-sm hover:shadow-md transition duration-300 overflow-hidden focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 bg-slate-100">
                                        @if (auth()->user()->restaurant && auth()->user()->restaurant->logo)
                                            <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}"
                                                alt="{{ auth()->user()->restaurant->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=059669&color=fff&bold=true"
                                                alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                        class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">

                                        <!-- User Info Header -->
                                        <div class="p-6 bg-slate-50 border-b border-slate-100">
                                            <p
                                                class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">
                                                Signed in as</p>
                                            <p class="text-sm font-black text-slate-800 truncate">
                                                {{ auth()->user()->name }}</p>
                                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}
                                            </p>
                                        </div>

                                        <!-- Links -->
                                        <div class="py-2">
                                            <a href="{{ route('admin.restaurant.index') }}"
                                                class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Restaurant Profile
                                            </a>
                                            <a href="{{ route('profile.edit') }}"
                                                class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                                    </path>
                                                </svg>
                                                Update Password
                                            </a>
                                        </div>

                                        <!-- Logout -->
                                        <div class="border-t border-slate-100 py-2 bg-slate-50">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full flex items-center gap-3 px-6 py-3 text-sm font-black text-red-500 hover:bg-red-50 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                        </path>
                                                    </svg>
                                                    Log Out
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @yield('header_actions')
                            </div>
                        </div>
                    </div>
            </header>

            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    <style>
        @keyframes vibrate {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(5deg);
            }

            50% {
                transform: rotate(0deg);
            }

            75% {
                transform: rotate(-5deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .animate-vibrate {
            animation: vibrate 0.2s linear infinite;
        }
    </style>
    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function globalNotifications() {
            return {
                calls: [],
                dropdownOpen: false,
                pollingInterval: null,
                initPolling() {
                    this.fetchCalls();
                    this.pollingInterval = setInterval(() => this.fetchCalls(), 5000);
                },
                fetchCalls() {
                    axios.get('{{ route('admin.table-calls.index') }}')
                        .then(response => {
                            this.calls = response.data;
                        })
                        .catch(error => console.error('Notification Error:', error));
                },
                acceptCall(id) {
                    axios.post(`/admin/table-calls/${id}/accept`)
                        .then(() => {
                            this.fetchCalls();
                        })
                        .catch(error => console.error('Error accepting call:', error));
                },
                completeCall(id) {
                    axios.post(`/admin/table-calls/${id}/complete`)
                        .then(() => {
                            this.calls = this.calls.filter(c => c.id !== id);
                        })
                        .catch(error => console.error('Error completing call:', error));
                },
                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        }
    </script>
</body>

</html>
