@extends('layouts.admin')

@section('header', 'Ads & Hot Deals')

@section('header_actions')
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create New Ad
    </a>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Advertisement Banners</h2>
                <p class="text-sm text-slate-500 mt-1">Manage promotional banners on the customer mobile app.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Image</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Order</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Ad Image" class="w-24 h-12 object-cover rounded-lg shadow-sm border border-slate-200">
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800 text-sm">{{ $banner->title ?? 'No Title' }}</p>
                                @if($banner->subtitle)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $banner->subtitle }}</p>
                                @endif
                                @if($banner->redirect_url)
                                    <p class="text-[10px] text-blue-500 mt-1 truncate max-w-xs">{{ $banner->redirect_url }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.badge :type="$banner->is_active ? 'success' : 'warning'">{{ $banner->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-600">{{ $banner->sort_order }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-secondary !px-2.5" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" data-confirm="Delete this banner?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost !px-2.5 text-red-600" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"><x-ui.empty-state title="No banners added yet." message="Create an ad to display on the mobile app." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
