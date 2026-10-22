@extends('layouts.admin')

@section('header', 'Create New Ad/Banner')

@section('content')
    <div class="card overflow-hidden max-w-3xl">
        <div class="px-6 py-5 sm:px-8 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Advertisement Details</h2>
                <p class="text-sm text-slate-500 mt-1">Add a new hot deal or banner.</p>
            </div>
        </div>

        <div class="px-6 py-6 sm:p-8">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Title (Optional)</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g., Weekend Special 50% OFF!"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subtitle (Optional)</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="e.g., Valid until Sunday only"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Badge Text</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', 'FEATURED') }}" placeholder="e.g., FEATURED, HOT DEAL"
                        class="input w-full">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Original Price (Rs)</label>
                        <input type="number" step="0.01" name="original_price" value="{{ old('original_price') }}" placeholder="e.g., 200"
                            class="input w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Discounted Price (Rs)</label>
                        <input type="number" step="0.01" name="discounted_price" value="{{ old('discounted_price') }}" placeholder="e.g., 150"
                            class="input w-full">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Preparation Time (e.g., 15 Minutes)</label>
                    <input type="text" name="prep_time" value="{{ old('prep_time') }}" placeholder="e.g., 15 Minutes"
                        class="input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Banner Image <span class="text-red-500">*</span></label>
                    <input type="file" name="image" required accept="image/*"
                        class="input w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-500 mt-2">Recommended size: 800x400px (JPG, PNG, WEBP)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Redirect URL (Optional)</label>
                    <input type="url" name="redirect_url" value="{{ old('redirect_url') }}" placeholder="https://example.com/promo"
                        class="input w-full">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                            class="input w-full">
                        <p class="text-xs text-slate-500 mt-1">Lower numbers appear first.</p>
                    </div>

                    <div class="flex items-center sm:mt-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-slate-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
@endsection
