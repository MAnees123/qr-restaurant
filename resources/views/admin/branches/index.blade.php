@extends('layouts.admin')

@section('header', 'Branches Management')

@section('content')

@if(session('success'))
<div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-200">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 text-red-600 p-4 rounded-xl border border-red-200">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- List -->
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Branch Name</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($branches as $b)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $b->name }}</td>
                        <td class="px-6 py-4">
                            <x-ui.badge :type="$b->is_active ? 'success' : 'danger'">{{ $b->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.branches.destroy', $b) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" data-confirm="Delete this branch and all its tables?" class="btn btn-sm btn-ghost text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3"><x-ui.empty-state title="No branches added yet." /></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div>
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Add New Branch</h3>
            <form action="{{ route('admin.branches.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Branch Name</label>
                    <input type="text" name="name" class="input w-full" placeholder="e.g. Main Branch, DHA Branch" required>
                </div>
                <div class="mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-700">Active</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary w-full">Create Branch</button>
            </form>
        </div>
    </div>
</div>

@endsection
