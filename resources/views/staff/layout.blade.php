<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Staff Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen">
    <header class="bg-white border-b border-slate-100 px-4 py-4 flex justify-between items-center">
        <h1 class="font-black text-slate-800">@yield('header', 'Staff Panel')</h1>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="text-sm font-bold text-slate-500 hover:text-red-500">Logout</button>
        </form>
    </header>
    <main class="max-w-3xl mx-auto p-4">
        @if(session('success'))<div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl p-4 text-sm font-bold">{{ session('success') }}</div>@endif
        @include('staff._errors')
        @yield('content')
    </main>
</body>
</html>
