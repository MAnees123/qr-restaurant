<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = [
            [
                'id' => 'default',
                'name' => 'Default Layout',
                'preview' => 'https://placehold.co/600x400/1e293b/ffffff?text=Default+Theme',
                'description' => 'The standard dark sidebar theme with amber accents.'
            ],
            [
                'id' => 'modern',
                'name' => 'Modern (Streamline)',
                'preview' => asset('images/themes/streamline.jpeg'),
                'description' => 'A bright, full-width top navigation layout with clean analytics.'
            ],
            [
                'id' => 'falcon',
                'name' => 'Falcon (Light)',
                'preview' => asset('images/themes/falcon.jpeg'),
                'description' => 'A light, dense UI optimized for complex data and management.'
            ],
            [
                'id' => 'classic',
                'name' => 'Classic Dashboard',
                'preview' => asset('images/themes/dashboard-classic.jpeg'),
                'description' => 'A clean, traditional dashboard with sidebar and detailed stats.'
            ],
            [
                'id' => 'dark',
                'name' => 'Dark Mode',
                'preview' => asset('images/themes/dark.jpeg'),
                'description' => 'A sleek, dark theme with neon accents and high contrast elements.'
            ]
        ];

        return view('admin.themes.index', compact('themes'));
    }

    public function apply(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:default,modern,falcon,classic,dark'
        ]);

        $user = auth()->user();
        $user->theme = $request->theme;
        $user->save();

        return redirect()->back()->with('success', 'Theme updated successfully!');
    }
}
