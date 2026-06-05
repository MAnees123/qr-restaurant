<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.restaurant.edit', auth()->user()->restaurant_id);
    }

    public function edit(Restaurant $restaurant)
    {
        if ($restaurant->id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        return view('admin.restaurant.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        if ($restaurant->id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = $restaurant->logo;
        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $file = $request->file('logo');
            $logoPath = $file->store('restaurant_logos', 'public');
            
            // Auto resize the uploaded logo to 256x256
            $absolutePath = Storage::disk('public')->path($logoPath);
            $this->resizeImage($absolutePath, 256, 256);
        }

        $restaurant->update([
            'name' => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.restaurant.edit', $restaurant)->with('success', 'Restaurant settings updated successfully.');
    }

    private function resizeImage($filePath, $targetWidth = 256, $targetHeight = 256)
    {
        $info = getimagesize($filePath);
        if (!$info) {
            return;
        }

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($filePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($filePath);
                } else {
                    return;
                }
                break;
            default:
                return;
        }

        if (!$image) {
            return;
        }

        $targetRatio = $targetWidth / $targetHeight;
        $originalRatio = $width / $height;

        $cropWidth = $width;
        $cropHeight = $height;
        $cropX = 0;
        $cropY = 0;

        if ($originalRatio > $targetRatio) {
            $cropWidth = (int)($height * $targetRatio);
            $cropX = (int)(($width - $cropWidth) / 2);
        } else {
            $cropHeight = (int)($width / $targetRatio);
            $cropY = (int)(($height - $cropHeight) / 2);
        }

        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled(
            $newImage,
            $image,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($newImage, $filePath, 90);
                break;
            case 'image/png':
                imagepng($newImage, $filePath, 9);
                break;
            case 'image/gif':
                imagegif($newImage, $filePath);
                break;
            case 'image/webp':
                imagewebp($newImage, $filePath, 90);
                break;
        }

        imagedestroy($image);
        imagedestroy($newImage);
    }

    public function generateMenuPDF()
    {
        $restaurant = auth()->user()->restaurant;
        $categories = \App\Models\MenuCategory::where('restaurant_id', $restaurant->id)
            ->with(['menuItems' => function($query) {
                $query->where('is_available', true);
            }])
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.menu-pdf', compact('restaurant', 'categories'));
        
        return $pdf->stream($restaurant->name . '-Menu.pdf');
    }
}
