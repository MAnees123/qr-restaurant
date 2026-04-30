@extends('layouts.admin')

@section('header', 'Menu Categories')

@section('content')
<div class="mb-6 flex justify-end">
    <form action="{{ route('admin.menu-categories.store') }}" method="POST" class="flex gap-4 items-center bg-white p-4 rounded-xl shadow-sm border w-full max-w-3xl">
        @csrf
        <input type="text" name="name" placeholder="Category Name (e.g. Starters)" class="rounded-lg border-gray-300 flex-1" required>
        <input type="number" name="sort_order" placeholder="Sort Order" value="0" class="rounded-lg border-gray-300 w-24">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded text-amber-500"> Active
        </label>
        <button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-amber-600">Add Category</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-sm border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Order</th>
                <th class="px-6 py-3 font-medium">Name</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($categories as $category)
                <tr>
                    <td class="px-6 py-4 text-slate-500">{{ $category->sort_order }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $category->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $category->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.menu-categories.edit', $category) }}" class="text-amber-500 hover:text-amber-700 text-sm font-medium mr-3">Edit</a>
                        <form action="{{ route('admin.menu-categories.destroy', $category) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete category?')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
