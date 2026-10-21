@extends('layouts.admin')
@section('title', 'Import / Export')
@section('header', 'Import / Export')

@section('content')
@include('platform._errors')
<div class="max-w-5xl grid md:grid-cols-2 gap-6">
@foreach($entities as $key => $e)
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
        <h2 class="font-black text-slate-800 text-lg mb-1">{{ $e['label'] }}</h2>
        <p class="text-xs text-slate-400 mb-4 font-mono">{{ implode(', ', $e['columns']) }}</p>
        <div class="flex gap-4 text-sm font-bold mb-4">
            <a href="{{ route('admin.data.export', $key) }}" class="text-emerald-600">Export CSV</a>
            <a href="{{ route('admin.data.template', $key) }}" class="text-blue-500">Download template</a>
        </div>
        <form action="{{ route('admin.data.preview', $key) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-center">
            @csrf
            <input type="file" name="file" accept=".csv,.txt" required class="text-sm">
            <button class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-4 rounded-xl text-sm">Preview import</button>
        </form>
    </div>
@endforeach
</div>
@endsection
