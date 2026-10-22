@extends('layouts.admin')
@section('title', $member ? 'Edit Staff' : 'New Staff')
@section('header', $member ? 'Edit Staff Account' : 'New Staff Account')
@section('content')
<div class="max-w-2xl mx-auto">
    @include('operations._alerts')
    <div class="card p-6 sm:p-8">
        <form action="{{ $member ? route('admin.staff-accounts.update', $member) : route('admin.staff-accounts.store') }}" method="POST" class="space-y-6">
            @csrf
            @if ($member) @method('PUT') @endif
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required class="input">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}" required class="input">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Staff Role</label>
                <select name="staff_role" required class="input">
                    @foreach ($roles as $r)
                        <option value="{{ $r }}" @selected(old('staff_role', $member->staff_role ?? '') === $r)>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password {{ $member ? '(leave blank to keep)' : '' }}</label>
                    <input type="password" name="password" {{ $member ? '' : 'required' }} class="input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input">
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                <a href="{{ route('admin.staff-accounts.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $member ? 'Save Changes' : 'Create Account' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
