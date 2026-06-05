@extends('layouts.admin')

@section('header', 'Menu Items')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div></div>
    <a href="{{ route('admin.menu-items.create') }}" class="bg-amber-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-amber-600">Add New Item</a>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-sm border-b">
            <tr>
                <th class="px-6 py-3 font-medium">Item</th>
                <th class="px-6 py-3 font-medium">Category</th>
                <th class="px-6 py-3 font-medium">Price</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($items as $item)
                <tr>
                    <td class="px-6 py-4 flex items-center gap-3">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-slate-100"></div>
                        @endif
                        <div>
                            <p class="font-bold text-slate-800">{{ $item->name }}</p>
                            <p class="text-xs text-slate-400 truncate w-48">{{ $item->description }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $item->category->name }}</td>
                    <td class="px-6 py-4 font-bold text-amber-600">${{ number_format($item->price, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $item->is_available ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $item->is_available ? 'Available' : 'Out of Stock' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('admin.menu-items.edit', $item) }}" class="text-amber-500 hover:text-amber-700 mr-3">Edit</a>
                        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete item?')" class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
