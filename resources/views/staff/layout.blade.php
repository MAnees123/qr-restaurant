<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Staff Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">
    <header class="bg-white border-b border-slate-200 shadow-sm px-4 py-3 flex justify-between items-center sticky top-0 z-10">
        <h1 class="font-semibold text-lg text-slate-800">@yield('header', 'Staff Panel')</h1>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn btn-sm btn-ghost">Logout</button>
        </form>
    </header>
    <main class="max-w-3xl mx-auto p-4 sm:p-6">
        @if(session('success'))<div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl p-4 text-sm font-medium">{{ session('success') }}</div>@endif
        @include('staff._errors')
        @yield('content')
    </main>
</body>
</html>
