@extends('layouts.admin')
@section('title', 'Role')
@section('header', $role->exists ? 'Edit Role' : 'New Role')
@section('content')
<div class="max-w-2xl mx-auto">
@include('staff._errors')
<div class="card p-6 sm:p-8">
<form action="{{ $role->exists ? route('admin.staff-roles.update', $role) : route('admin.staff-roles.store') }}" method="POST" class="space-y-6">
@csrf @if($role->exists) @method('PUT') @endif
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label><input type="text" name="name" value="{{ old('name', $role->name) }}" required class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label><input type="text" name="description" value="{{ old('description', $role->description) }}" class="input"></div>
<div><label class="block text-sm font-medium text-slate-700 mb-2">Permissions</label>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
@foreach($permissions as $code => $label)
<label class="flex items-center gap-2 text-sm font-medium text-slate-700 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 transition-colors cursor-pointer"><input type="checkbox" name="permissions[]" value="{{ $code }}" class="rounded border-slate-300 text-indigo-600" @checked(in_array($code, old('permissions', $role->permissions ?? [])))> {{ $label }}</label>
@endforeach</div></div>
<div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
<a href="{{ route('admin.staff-roles.index') }}" class="btn btn-secondary">Cancel</a>
<button class="btn btn-primary">Save Role</button></div>
</form></div></div>
@endsection
