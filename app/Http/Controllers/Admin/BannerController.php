<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image; // Assuming intervention image is not available, we can use GD or just simple upload.

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'redirect_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'redirect_url' => $request->redirect_url,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        if ($banner->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        if ($banner->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'redirect_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $banner->image_path = $request->file('image')->store('banners', 'public');
        }

        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->redirect_url = $request->redirect_url;
        $banner->is_active = $request->has('is_active');
        $banner->sort_order = $request->sort_order ?? 0;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
