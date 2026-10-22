@extends('layouts.admin')
@section('title', 'Staff Accounts')
@section('header', 'Staff Accounts')
@section('content')
@include('operations._alerts')
<div class="space-y-6">
<div class="flex flex-wrap justify-between items-center gap-3">
    <p class="text-slate-500">Create waiter and cashier logins. Staff land on their own panel after signing in.</p>
    <a href="{{ route('admin.staff-accounts.create') }}" class="btn btn-primary">New Staff Account</a>
</div>
<div class="card overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wide">
            <th class="px-6 py-4">Name</th>
            <th class="px-6 py-4">Email</th>
            <th class="px-6 py-4">Role</th>
            <th class="px-6 py-4 text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($staff as $m)
            <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="px-6 py-4 font-semibold text-slate-800">{{ $m->name }}</td>
                <td class="px-6 py-4 text-slate-500">{{ $m->email }}</td>
                <td class="px-6 py-4"><x-ui.badge type="info" class="uppercase">{{ $m->staff_role }}</x-ui.badge></td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end items-center gap-2">
                    <a href="{{ route('admin.staff-accounts.edit', $m) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('admin.staff-accounts.destroy', $m) }}" method="POST" class="inline" data-confirm="Delete this staff account?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ghost text-red-600">Delete</button>
                    </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4"><x-ui.empty-state title="No staff accounts yet" message="Create a waiter or cashier login to get started."/></td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $staff->links() }}</div>
</div>
</div>
@endsection
