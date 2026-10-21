@extends('layouts.admin')
@section('title', 'Backups')
@section('header', 'Backups')

@section('content')
@include('platform._errors')
<div class="max-w-4xl space-y-6">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8 flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-slate-500 max-w-xl">A backup is a JSON snapshot of this restaurant's data (menu, tables, orders, discounts, banners and other restaurant-owned records). User accounts and billing are not included. Restoring <b>replaces</b> the current restaurant data with the snapshot.</p>
        <form action="{{ route('admin.backups.store') }}" method="POST">
            @csrf
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl transition">Create backup now</button>
        </form>
    </div>
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                @foreach(['File','Created','Size',''] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($backups as $b)
                <tr>
                    <td class="px-6 py-4 font-mono text-sm text-slate-700">{{ $b['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ \Carbon\Carbon::createFromTimestamp($b['created'])->format('Y-m-d H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ number_format($b['size'] / 1024, 1) }} KB</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-3">
                        <a href="{{ route('admin.backups.download', $b['name']) }}" class="text-blue-500 font-black text-sm">Download</a>
                        <form action="{{ route('admin.backups.restore', $b['name']) }}" method="POST" class="inline" data-confirm="Restore this backup? All current restaurant data will be replaced.">
                            @csrf
                            <input type="hidden" name="confirm" value="RESTORE">
                            <button type="submit" class="text-amber-600 font-black text-sm">Restore</button>
                        </form>
                        <form action="{{ route('admin.backups.destroy', $b['name']) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 font-black text-sm" data-confirm="Delete this backup file?">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">No backups yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-xs text-slate-400">Automatic backups: run <code>php artisan backups:run</code> from the server scheduler (cron) if you want them daily.</p>
</div>
@endsection
