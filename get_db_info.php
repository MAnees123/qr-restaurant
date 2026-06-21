<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
$result = [];
foreach($tables as $tableObj) {
    $table = array_values((array)$tableObj)[0];
    $columns = Schema::getColumnListing($table);
    $count = DB::table($table)->count();
    $result[$table] = [
        'count' => $count,
        'columns' => $columns
    ];
}
echo json_encode($result, JSON_PRETTY_PRINT);
