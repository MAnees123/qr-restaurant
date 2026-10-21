@extends('layouts.admin')
@section('title', 'Import preview')
@section('header', 'Import preview: ' . $label)

@section('content')
<div class="max-w-6xl space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600"><b>{{ $valid }}</b> of {{ count($rows) }} rows are valid. Rows with errors will be skipped. Existing records with the same key are updated.</p>
        <div class="flex gap-3">
            <a href="{{ route('admin.data.index') }}" class="px-4 py-2 rounded-xl border text-sm font-bold text-slate-600">Cancel</a>
            @if($valid > 0)
            <form action="{{ route('admin.data.commit', $entity) }}" method="POST">
                @csrf <input type="hidden" name="token" value="{{ $token }}">
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl text-sm">Import {{ $valid }} valid rows</button>
            </form>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead><tr class="bg-slate-50">
                <th class="px-3 py-3 text-xs font-black text-slate-400 uppercase">Line</th>
                @foreach($columns as $c)<th class="px-3 py-3 text-xs font-black text-slate-400 uppercase">{{ $c }}</th>@endforeach
                <th class="px-3 py-3 text-xs font-black text-slate-400 uppercase">Result</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @foreach($rows as $r)
                <tr class="{{ $r['errors'] ? 'bg-red-50' : '' }}">
                    <td class="px-3 py-2 text-slate-400">{{ $r['line'] }}</td>
                    @foreach($columns as $c)<td class="px-3 py-2">{{ $r['data'][$c] ?? '' }}</td>@endforeach
                    <td class="px-3 py-2">@if($r['errors'])<span class="text-red-600 font-semibold">{{ implode(' ', $r['errors']) }}</span>@else<span class="text-emerald-600 font-bold">OK</span>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
