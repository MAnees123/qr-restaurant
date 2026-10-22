@extends('layouts.admin')
@section('title', 'Roles & Permissions')
@section('header', 'Roles & Permissions')
@section('content')
@include('staff._errors')
<div class="space-y-6">
<div class="flex flex-wrap gap-3 justify-between items-center">
    <p class="text-slate-500">Create custom roles and assign them to your staff.</p>
    <a href="{{ route('admin.staff-roles.create') }}" class="btn btn-primary">New Role</a>
</div>
<div class="card overflow-x-auto">
<table class="w-full text-left">
<thead><tr class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase tracking-wide"><th class="px-6 py-4">Role</th><th class="px-6 py-4">Permissions</th><th class="px-6 py-4 text-right">Staff</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($roles as $role)
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4 font-semibold text-slate-800">{{ $role->name }}<div class="text-xs font-normal text-slate-400">{{ $role->description }}</div></td>
<td class="px-6 py-4 text-sm"><x-ui.badge type="info">{{ count($role->permissions ?? []) }} permission(s)</x-ui.badge></td>
<td class="px-6 py-4 text-sm text-right font-semibold">{{ $role->users_count }}</td>
<td class="px-6 py-4 text-right"><div class="flex justify-end items-center gap-2">
<a href="{{ route('admin.staff-roles.edit', $role) }}" class="btn btn-sm btn-secondary">Edit</a>
<form action="{{ route('admin.staff-roles.destroy', $role) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost text-red-600" data-confirm="Delete this role? Staff using it lose its permissions.">Delete</button></form></div></td></tr>
@empty
<tr><td colspan="4"><x-ui.empty-state title="No custom roles yet" message="Create a role to fine-tune what staff can access."/></td></tr>
@endforelse
</tbody></table></div>

<div>
<h3 class="font-semibold text-slate-800 mb-3">Assign roles to staff</h3>
<div class="card overflow-x-auto">
<table class="w-full text-left"><tbody class="divide-y divide-slate-100">
@forelse($staff as $u)
<tr class="hover:bg-slate-50/60 transition-colors"><td class="px-6 py-4"><span class="font-semibold text-slate-800">{{ $u->name }}</span> <span class="text-xs text-slate-400">{{ $u->email }} ({{ $u->role }})</span></td>
<td class="px-6 py-4">
@if($u->role === 'admin')<span class="text-xs font-medium text-slate-400">Admins have all permissions</span>
@else
<form action="{{ route('admin.staff-roles.assign', $u) }}" method="POST" class="flex gap-2">@csrf
<select name="custom_role_id" class="input !w-auto text-sm"><option value="">No custom role</option>
@foreach($roles as $role)<option value="{{ $role->id }}" @selected($u->custom_role_id == $role->id)>{{ $role->name }}</option>@endforeach</select>
<button class="btn btn-sm btn-secondary">Save</button></form>
@endif</td></tr>
@empty
<tr><td><x-ui.empty-state title="No staff found"/></td></tr>
@endforelse
</tbody></table></div>
</div>
</div>
@endsection
