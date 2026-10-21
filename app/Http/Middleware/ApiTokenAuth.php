<?php

namespace App\Http\Middleware;

use App\Models\Platform\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bearer-token auth for /api/v1. Usage: ApiTokenAuth::class . ':read' (or ':write').
 * Always answers JSON, scopes the request to the token's restaurant and rate limits per token.
 */
class ApiTokenAuth
{
    public const PER_MINUTE = 120;

    public function handle(Request $request, Closure $next, string $ability = 'read'): Response
    {
        $plain = $request->bearerToken();
        if (! $plain) {
            return $this->error('Missing bearer token.', 401);
        }

        $token = ApiToken::with('restaurant')->where('token_hash', ApiToken::hash($plain))->first();
        if (! $token || $token->revoked_at) {
            return $this->error('Invalid or revoked token.', 401);
        }

        $restaurant = $token->restaurant;
        if (! $restaurant || $restaurant->is_suspended || ! $restaurant->hasFeature('api_access')) {
            return $this->error('API access is not enabled for this restaurant.', 403);
        }

        if (! $token->can($ability)) {
            return $this->error("Token lacks the '{$ability}' scope.", 403);
        }

        $key = 'api-token:' . $token->id;
        if (RateLimiter::tooManyAttempts($key, self::PER_MINUTE)) {
            return $this->error('Too many requests.', 429)
                ->header('Retry-After', (string) RateLimiter::availableIn($key));
        }
        RateLimiter::hit($key, 60);

        $token->forceFill(['last_used_at' => now()])->saveQuietly();
        $request->attributes->set('api_token', $token);
        $request->attributes->set('api_restaurant_id', (int) $token->restaurant_id);

        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit', (string) self::PER_MINUTE);
        $response->headers->set('X-RateLimit-Remaining', (string) RateLimiter::remaining($key, self::PER_MINUTE));

        return $response;
    }

    private function error(string $message, int $status)
    {
        return response()->json(['error' => $message], $status);
    }
}
