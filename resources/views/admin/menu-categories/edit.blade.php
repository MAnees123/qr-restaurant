@extends('layouts.admin')

@section('header', 'Edit Category: ' . $menuCategory->name)

@section('content')
<div class="bg-white rounded-xl shadow-sm border p-6 max-w-3xl mx-auto">
    <form action="{{ route('admin.menu-categories.update', $menuCategory) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Category Name</label>
            <input type="text" name="name" value="{{ old('name', $menuCategory->name) }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optional)</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">{{ old('description', $menuCategory->description) }}</textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $menuCategory->sort_order) }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
                <p class="text-xs text-slate-500 mt-1">Lower numbers appear first on the menu.</p>
            </div>
        </div>
        
        <div class="pt-4 border-t flex justify-between items-center">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $menuCategory->is_active ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500"> 
                <span class="font-medium text-slate-700">Category is Active</span>
            </label>
            
            <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-amber-600">Update Category</button>
        </div>
    </form>
</div>
@endsection
