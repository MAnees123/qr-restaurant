@extends('layouts.admin')

@section('header', 'Menu Items')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary">Add New Item</a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Item</th>
                        <th class="px-6 py-4 font-medium">Category</th>
                        <th class="px-6 py-4 font-medium text-right">Price</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                @if($item->image)
                                    <img src="{{ Storage::url($item->image) }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-100"></div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $item->name }}</p>
                                    <p class="text-xs text-slate-400 truncate w-48">{{ $item->description }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->category->name }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-800">${{ number_format($item->price, 2) }}</td>
                            <td class="px-6 py-4">
                                <x-ui.badge :type="$item->is_available ? 'success' : 'danger'">{{ $item->is_available ? 'Available' : 'Out of Stock' }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-confirm="Delete item?" class="btn btn-sm btn-ghost text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"><x-ui.empty-state title="No menu items yet" message="Add your first item to build the menu." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
