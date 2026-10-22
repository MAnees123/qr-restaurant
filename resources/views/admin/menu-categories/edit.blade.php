@extends('layouts.admin')

@section('header', 'Edit Category: ' . $menuCategory->name)

@section('content')
<div class="card p-6 max-w-3xl mx-auto">
    <form action="{{ route('admin.menu-categories.update', $menuCategory) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Category Name</label>
            <input type="text" name="name" value="{{ old('name', $menuCategory->name) }}" class="input w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optional)</label>
            <textarea name="description" rows="3" class="input w-full">{{ old('description', $menuCategory->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $menuCategory->sort_order) }}" class="input w-full" required>
                <p class="text-xs text-slate-500 mt-1">Lower numbers appear first on the menu.</p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-4 justify-between items-center">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $menuCategory->is_active ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                <span class="font-medium text-slate-700">Category is Active</span>
            </label>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </div>
    </form>
</div>
@endsection
