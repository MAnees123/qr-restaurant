@extends('layouts.admin')
@section('title', 'API Documentation')
@section('header', 'API Documentation')

@php
    $base = url('/api/v1');
    $endpoints = [
        ['GET', '/categories', 'read', 'List menu categories.'],
        ['GET', '/items?category_id=ID', 'read', 'List menu items (optional category filter).'],
        ['GET', '/tables', 'read', 'List tables.'],
        ['GET', '/orders?status=pending&per_page=25', 'read', 'Paginated orders, newest first.'],
        ['GET', '/orders/{id}', 'read', 'One order with its items.'],
        ['PATCH', '/orders/{id}/status', 'write', 'Body: status = pending|confirmed|preparing|ready|served|cancelled.'],
    ];
@endphp

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="card p-6 sm:p-8 space-y-3 text-sm text-slate-600">
        <p>Base URL: <code class="font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $base }}</code></p>
        <p>Authenticate with <code class="font-mono bg-slate-100 px-2 py-0.5 rounded">Authorization: Bearer &lt;token&gt;</code>. Tokens are scoped to this restaurant. Limit: 120 requests/minute per token. Errors are JSON ({"error": "..."}).</p>
    </div>
    @foreach($endpoints as [$method, $path, $scope, $desc])
    <div class="card card-hover p-5">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <x-ui.badge :type="$method === 'GET' ? 'success' : 'warning'">{{ $method }}</x-ui.badge>
            <code class="font-mono font-bold text-slate-800 break-all">/api/v1{{ $path }}</code>
            <span class="text-xs text-slate-400">scope: {{ $scope }}</span>
        </div>
        <p class="text-sm text-slate-500 mb-3">{{ $desc }}</p>
        <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto">@if($method === 'GET')curl -H "Authorization: Bearer YOUR_TOKEN" -H "Accept: application/json" "{{ $base }}{{ $path }}"@else
curl -X PATCH -H "Authorization: Bearer YOUR_TOKEN" -H "Accept: application/json" \
     -d "status=preparing" "{{ $base }}{{ $path }}"@endif</pre>
    </div>
    @endforeach
</div>
@endsection
