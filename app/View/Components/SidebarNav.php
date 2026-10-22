<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Categorised admin sidebar. Structure lives in config/sidebar.php; feature modules add their
 * links through config/feature_menu/*.php (see FeatureMenu). Used by every theme layout, which
 * only supplies colours through the --sb-* CSS variables.
 */
class SidebarNav extends Component
{
    public array $top = [];

    /** @var array<string, array{label: string, icon: string, items: array, active: bool}> */
    public array $groups = [];

    public array $icons = [];

    public function __construct()
    {
        $user = auth()->user();
        $config = config('sidebar');
        $this->icons = $config['icons'];

        $allowed = fn (array $item) => Route::has($item['route'])
            && (empty($item['feature']) || ($user?->restaurant?->hasFeature($item["feature"]) ?? false));

        $this->top = array_values(array_filter($config['top'], $allowed));

        $extra = [];
        foreach (glob(config_path('feature_menu/*.php')) as $file) {
            $default = basename($file, '.php');
            foreach (require $file as $item) {
                $extra[$item['group'] ?? $default][] = $item;
            }
        }

        foreach ($config['groups'] as $key => $group) {
            $items = array_values(array_filter(
                array_merge($group['items'], $extra[$key] ?? []),
                $allowed
            ));

            if ($items === []) {
                continue;
            }

            foreach ($items as &$item) {
                $item['active'] = request()->routeIs($item['pattern'] ?? $item['route']);
            }
            unset($item);

            $this->groups[$key] = [
                'label' => $group['label'],
                'icon' => $group['icon'],
                'items' => $items,
                'active' => (bool) array_filter($items, fn ($i) => $i['active']),
            ];
        }
    }

    public function render(): View
    {
        return view('components.sidebar-nav');
    }
}
