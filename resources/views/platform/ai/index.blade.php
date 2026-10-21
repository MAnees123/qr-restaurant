@extends('layouts.admin')
@section('title', 'Smart Tools')
@section('header', 'Smart Tools (AI features)')

@php $inp = 'w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold'; $card = 'bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8'; @endphp
@section('content')
@include('platform._errors')
<div class="max-w-5xl space-y-6">
    <div class="rounded-2xl bg-sky-50 border border-sky-100 text-sky-800 p-4 text-sm">
        <b>How this works:</b> these tools use statistics and text templates computed from your own orders.
        @if($settings->llmConfigured())
            An LLM endpoint is configured, so the description generator will try it first and fall back to templates on error.
        @else
            No LLM endpoint is configured, so nothing is sent to any external AI service.
        @endif
    </div>

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-4">Menu description generator</h2>
        <form action="{{ route('admin.ai.describe') }}" method="POST" class="grid sm:grid-cols-3 gap-4 items-end">@csrf
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Dish name</label><input name="name" required value="{{ old('name') }}" class="{{ $inp }}"></div>
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Category</label><input name="category" value="{{ old('category') }}" class="{{ $inp }}" placeholder="e.g. Dessert"></div>
            <button class="bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl">Generate</button>
        </form>
        @if(session('ai_description'))
            <p class="mt-4 p-4 bg-slate-50 rounded-xl text-slate-700">{{ session('ai_description')['text'] }}
                <span class="block text-xs text-slate-400 mt-1">Source: {{ session('ai_description')['source'] === 'llm' ? 'configured LLM' : 'template (no AI model)' }}</span></p>
        @endif
    </div>

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-4">Price suggestion</h2>
        <form action="{{ route('admin.ai.price') }}" method="POST" class="grid sm:grid-cols-4 gap-4 items-end">@csrf
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Ingredient cost</label><input type="number" step="0.01" name="cost" required value="{{ old('cost') }}" class="{{ $inp }}"></div>
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Target margin %</label><input type="number" step="1" name="margin" required value="{{ old('margin', $settings->target_margin) }}" class="{{ $inp }}"></div>
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Menu item (optional)</label>
                <select name="item_id" class="{{ $inp }}"><option value="">-</option>@foreach($items as $i)<option value="{{ $i->id }}" @selected(old('item_id') == $i->id)>{{ $i->name }}</option>@endforeach</select></div>
            <button class="bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl">Suggest</button>
        </form>
        @if($p = session('ai_price'))
            <div class="mt-4 p-4 bg-slate-50 rounded-xl text-sm text-slate-700 space-y-1">
                <p>Price for a {{ $p['margin'] }}% margin on cost {{ number_format($p['cost'], 2) }}: <b>{{ number_format($p['suggested'], 2) }}</b> (rounded: <b>{{ number_format($p['rounded'], 2) }}</b>)</p>
                @if($p['history'])<p class="text-slate-500">Last 30 days: {{ $p['history']['units_30d'] }} units sold at an average of {{ number_format($p['history']['avg_price'], 2) }}.</p>@endif
            </div>
        @endif
    </div>

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-1">Sales forecast, next 7 days</h2>
        <p class="text-xs text-slate-400 mb-4">7-day moving average of daily revenue (last 28 days). A simple statistical estimate, not a prediction guarantee.</p>
        @if($forecast['has_data'])
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($forecast['forecast'] as $f)
                <div class="bg-slate-50 rounded-xl p-3 text-center"><p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($f['date'])->format('D d M') }}</p><p class="font-black text-slate-800">{{ number_format($f['revenue'], 2) }}</p></div>
            @endforeach
            </div>
            <p class="mt-3 text-sm text-slate-600">Expected 7-day total: <b>{{ number_format($forecast['total'], 2) }}</b></p>
        @else
            <p class="text-slate-400 text-sm">Not enough order history yet.</p>
        @endif
    </div>

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-4">Frequently bought together</h2>
        @forelse($pairs as $p)
            <p class="text-sm py-1 border-b border-slate-100">{{ $p['a_name'] }} + {{ $p['b_name'] }} <span class="text-slate-400">({{ $p['together'] }} orders)</span></p>
        @empty
            <p class="text-slate-400 text-sm">No multi-item orders yet.</p>
        @endforelse
        <p class="text-xs text-slate-400 mt-3">JSON per item: <code>{{ route('admin.ai.recommendations') }}?item_id=ID</code> (requires admin login).</p>
    </div>

    <div class="{{ $card }}">
        <h2 class="font-black text-slate-800 text-lg mb-1">Optional LLM provider</h2>
        <p class="text-xs text-slate-400 mb-4">OpenAI-compatible chat completions endpoint (HTTPS). You must supply your own URL and API key; the key is stored encrypted. Leave empty to keep using templates only.</p>
        <form action="{{ route('admin.ai.settings') }}" method="POST" class="grid sm:grid-cols-2 gap-4">@csrf @method('PUT')
            <div class="sm:col-span-2"><label class="block text-xs font-black text-slate-500 uppercase mb-2">Endpoint URL</label><input name="llm_url" value="{{ old('llm_url', $settings->llm_url) }}" placeholder="https://api.example.com/v1/chat/completions" class="{{ $inp }}"></div>
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">API key {{ $settings->llm_key ? '(saved, leave blank to keep)' : '' }}</label><input type="password" name="llm_key" autocomplete="off" class="{{ $inp }}"></div>
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Model</label><input name="llm_model" value="{{ old('llm_model', $settings->llm_model) }}" class="{{ $inp }}"></div>
            <div><label class="block text-xs font-black text-slate-500 uppercase mb-2">Default target margin %</label><input type="number" name="target_margin" min="1" max="95" value="{{ old('target_margin', $settings->target_margin) }}" class="{{ $inp }}"></div>
            <label class="flex items-center gap-2 text-sm font-bold text-slate-600"><input type="checkbox" name="clear_key" value="1" class="rounded"> Remove saved key</label>
            <button class="bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl sm:col-span-2">Save settings</button>
        </form>
    </div>
</div>
@endsection
