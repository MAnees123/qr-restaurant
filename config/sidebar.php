<?php

/*
 * Admin sidebar structure shared by every theme (see App\View\Components\SidebarNav).
 *
 * 'top'    links are always rendered first, outside any category.
 * 'groups' are collapsible categories; their children are the core 'items' below plus any
 *          entry from config/feature_menu/*.php whose 'group' (default: the file name) matches the key.
 * An item renders only when its route exists, the restaurant has its 'feature' (if set),
 * and a group with no visible children is hidden.
 */
return [
    'top' => [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'home'],
    ],

    'groups' => [
        'orders' => [
            'label' => 'Orders & Dining', 'icon' => 'receipt',
            'items' => [
                ['label' => 'Orders',             'route' => 'admin.orders.index',       'pattern' => 'admin.orders.*'],
                ['label' => 'Kitchen Dashboard',  'route' => 'kitchen.dashboard',        'pattern' => 'kitchen.*',               'feature' => 'kitchen_screen'],
                ['label' => 'Table Reservations', 'route' => 'admin.reservations.index', 'pattern' => 'admin.reservations.*',    'feature' => 'table_reservation'],
                ['label' => 'Tables & QR',        'route' => 'admin.tables.index',       'pattern' => 'admin.tables.*',          'feature' => 'table_ordering'],
            ],
        ],
        'menu' => [
            'label' => 'Menu', 'icon' => 'book',
            'items' => [
                ['label' => 'Menu Categories', 'route' => 'admin.menu-categories.index', 'pattern' => 'admin.menu-categories.*'],
                ['label' => 'Menu Items',      'route' => 'admin.menu-items.index',      'pattern' => 'admin.menu-items.*'],
                ['label' => 'Inventory',       'route' => 'admin.portions.index',        'pattern' => 'admin.portions.*', 'feature' => 'inventory'],
            ],
        ],
        'marketing' => [
            'label' => 'Marketing', 'icon' => 'tag',
            'items' => [
                ['label' => 'Coupons & Offers', 'route' => 'admin.discounts.index', 'pattern' => 'admin.discounts.*', 'feature' => 'coupons'],
                ['label' => 'Hot Deals & Ads',  'route' => 'admin.banners.index',   'pattern' => 'admin.banners.*',   'feature' => 'banners'],
            ],
        ],
        'staff' => [
            'label' => 'Staff & People', 'icon' => 'users',
            'items' => [],
        ],
        'finance' => [
            'label' => 'Finance', 'icon' => 'cash',
            'items' => [],
        ],
        'reports' => [
            'label' => 'Analytics & Reports', 'icon' => 'chart',
            'items' => [
                ['label' => 'Analytics', 'route' => 'admin.analytics.index', 'pattern' => 'admin.analytics.*', 'feature' => 'analytics'],
            ],
        ],
        'comms' => [
            'label' => 'Notifications', 'icon' => 'bell',
            'items' => [],
        ],
        'branding' => [
            'label' => 'Branding & Hardware', 'icon' => 'brush',
            'items' => [
                ['label' => 'Themes', 'route' => 'admin.themes.index', 'pattern' => 'admin.themes.*', 'feature' => 'theme_custom'],
            ],
        ],
        'operations' => [
            'label' => 'Online & Delivery', 'icon' => 'truck',
            'items' => [],
        ],
        'platform' => [
            'label' => 'Platform & Security', 'icon' => 'shield',
            'items' => [],
        ],
        'settings' => [
            'label' => 'Settings', 'icon' => 'cog',
            'items' => [
                ['label' => 'Restaurant Settings', 'route' => 'admin.restaurant.index', 'pattern' => 'admin.restaurant.*'],
                ['label' => 'Branches',            'route' => 'admin.branches.index',   'pattern' => 'admin.branches.*', 'feature' => 'multi_branch'],
            ],
        ],
    ],

    // Heroicons (outline, 24px) path data keyed by the 'icon' names above.
    'icons' => [
        'home'    => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10',
        'receipt' => 'M9 12h6m-6 4h6M7 3h10a2 2 0 012 2v16l-3-2-2 2-2-2-2 2-2-2-3 2V5a2 2 0 012-2z',
        'book'    => 'M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25',
        'tag'     => 'M7 7h.01M3 5v6.17a2 2 0 00.59 1.41l8.83 8.83a2 2 0 002.83 0l5.17-5.17a2 2 0 000-2.83L11.59 4.59A2 2 0 0010.17 4H5a2 2 0 00-2 2z',
        'users'   => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z',
        'cash'    => 'M3 7h18v10H3V7zm9 7a2 2 0 100-4 2 2 0 000 4zM6 10h.01M18 14h.01',
        'chart'   => 'M4 20V10m6 10V4m6 16v-7m4 7H2',
        'bell'    => 'M15 17h5l-1.4-1.4A2 2 0 0118 14.17V11a6 6 0 10-12 0v3.17a2 2 0 01-.6 1.43L4 17h5m6 0a3 3 0 11-6 0',
        'brush'   => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.34M11 7.34l1.42-1.42a2 2 0 012.83 0l2.83 2.83a2 2 0 010 2.83L10 19',
        'truck'   => 'M3 7h11v9H3V7zm11 3h4l3 3v3h-7v-6zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z',
        'shield'  => 'M12 3l8 3v6c0 4.5-3.2 8.2-8 9-4.8-.8-8-4.5-8-9V6l8-3zm-2 9l2 2 4-4',
        'cog'     => 'M10.33 4.32c.43-1.76 2.92-1.76 3.35 0a1.72 1.72 0 002.57 1.07c1.54-.94 3.31.83 2.37 2.37a1.72 1.72 0 001.07 2.57c1.76.43 1.76 2.92 0 3.35a1.72 1.72 0 00-1.07 2.57c.94 1.54-.83 3.31-2.37 2.37a1.72 1.72 0 00-2.57 1.07c-.43 1.76-2.92 1.76-3.35 0a1.72 1.72 0 00-2.57-1.07c-1.54.94-3.31-.83-2.37-2.37a1.72 1.72 0 00-1.07-2.57c-1.76-.43-1.76-2.92 0-3.35a1.72 1.72 0 001.07-2.57c-.94-1.54.83-3.31 2.37-2.37.99.6 2.29.07 2.57-1.07zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ],
];
