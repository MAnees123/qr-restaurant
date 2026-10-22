@extends('layouts.admin')

@section('header', 'Add Menu Item')

@section('content')
<div class="card p-6 max-w-3xl mx-auto">
    <form action="{{ route('admin.menu-items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
            <input type="text" name="name" class="input w-full" required>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                <select name="menu_category_id" class="input w-full" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Real Price (Rs) - Cut</label>
                <input type="number" step="0.01" name="original_price" class="input w-full" placeholder="e.g. 200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount Price (Rs)</label>
                <input type="number" step="0.01" name="price" class="input w-full" placeholder="e.g. 150" required>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="input w-full"></textarea>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Preparation Time (Mins)</label>
                <input type="number" name="preparation_time" value="15" class="input w-full" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="input w-full">
            </div>
        </div>
        
        <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-4 justify-between items-center">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_available" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500"> 
                <span class="font-medium text-slate-700">Available for Order</span>
            </label>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.menu-items.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Item</button>
            </div>
        </div>
    </form>
</div>
@endsection
