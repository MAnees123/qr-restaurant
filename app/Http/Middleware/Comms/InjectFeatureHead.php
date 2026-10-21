<?php

namespace App\Http\Middleware\Comms;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Injects resources/views/partials/feature-head.blade.php (branding CSS variables, favicon, dark mode)
 * before </head> of HTML pages, so theme layouts do not have to be edited.
 * The partial itself checks the granted features.
 */
class InjectFeatureHead
{
    private const GUEST_ROUTES = ['menu.show', 'payment.show', 'order.confirmed'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && !$request->expectsJson() && !$request->ajax()
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && !$request->routeIs('admin.printer.receipt', 'admin.printer.test', 'admin.barcodes.labels')
        ) {
            $html = $response->getContent();
            if (is_string($html) && ($pos = stripos($html, '</head>')) !== false && !str_contains($html, 'data-comms-feature-head')) {
                $ctx = $this->context($request);
                if ($ctx) {
                    $response->setContent(substr($html, 0, $pos) . view('partials.feature-head', $ctx)->render() . substr($html, $pos));
                }
            }
        }
        return $response;
    }

    private function context(Request $request): ?array
    {
        if ($request->routeIs(...self::GUEST_ROUTES)) {
            $restaurant = Restaurant::find(session('current_restaurant_id'));
            return $restaurant ? ['restaurant' => $restaurant, 'mode' => 'guest', 'user' => null] : null;
        }
        $user = $request->user();
        if ($user && $user->restaurant && !$user->is_super_admin) {
            return ['restaurant' => $user->restaurant, 'mode' => 'admin', 'user' => $user];
        }
        return null;
    }
}
