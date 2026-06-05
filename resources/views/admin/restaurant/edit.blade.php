@extends('layouts.admin')

@section('header', 'Restaurant Settings')

@section('content')
<div class="bg-white rounded-xl shadow-sm border p-6 max-w-3xl">
    <form action="{{ route('admin.restaurant.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex items-center gap-6 mb-8 pb-8 border-b">
            @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Logo" class="w-24 h-24 rounded-full object-cover border-4 border-stone-100">
            @else
                <div class="w-24 h-24 rounded-full bg-stone-100 flex items-center justify-center border-4 border-stone-200">
                    <svg class="w-8 h-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            @endif
            
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Restaurant Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                <p class="text-xs text-slate-500 mt-2">Recommended size: 256x256px. Max 2MB.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Restaurant Name</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" class="w-full rounded-lg border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Cuisine Type</label>
                <input type="text" name="cuisine_type" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}" class="w-full rounded-lg border-gray-300" placeholder="e.g. Italian, Sushi, Cafe">
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}" class="w-full rounded-lg border-gray-300">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
            <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300">{{ old('address', $restaurant->address) }}</textarea>
        </div>
        
        <div class="pt-4 flex justify-end">
            <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-amber-600">Save Changes</button>
        </div>
    </form>
</div>
@endsection
