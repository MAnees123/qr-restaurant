@extends('layouts.admin')

@section('header', 'Menu Categories')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.menu-categories.store') }}" method="POST" class="card p-4 flex flex-col sm:flex-row gap-4 sm:items-center w-full max-w-3xl sm:ml-auto">
        @csrf
        <input type="text" name="name" placeholder="Category Name (e.g. Starters)" class="input flex-1" required>
        <input type="number" name="sort_order" placeholder="Sort Order" value="0" class="input sm:w-28">
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500"> Active
        </label>
        <button type="submit" class="btn btn-primary">Add Category</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Order</th>
                        <th class="px-6 py-4 font-medium">Name</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 text-slate-500">{{ $category->sort_order }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $category->name }}</td>
                            <td class="px-6 py-4">
                                <x-ui.badge :type="$category->is_active ? 'success' : 'warning'">{{ $category->is_active ? 'Active' : 'Hidden' }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.menu-categories.edit', $category) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('admin.menu-categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-confirm="Delete category?" class="btn btn-sm btn-ghost text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"><x-ui.empty-state title="No categories yet" message="Add your first category above." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
