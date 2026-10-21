@extends('layouts.admin')
@section('title', $member ? 'Edit Staff' : 'New Staff')
@section('header', $member ? 'Edit Staff Account' : 'New Staff Account')
@section('content')
<div class="max-w-2xl mx-auto">
    @include('operations._alerts')
    <div class="bg-white rounded-2xl shadow-sm border p-8">
        <form action="{{ $member ? route('admin.staff-accounts.update', $member) : route('admin.staff-accounts.store') }}" method="POST" class="space-y-6">
            @csrf
            @if ($member) @method('PUT') @endif
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Staff Role</label>
                <select name="staff_role" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold">
                    @foreach ($roles as $r)
                        <option value="{{ $r }}" @selected(old('staff_role', $member->staff_role ?? '') === $r)>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Password {{ $member ? '(leave blank to keep)' : '' }}</label>
                    <input type="password" name="password" {{ $member ? '' : 'required' }} class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl transition shadow-lg shadow-emerald-200 uppercase tracking-[0.2em]">{{ $member ? 'Save Changes' : 'Create Account' }}</button>
        </form>
    </div>
</div>
@endsection
