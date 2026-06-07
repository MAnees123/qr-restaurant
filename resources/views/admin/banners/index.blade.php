@extends('layouts.admin')

@section('header', 'Ads & Hot Deals')

@section('header_actions')
    <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create New Ad
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-[2rem] shadow-sm border overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-slate-800">Advertisement Banners</h2>
                <p class="text-sm text-slate-400 font-bold mt-1">Manage promotional banners on the customer mobile app.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="p-4 pl-8 text-xs font-black text-slate-400 uppercase tracking-widest">Image</th>
                        <th class="p-4 text-xs font-black text-slate-400 uppercase tracking-widest">Details</th>
                        <th class="p-4 text-xs font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="p-4 text-xs font-black text-slate-400 uppercase tracking-widest">Order</th>
                        <th class="p-4 pr-8 text-right text-xs font-black text-slate-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition">
                            <td class="p-4 pl-8">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Ad Image" class="w-24 h-12 object-cover rounded-lg shadow-sm border border-slate-200">
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-slate-800 text-sm">{{ $banner->title ?? 'No Title' }}</p>
                                @if($banner->subtitle)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $banner->subtitle }}</p>
                                @endif
                                @if($banner->redirect_url)
                                    <p class="text-[10px] text-blue-500 mt-1 truncate max-w-xs">{{ $banner->redirect_url }}</p>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($banner->is_active)
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase rounded-lg tracking-widest">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase rounded-lg tracking-widest">Inactive</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-600">{{ $banner->sort_order }}</span>
                            </td>
                            <td class="p-4 pr-8 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="text-slate-500 font-bold">No banners added yet.</p>
                                <p class="text-sm text-slate-400 mt-1">Create an ad to display on the mobile app.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
