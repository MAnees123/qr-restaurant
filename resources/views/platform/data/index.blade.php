@extends('layouts.admin')
@section('title', 'Import / Export')
@section('header', 'Import / Export')

@section('content')
@include('platform._errors')
<div class="max-w-5xl grid md:grid-cols-2 gap-6">
@foreach($entities as $key => $e)
    <div class="card card-hover p-6">
        <h2 class="font-semibold text-slate-800 text-lg mb-1">{{ $e['label'] }}</h2>
        <p class="text-xs text-slate-400 mb-4 font-mono">{{ implode(', ', $e['columns']) }}</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <a href="{{ route('admin.data.export', $key) }}" class="btn btn-sm btn-primary">Export CSV</a>
            <a href="{{ route('admin.data.template', $key) }}" class="btn btn-sm btn-secondary">Download template</a>
        </div>
        <form action="{{ route('admin.data.preview', $key) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-center">
            @csrf
            <input type="file" name="file" accept=".csv,.txt" required class="text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
            <button class="btn btn-sm btn-secondary">Preview import</button>
        </form>
    </div>
@endforeach
</div>
@endsection
