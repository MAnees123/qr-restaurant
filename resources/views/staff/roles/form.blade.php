@extends('layouts.admin')
@section('title', 'Role')
@section('header', $role->exists ? 'Edit Role' : 'New Role')
@section('content')
<div class="max-w-2xl mx-auto">
@include('staff._errors')
<div class="bg-white rounded-2xl shadow-sm border p-8">
<form action="{{ $role->exists ? route('admin.staff-roles.update', $role) : route('admin.staff-roles.store') }}" method="POST" class="space-y-6">
@csrf @if($role->exists) @method('PUT') @endif
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Name</label><input type="text" name="name" value="{{ old('name', $role->name) }}" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Description</label><input type="text" name="description" value="{{ old('description', $role->description) }}" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold"></div>
<div><label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Permissions</label>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
@foreach($permissions as $code => $label)
<label class="flex items-center gap-2 text-sm font-bold text-slate-700"><input type="checkbox" name="permissions[]" value="{{ $code }}" class="rounded border-slate-300 text-emerald-600" @checked(in_array($code, old('permissions', $role->permissions ?? [])))> {{ $label }}</label>
@endforeach</div></div>
<button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl uppercase tracking-widest">Save Role</button>
</form></div></div>
@endsection
