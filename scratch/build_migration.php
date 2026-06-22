<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$user = DB::table('users')->where('email', 'admin@restaurant.com')->first();
if (!$user) {
    die("User admin@restaurant.com not found.\n");
}

$restaurantId = $user->restaurant_id;
$restaurant = DB::table('restaurants')->where('id', $restaurantId)->first();

if (!$restaurant) {
    die("Restaurant not found for user.\n");
}

// Fetch all dependencies as arrays
function getTableData($table, $column, $values) {
    if (!Schema::hasTable($table) || empty($values)) return [];
    return DB::table($table)->whereIn($column, (array) $values)->get()->map(fn($item) => (array)$item)->toArray();
}

$restaurantData = (array)$restaurant;
$users = getTableData('users', 'restaurant_id', [$restaurantId]);
// add user if not in the list
$userIds = array_column($users, 'id');
if (!in_array($user->id, $userIds)) {
    $users[] = (array)$user;
}

$features = getTableData('restaurant_feature', 'restaurant_id', [$restaurantId]);
$restaurantRoles = getTableData('restaurant_role', 'restaurant_id', [$restaurantId]);
$subscriptions = getTableData('subscriptions', 'restaurant_id', [$restaurantId]);
$menuCategories = getTableData('menu_categories', 'restaurant_id', [$restaurantId]);
$menuItems = getTableData('menu_items', 'restaurant_id', [$restaurantId]);
$portions = getTableData('portions', 'restaurant_id', [$restaurantId]);
$banners = getTableData('banners', 'restaurant_id', [$restaurantId]);
$tables = getTableData('tables', 'restaurant_id', [$restaurantId]);

$tableIds = array_column($tables, 'id');
$qrCodes = getTableData('qr_codes', 'table_id', $tableIds);

$reservations = getTableData('reservations', 'restaurant_id', [$restaurantId]);
$orders = getTableData('orders', 'restaurant_id', [$restaurantId]);

$orderIds = array_column($orders, 'id');
$orderItems = getTableData('order_items', 'order_id', $orderIds);
$payments = getTableData('payments', 'order_id', $orderIds);

$exportData = [
    'restaurants' => [$restaurantData],
    'users' => $users,
    'restaurant_feature' => $features,
    'restaurant_role' => $restaurantRoles,
    'subscriptions' => $subscriptions,
    'menu_categories' => $menuCategories,
    'menu_items' => $menuItems,
    'portions' => $portions,
    'banners' => $banners,
    'tables' => $tables,
    'qr_codes' => $qrCodes,
    'reservations' => $reservations,
    'orders' => $orders,
    'order_items' => $orderItems,
    'payments' => $payments,
];

file_put_contents('scratch/migration_data.json', json_encode($exportData, JSON_PRETTY_PRINT));
echo "Data dumped to scratch/migration_data.json\n";
