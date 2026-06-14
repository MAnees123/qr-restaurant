<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the Super Admin profile form.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('superadmin.profile.edit', compact('user'));
    }

    /**
     * Update the Super Admin's basic profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('superadmin.profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the Super Admin's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('superadmin.profile.edit')->with('success', 'Password updated successfully.');
    }
}
