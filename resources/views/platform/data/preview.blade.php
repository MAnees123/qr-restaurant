@extends('layouts.admin')
@section('title', 'Import preview')
@section('header', 'Import preview: ' . $label)

@section('content')
<div class="max-w-6xl space-y-4">
    <div class="card p-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600"><b>{{ $valid }}</b> of {{ count($rows) }} rows are valid. Rows with errors will be skipped. Existing records with the same key are updated.</p>
        <div class="flex gap-3">
            <a href="{{ route('admin.data.index') }}" class="btn btn-secondary">Cancel</a>
            @if($valid > 0)
            <form action="{{ route('admin.data.commit', $entity) }}" method="POST">
                @csrf <input type="hidden" name="token" value="{{ $token }}">
                <button class="btn btn-primary">Import {{ $valid }} valid rows</button>
            </form>
            @endif
        </div>
    </div>
    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead><tr>
                <th class="px-4 py-3">Line</th>
                @foreach($columns as $c)<th class="px-4 py-3">{{ $c }}</th>@endforeach
                <th class="px-4 py-3">Result</th>
            </tr></thead>
            <tbody>
            @foreach($rows as $r)
                <tr class="{{ $r['errors'] ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 text-slate-400">{{ $r['line'] }}</td>
                    @foreach($columns as $c)<td class="px-4 py-3">{{ $r['data'][$c] ?? '' }}</td>@endforeach
                    <td class="px-4 py-3">@if($r['errors'])<x-ui.badge type="danger">{{ implode(' ', $r['errors']) }}</x-ui.badge>@else<x-ui.badge type="success">OK</x-ui.badge>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
