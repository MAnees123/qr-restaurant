@extends('layouts.admin')
@section('title', 'Staff Accounts')
@section('header', 'Staff Accounts')
@section('content')
@include('operations._alerts')
<div class="mb-6 flex flex-wrap justify-between items-center gap-3">
    <p class="text-slate-500">Create waiter and cashier logins. Staff land on their own panel after signing in.</p>
    <a href="{{ route('admin.staff-accounts.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl transition shadow-lg shadow-emerald-100">New Staff Account</a>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left">
        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Name</th>
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Email</th>
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Role</th>
            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($staff as $m)
            <tr>
                <td class="px-6 py-4 font-bold text-slate-800">{{ $m->name }}</td>
                <td class="px-6 py-4 text-slate-500">{{ $m->email }}</td>
                <td class="px-6 py-4"><span class="bg-amber-100 text-amber-700 text-[10px] font-black uppercase px-2 py-1 rounded-md tracking-wider">{{ $m->staff_role }}</span></td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.staff-accounts.edit', $m) }}" class="text-blue-500 hover:text-blue-700 font-black text-sm">Edit</a>
                    <form action="{{ route('admin.staff-accounts.destroy', $m) }}" method="POST" class="inline" data-confirm="Delete this staff account?">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 font-black text-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">No staff accounts yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $staff->links() }}</div>
</div>
@endsection
