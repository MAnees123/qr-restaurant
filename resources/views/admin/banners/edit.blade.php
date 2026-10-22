@extends('layouts.admin')

@section('header', 'Edit Ad/Banner')

@section('content')
    <div class="card overflow-hidden max-w-3xl">
        <div class="px-6 py-5 sm:px-8 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Advertisement Details</h2>
                <p class="text-sm text-slate-500 mt-1">Update hot deal or banner.</p>
            </div>
        </div>

        <div class="px-6 py-6 sm:p-8">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Title (Optional)</label>
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subtitle (Optional)</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Badge Text</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="e.g., FEATURED"
                        class="input w-full">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Original Price (Rs)</label>
                        <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $banner->original_price) }}" placeholder="e.g., 200"
                            class="input w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Discounted Price (Rs)</label>
                        <input type="number" step="0.01" name="discounted_price" value="{{ old('discounted_price', $banner->discounted_price) }}" placeholder="e.g., 150"
                            class="input w-full">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Preparation Time (e.g., 15 Minutes)</label>
                    <input type="text" name="prep_time" value="{{ old('prep_time', $banner->prep_time) }}" placeholder="e.g., 15 Minutes"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Current Image</label>
                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Current Banner" class="h-32 object-cover rounded-xl border border-slate-200 mb-4 shadow-sm">
                    
                    <label class="block text-sm font-medium text-slate-700 mb-1">Replace Image (Optional)</label>
                    <input type="file" name="image" accept="image/*"
                        class="input w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Redirect URL (Optional)</label>
                    <input type="url" name="redirect_url" value="{{ old('redirect_url', $banner->redirect_url) }}"
                        class="input w-full">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}"
                            class="input w-full">
                    </div>

                    <div class="flex items-center sm:mt-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-slate-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
@endsection
