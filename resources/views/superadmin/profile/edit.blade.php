@extends('superadmin.layout')

@section('title', 'My Profile')

@section('content')
<div style="margin-bottom:24px;">
    <h1 style="font-size:22px; font-weight:800; color:var(--text);">Account Settings</h1>
    <p style="font-size:13px; color:var(--muted); margin-top:3px;">Manage your profile information, avatar image, and update your security credentials</p>
</div>

<div class="form-grid" style="display:grid; grid-template-columns:1fr 2fr; gap:24px; align-items:start;">

    {{-- Avatar and Overview Side --}}
    <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="card" style="text-align:center; padding:30px 20px;">
            <div style="position:relative; display:inline-block; margin:0 auto 16px;">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" 
                         style="width:110px; height:110px; border-radius:50%; object-cover: cover; border:3px solid var(--accent); box-shadow: 0 4px 12px rgba(108,99,255,.15);">
                @else
                    <div style="width:110px; height:110px; border-radius:50%; background:linear-gradient(135deg, var(--accent), var(--accent2)); display:flex; align-items:center; justify-content:center; font-size:42px; font-weight:800; color:#fff; border:3px solid var(--accent); box-shadow: 0 4px 12px rgba(108,99,255,.15);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            <h3 style="font-size:18px; font-weight:700; color:var(--text); margin-bottom:4px;">{{ $user->name }}</h3>
            <p style="font-size:12.5px; color:var(--muted); margin-bottom:12px;">{{ $user->email }}</p>
            <span class="badge badge-purple" style="font-size:11px; padding:4px 12px;">Super Administrator</span>
        </div>
    </div>

    {{-- Forms Side --}}
    <div style="display:flex; flex-direction:column; gap:24px;">
        
        {{-- Profile Details Update Form --}}
        <div class="card">
            <div class="card-header" style="border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:20px;">
                <span><i class="fa-solid fa-user-gear" style="color:var(--accent2); margin-right:8px;"></i>Profile Information</span>
            </div>
            
            <form action="{{ route('superadmin.profile.update') }}" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="avatar">Profile Photo (DP)</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*"
                           class="form-control @error('avatar') is-invalid @enderror" style="padding: 7px 12px;">
                    <small style="color:var(--muted); font-size:11px; display:block; margin-top:5px;">Supports JPEG, PNG, JPG, GIF, WebP up to 2MB</small>
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-top:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-circle-check"></i> Save Profile Details
                    </button>
                </div>
            </form>
        </div>

        {{-- Password Update Form --}}
        <div class="card">
            <div class="card-header" style="border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:20px;">
                <span><i class="fa-solid fa-lock" style="color:var(--accent2); margin-right:8px;"></i>Change Password</span>
            </div>
            
            <form action="{{ route('superadmin.profile.password') }}" method="POST" style="display:flex; flex-direction:column; gap:16px;">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" 
                           class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input type="password" id="password" name="password" 
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="form-control" required>
                </div>

                <div style="margin-top:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
