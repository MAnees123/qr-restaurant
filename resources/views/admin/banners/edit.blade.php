@extends('layouts.admin')

@section('header', 'Edit Ad/Banner')

@section('content')
    <div class="bg-white rounded-[2rem] shadow-sm border overflow-hidden max-w-3xl">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-slate-800">Advertisement Details</h2>
                <p class="text-sm text-slate-400 font-bold mt-1">Update hot deal or banner.</p>
            </div>
        </div>

        <div class="p-8">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Title (Optional)</label>
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Subtitle (Optional)</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Badge Text</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="e.g., FEATURED"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Original Price (Rs)</label>
                        <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $banner->original_price) }}" placeholder="e.g., 200"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Discounted Price (Rs)</label>
                        <input type="number" step="0.01" name="discounted_price" value="{{ old('discounted_price', $banner->discounted_price) }}" placeholder="e.g., 150"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Preparation Time (e.g., 15 Minutes)</label>
                    <input type="text" name="prep_time" value="{{ old('prep_time', $banner->prep_time) }}" placeholder="e.g., 15 Minutes"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Current Image</label>
                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Current Banner" class="h-32 object-cover rounded-xl border border-slate-200 mb-4">
                    
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Replace Image (Optional)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Redirect URL (Optional)</label>
                    <input type="url" name="redirect_url" value="{{ old('redirect_url', $banner->redirect_url) }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-2">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-0 transition">
                    </div>

                    <div class="flex items-center mt-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                            <span class="ml-3 text-sm font-black text-slate-700 uppercase tracking-widest">Active</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('admin.banners.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</a>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
@endsection
