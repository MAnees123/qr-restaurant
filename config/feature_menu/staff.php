<?php

return [
    ['feature' => 'role_permission', 'label' => 'Roles & Permissions', 'route' => 'admin.staff-roles.index', 'pattern' => 'admin.staff-roles.*'],
    ['feature' => 'attendance',      'label' => 'Attendance',          'route' => 'admin.attendance.index',  'pattern' => 'admin.attendance.*'],
    ['feature' => 'payroll',         'label' => 'Payroll',             'route' => 'admin.payroll.index',     'pattern' => 'admin.payroll.*'],
    ['feature' => 'customer_mgmt',   'label' => 'Customers',           'route' => 'admin.customers.index',   'pattern' => 'admin.customers.*'],
];
