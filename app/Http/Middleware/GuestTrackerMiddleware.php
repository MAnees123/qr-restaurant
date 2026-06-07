<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class GuestTrackerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('guest_token');
        if (!$token) {
            $token = Str::uuid()->toString();
            // Store for 3 hours (180 minutes)
            Cookie::queue('guest_token', $token, 180);
        }
        
        $request->merge(['guest_token' => $token]);
        
        return $next($request);
    }
}
