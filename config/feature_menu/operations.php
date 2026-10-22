<?php

return [
    ['group' => 'staff', 'feature' => 'waiter_panel',   'label' => 'Staff Accounts',   'route' => 'admin.staff-accounts.index', 'pattern' => 'admin.staff-accounts.*'],
    ['feature' => 'cashier_module', 'label' => 'Cash Summary',     'route' => 'admin.cashier.summary',      'pattern' => 'admin.cashier.*'],
    ['feature' => 'online_orders',  'label' => 'Online Orders',    'route' => 'admin.online-orders.index',  'pattern' => 'admin.online-orders.*'],
    ['feature' => 'delivery',       'label' => 'Delivery',         'route' => 'admin.delivery.index',       'pattern' => 'admin.delivery.*'],
    ['group' => 'marketing', 'feature' => 'loyalty',        'label' => 'Loyalty Points',   'route' => 'admin.loyalty.index',        'pattern' => 'admin.loyalty.*'],
];
