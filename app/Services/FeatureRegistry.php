<?php

namespace App\Services;

/**
 * Central registry of ALL features available in the SaaS platform.
 * Super Admin grants subsets of these to each restaurant.
 */
class FeatureRegistry
{
    /**
     * All available features, grouped by category.
     * 'code' is the unique key stored in the DB.
     */
    public static function all(): array
    {
        return [
            'Operations' => [
                ['code' => 'pos',               'label' => 'POS System'],
                ['code' => 'table_ordering',    'label' => 'QR Table Ordering'],
                ['code' => 'kitchen_screen',    'label' => 'Live Kitchen Screen'],
                ['code' => 'waiter_panel',      'label' => 'Waiter Panel'],
                ['code' => 'cashier_module',    'label' => 'Cashier Module'],
                ['code' => 'online_orders',     'label' => 'Online Orders'],
                ['code' => 'delivery',          'label' => 'Delivery System'],
                ['code' => 'table_reservation', 'label' => 'Table Reservation'],
            ],
            'Management' => [
                ['code' => 'inventory',         'label' => 'Inventory System'],
                ['code' => 'multi_branch',      'label' => 'Multi-Branch System'],
                ['code' => 'role_permission',   'label' => 'Role & Permission System'],
                ['code' => 'customer_mgmt',     'label' => 'Customer Management'],
                ['code' => 'expense_tracking',  'label' => 'Expense Tracking'],
                ['code' => 'purchase_mgmt',     'label' => 'Purchase Management'],
                ['code' => 'supplier_mgmt',     'label' => 'Supplier Management'],
                ['code' => 'attendance',        'label' => 'Attendance System'],
                ['code' => 'payroll',           'label' => 'Payroll'],
            ],
            'Marketing & Loyalty' => [
                ['code' => 'coupons',           'label' => 'Coupons & Discounts'],
                ['code' => 'loyalty',           'label' => 'Loyalty System'],
                ['code' => 'banners',           'label' => 'Hot Deals & Banners'],
            ],
            'Notifications' => [
                ['code' => 'sms_notif',         'label' => 'SMS Notifications'],
                ['code' => 'whatsapp_notif',    'label' => 'WhatsApp Notifications'],
                ['code' => 'email_notif',       'label' => 'Email Notifications'],
            ],
            'Finance' => [
                ['code' => 'billing',           'label' => 'Billing System'],
                ['code' => 'tax_mgmt',          'label' => 'Tax Management'],
                ['code' => 'accounting',        'label' => 'Accounting Module'],
            ],
            'Analytics & Reports' => [
                ['code' => 'analytics',         'label' => 'Analytics Dashboard'],
                ['code' => 'reports',           'label' => 'Reports'],
            ],
            'Customization & Branding' => [
                ['code' => 'theme_custom',      'label' => 'Theme Customization'],
                ['code' => 'custom_branding',   'label' => 'Custom Branding'],
                ['code' => 'dark_mode',         'label' => 'Dark Mode'],
                ['code' => 'qr_logo',           'label' => 'QR Logo Feature'],
            ],
            'Integration & Hardware' => [
                ['code' => 'printer',           'label' => 'Printer Integration'],
                ['code' => 'barcode',           'label' => 'Barcode System'],
                ['code' => 'api_access',        'label' => 'API Access'],
                ['code' => 'mobile_app',        'label' => 'Mobile App Access'],
            ],
            'Data & Security' => [
                ['code' => 'backup',            'label' => 'Backup System'],
                ['code' => 'export_import',     'label' => 'Export / Import'],
                ['code' => 'advanced_security', 'label' => 'Advanced Security Features'],
                ['code' => 'ai_features',       'label' => 'AI Features'],
            ],
        ];
    }

    /** Flat list of all feature codes */
    public static function allCodes(): array
    {
        $codes = [];
        foreach (self::all() as $features) {
            foreach ($features as $f) {
                $codes[] = $f['code'];
            }
        }
        return $codes;
    }

    /** Default free-plan features */
    public static function freeFeatures(): array
    {
        return ['table_ordering', 'qr_logo', 'dark_mode'];
    }

    /** All features (for Starter plan) */
    public static function starterFeatures(): array
    {
        return ['pos', 'table_ordering', 'kitchen_screen', 'waiter_panel', 'cashier_module',
                'coupons', 'analytics', 'reports', 'theme_custom', 'dark_mode', 'qr_logo',
                'email_notif', 'export_import', 'banners'];
    }
}
