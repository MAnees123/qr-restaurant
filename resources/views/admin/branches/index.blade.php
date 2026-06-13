@extends('layouts.admin')

@section('header', 'Branches Management')

@section('content')

@if(session('success'))
<div class="mb-6 bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-200">
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
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4 font-bold text-gray-700">Branch Name</th>
                        <th class="p-4 font-bold text-gray-700">Status</th>
                        <th class="p-4 font-bold text-gray-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($branches as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-bold text-gray-800">{{ $b->name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-[10px] uppercase font-black tracking-widest rounded-lg {{ $b->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $b->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <form action="{{ route('admin.branches.destroy', $b) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this branch and all its tables?')" class="text-xs text-red-500 font-bold uppercase hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-500">No branches added yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Form -->
    <div>
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Branch</h3>
            <form action="{{ route('admin.branches.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Branch Name</label>
                    <input type="text" name="name" class="w-full rounded-lg border-gray-300" placeholder="e.g. Main Branch, DHA Branch" required>
                </div>
                <div class="mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-amber-500">
                        <span class="text-sm font-bold text-gray-700">Active</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 rounded-xl transition">Create Branch</button>
            </form>
        </div>
    </div>
</div>

@endsection
