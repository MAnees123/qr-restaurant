@extends('layouts.admin')
@section('title', 'Roles & Permissions')
@section('header', 'Roles & Permissions')
@section('content')
@include('staff._errors')
<div class="mb-6 flex justify-between items-center">
    <p class="text-slate-500">Create custom roles and assign them to your staff.</p>
    <a href="{{ route('admin.staff-roles.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl">New Role</a>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto mb-8">
<table class="w-full text-left">
<thead><tr class="bg-slate-50/50 border-b"><th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Role</th><th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Permissions</th><th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Staff</th><th class="px-6 py-4 text-right text-xs font-black text-slate-400 uppercase">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($roles as $role)
<tr><td class="px-6 py-4 font-black text-slate-800">{{ $role->name }}<div class="text-xs font-medium text-slate-400">{{ $role->description }}</div></td>
<td class="px-6 py-4 text-sm text-slate-600">{{ count($role->permissions ?? []) }} permission(s)</td>
<td class="px-6 py-4 text-sm font-bold">{{ $role->users_count }}</td>
<td class="px-6 py-4 text-right space-x-2">
<a href="{{ route('admin.staff-roles.edit', $role) }}" class="text-blue-500 font-black text-sm">Edit</a>
<form action="{{ route('admin.staff-roles.destroy', $role) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-500 font-black text-sm" data-confirm="Delete this role? Staff using it lose its permissions.">Delete</button></form></td></tr>
@empty
<tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">No custom roles yet.</td></tr>
@endforelse
</tbody></table></div>

<h3 class="font-black text-slate-800 mb-3">Assign roles to staff</h3>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
<table class="w-full text-left"><tbody class="divide-y divide-slate-100">
@foreach($staff as $u)
<tr><td class="px-6 py-4"><span class="font-bold text-slate-800">{{ $u->name }}</span> <span class="text-xs text-slate-400">{{ $u->email }} ({{ $u->role }})</span></td>
<td class="px-6 py-4">
@if($u->role === 'admin')<span class="text-xs font-bold text-slate-400">Admins have all permissions</span>
@else
<form action="{{ route('admin.staff-roles.assign', $u) }}" method="POST" class="flex gap-2">@csrf
<select name="custom_role_id" class="rounded-xl border-slate-200 font-bold text-sm"><option value="">No custom role</option>
@foreach($roles as $role)<option value="{{ $role->id }}" @selected($u->custom_role_id == $role->id)>{{ $role->name }}</option>@endforeach</select>
<button class="bg-slate-800 text-white font-bold px-4 rounded-xl text-sm">Save</button></form>
@endif</td></tr>
@endforeach
</tbody></table></div>
@endsection
