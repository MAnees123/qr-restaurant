<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Sidebar links for feature modules. Each module lists itself in config/feature_menu/*.php:
 *   ['feature' => 'payroll', 'label' => 'Payroll', 'route' => 'admin.payroll.index', 'pattern' => 'admin.payroll.*']
 * A link renders only when the restaurant has the feature granted and the route exists.
 */
class FeatureMenu extends Component
{
    public array $items = [];

    public function __construct(public string $theme = 'default')
    {
        $restaurant = auth()->user()?->restaurant;

        foreach (glob(config_path('feature_menu/*.php')) as $file) {
            foreach (require $file as $item) {
                if (Route::has($item['route']) && $restaurant?->hasFeature($item['feature'])) {
                    $this->items[] = $item;
                }
            }
        }
    }

    public function render(): View
    {
        return view('components.feature-menu');
    }
}
