@extends('layouts.admin')
@section('title', 'Expenses')
@section('header', 'Expenses')
@section('content')
@include('finance.partials.errors')
<div class="mb-6 flex flex-wrap gap-3 justify-between items-end">
    <form method="GET" class="flex gap-2 items-end"><input type="month" name="month" value="{{ $month }}" class="input w-auto"><button class="btn btn-secondary">Show</button></form>
    <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">New Expense</a>
</div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr>@foreach(['Date','Category','Vendor','Amount',''] as $h)<th class="px-6 py-4 {{ $h === 'Amount' ? 'text-right' : '' }}">{{ $h }}</th>@endforeach</tr></thead>
            <tbody>
            @forelse($expenses as $e)
                <tr><td class="px-6 py-4 text-sm">{{ $e->expense_date->format('d M') }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ $e->category->name ?? 'Uncategorised' }}<br><span class="text-xs font-normal text-slate-400">{{ $e->notes }}</span></td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $e->supplier->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-semibold whitespace-nowrap">Rs {{ number_format($e->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right"><div class="flex items-center justify-end gap-2"><a href="{{ route('admin.expenses.edit', $e) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('admin.expenses.destroy', $e) }}" method="POST" class="inline" data-confirm="Delete this expense?">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600">Delete</button></form></div></td></tr>
            @empty<tr><td colspan="5"><x-ui.empty-state title="No expenses this month" /></td></tr>@endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $expenses->links() }}</div>
    </div>
    <div class="space-y-6">
        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Monthly summary</p>
            <p class="text-3xl font-bold text-slate-800 mb-3">Rs {{ number_format($monthTotal, 2) }}</p>
            @foreach($summary as $s)<div class="flex justify-between text-sm py-1.5 border-t border-slate-100"><span>{{ $s->category }}</span><b class="font-semibold">Rs {{ number_format($s->total, 2) }}</b></div>@endforeach
        </div>
        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Categories</p>
            <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="flex gap-2 mb-3">@csrf<input name="name" required maxlength="100" placeholder="New category" class="input flex-1"><button class="btn btn-primary btn-sm">Add</button></form>
            @php $cats = \App\Models\Finance\ExpenseCategory::where('restaurant_id', auth()->user()->restaurant_id)->orderBy('name')->get(); @endphp
            @foreach($cats as $c)<div class="flex justify-between items-center text-sm py-1"><span>{{ $c->name }}</span>
                <form method="POST" action="{{ route('admin.expense-categories.destroy', $c) }}" data-confirm="Delete category {{ $c->name }}? Its expenses become uncategorised.">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600">&times;</button></form></div>@endforeach
        </div>
    </div>
</div>
@endsection
