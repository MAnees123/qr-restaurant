@extends('layouts.admin')

@section('header', 'Restaurant Settings')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row gap-4">
    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary flex-1 !py-3">Manage Restaurant Branches</a>
    <a href="{{ route('admin.portions.index') }}" class="btn btn-secondary flex-1 !py-3">Manage Portions & Sections</a>
</div>

<div class="card p-6 max-w-3xl">
    <form action="{{ route('admin.restaurant.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8 pb-8 border-b border-slate-100">
            @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Logo" class="w-24 h-24 rounded-full object-cover border-4 border-slate-100">
            @else
                <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center border-4 border-slate-200">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            @endif
            
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Restaurant Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-xs text-slate-500 mt-2">Recommended size: 256x256px. Max 2MB.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Restaurant Name</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" class="input w-full" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Cuisine Type</label>
                <input type="text" name="cuisine_type" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}" class="input w-full" placeholder="e.g. Italian, Sushi, Cafe">
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}" class="input w-full">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
            <textarea name="address" rows="3" class="input w-full">{{ old('address', $restaurant->address) }}</textarea>
        </div>
        
        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection
