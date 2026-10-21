@extends('layouts.guest')
@section('title', 'Two-factor verification')
@section('content')
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px;background:#111">
  <form method="POST" action="{{ route('two-factor.challenge.store') }}" style="background:#fff;border-radius:24px;padding:32px;width:100%;max-width:400px;font-family:Poppins,system-ui,sans-serif">
    @csrf
    <h1 style="margin:0 0 8px;font-size:22px;font-weight:700">Two-factor verification</h1>
    <p style="color:#64748b;font-size:14px">Enter the 6-digit code from your authenticator app, or one of your recovery codes.</p>
    <input type="text" name="code" autocomplete="one-time-code" autofocus required style="width:100%;box-sizing:border-box;margin-top:12px;padding:12px;border:1px solid #cbd5e1;border-radius:12px;font-size:18px;letter-spacing:2px">
    @error('code')<p style="color:#dc2626;font-size:13px;margin:8px 0 0">{{ $message }}</p>@enderror
    <button style="margin-top:16px;width:100%;padding:12px;border:0;border-radius:12px;background:#059669;color:#fff;font-weight:700">Verify</button>
    <a href="{{ route('login') }}" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:#64748b">Back to login</a>
  </form>
</div>
@endsection
