@extends('layouts.admin')
@section('title', 'Expenses')
@section('header', 'Expenses')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-end">
    <form method="GET" class="flex gap-2 items-end"><input type="month" name="month" value="{{ $month }}" class="rounded-xl border-slate-200 text-sm font-bold"><button class="bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-sm">Show</button></form>
    <a href="{{ route('admin.expenses.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg shadow-emerald-100">New Expense</a>
</div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-slate-50/50 border-b">@foreach(['Date','Category','Vendor','Amount',''] as $h)<th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">{{ $h }}</th>@endforeach</tr></thead>
            <tbody class="divide-y">
            @forelse($expenses as $e)
                <tr><td class="px-6 py-3 text-sm">{{ $e->expense_date->format('d M') }}</td>
                    <td class="px-6 py-3 text-sm font-bold">{{ $e->category->name ?? 'Uncategorised' }}<br><span class="text-xs font-normal text-slate-400">{{ $e->notes }}</span></td>
                    <td class="px-6 py-3 text-sm text-slate-500">{{ $e->supplier->name ?? '-' }}</td>
                    <td class="px-6 py-3 font-black">Rs {{ number_format($e->amount, 2) }}</td>
                    <td class="px-6 py-3 whitespace-nowrap space-x-2"><a href="{{ route('admin.expenses.edit', $e) }}" class="text-blue-500 font-black text-sm">Edit</a>
                        <form action="{{ route('admin.expenses.destroy', $e) }}" method="POST" class="inline" data-confirm="Delete this expense?">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm">Delete</button></form></td></tr>
            @empty<tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No expenses this month.</td></tr>@endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $expenses->links() }}</div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-[2rem] shadow-sm border p-6">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Monthly summary</p>
            <p class="text-3xl font-black text-slate-800 mb-3">Rs {{ number_format($monthTotal, 2) }}</p>
            @foreach($summary as $s)<div class="flex justify-between text-sm py-1"><span>{{ $s->category }}</span><b>Rs {{ number_format($s->total, 2) }}</b></div>@endforeach
        </div>
        <div class="bg-white rounded-[2rem] shadow-sm border p-6">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Categories</p>
            <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="flex gap-2 mb-3">@csrf<input name="name" required maxlength="100" placeholder="New category" class="flex-1 rounded-xl border-slate-200 text-sm"><button class="bg-slate-800 text-white font-bold px-4 rounded-xl text-sm">Add</button></form>
            @php $cats = \App\Models\Finance\ExpenseCategory::where('restaurant_id', auth()->user()->restaurant_id)->orderBy('name')->get(); @endphp
            @foreach($cats as $c)<div class="flex justify-between text-sm py-1"><span>{{ $c->name }}</span>
                <form method="POST" action="{{ route('admin.expense-categories.destroy', $c) }}" data-confirm="Delete category {{ $c->name }}? Its expenses become uncategorised.">@csrf @method('DELETE')<button class="text-red-500 font-black">&times;</button></form></div>@endforeach
        </div>
    </div>
</div>
@endsection
