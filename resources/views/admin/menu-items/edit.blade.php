@extends('layouts.admin')

@section('header', 'Edit Item: ' . $menuItem->name)

@section('content')
<div class="bg-white rounded-xl shadow-sm border p-6 max-w-3xl mx-auto">
    <form action="{{ route('admin.menu-items.update', $menuItem) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $menuItem->name) }}" class="w-full rounded-lg border-gray-300" required>
        </div>
        
        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                <select name="menu_category_id" class="w-full rounded-lg border-gray-300" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $menuItem->menu_category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Real Price (Rs) - Cut</label>
                <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $menuItem->original_price) }}" class="w-full rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount Price (Rs)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $menuItem->price) }}" class="w-full rounded-lg border-gray-300" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6 items-center mb-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description', $menuItem->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Preparation Time (Mins)</label>
                <input type="number" name="preparation_time" value="{{ old('preparation_time', $menuItem->preparation_time) }}" class="w-full rounded-lg border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $menuItem->sort_order) }}" class="w-full rounded-lg border-gray-300" required>
                <p class="text-xs text-slate-500 mt-1">Lower numbers appear first.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6 items-center">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Update Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
            </div>
            <div class="flex justify-end">
                @if($menuItem->image)
                    <img src="{{ Storage::url($menuItem->image) }}" class="w-20 h-20 rounded-lg object-cover border shadow-sm">
                @endif
            </div>
        </div>
        
        <div class="pt-4 border-t flex justify-between items-center">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_available" value="1" {{ $menuItem->is_available ? 'checked' : '' }} class="rounded text-amber-500"> 
                <span class="font-medium text-slate-700">Available for Order</span>
            </label>
            
            <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-amber-600">Save Changes</button>
        </div>
    </form>
</div>
@endsection
