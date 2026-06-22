<?php

$data = json_decode(file_get_contents(__DIR__.'/migration_data.json'), true);

$className = 'InsertAdminRestaurantData';

$migrationContent = "<?php\n\n";
$migrationContent .= "use Illuminate\\Database\\Migrations\\Migration;\n";
$migrationContent .= "use Illuminate\\Support\\Facades\\DB;\n\n";
$migrationContent .= "return new class extends Migration\n{\n";
$migrationContent .= "    /**\n     * Run the migrations.\n     */\n";
$migrationContent .= "    public function up(): void\n    {\n";

// Tables to process in order (to respect foreign keys)
$tablesOrder = [
    'plans',
    'restaurants',
    'users',
    'restaurant_feature',
    'restaurant_role',
    'subscriptions',
    'menu_categories',
    'menu_items',
    'portions',
    'banners',
    'tables',
    'qr_codes',
    'reservations',
    'orders',
    'order_items',
    'payments',
];

foreach ($tablesOrder as $table) {
    if (empty($data[$table])) {
        continue;
    }
    
    $migrationContent .= "\n        // Seed data for $table\n";
    foreach ($data[$table] as $row) {
        $exportRow = var_export($row, true);
        // Replace array ( ) with [ ]
        $exportRow = str_replace(['array (', ')'], ['[', ']'], $exportRow);
        
        if (isset($row['id'])) {
            $migrationContent .= "        DB::table('$table')->updateOrInsert(\n";
            $migrationContent .= "            ['id' => " . var_export($row['id'], true) . "],\n";
            $migrationContent .= "            $exportRow\n";
            $migrationContent .= "        );\n";
        } else {
            // For tables without 'id', use all non-null columns as matching criteria or just insertOrIgnore
            $migrationContent .= "        \$exists = DB::table('$table')";
            foreach ($row as $k => $v) {
                if ($v !== null) {
                    $migrationContent .= "->where('$k', " . var_export($v, true) . ")";
                }
            }
            $migrationContent .= "->exists();\n";
            $migrationContent .= "        if (!\$exists) {\n";
            $migrationContent .= "            DB::table('$table')->insert($exportRow);\n";
            $migrationContent .= "        }\n";
        }
    }
}

$migrationContent .= "    }\n\n";
$migrationContent .= "    /**\n     * Reverse the migrations.\n     */\n";
$migrationContent .= "    public function down(): void\n    {\n";

// Reverse order for deletion
$reverseOrder = array_reverse($tablesOrder);
foreach ($reverseOrder as $table) {
    if (empty($data[$table])) {
        continue;
    }
    
    $migrationContent .= "\n        // Remove data for $table\n";
    // We can delete by IDs
    $ids = [];
    foreach ($data[$table] as $row) {
        if (isset($row['id'])) {
            $ids[] = $row['id'];
        }
    }
    
    if (!empty($ids)) {
        $idsExport = var_export($ids, true);
        $idsExport = str_replace(['array (', ')'], ['[', ']'], $idsExport);
        $migrationContent .= "        DB::table('$table')->whereIn('id', $idsExport)->delete();\n";
    } else {
        // For tables without id, delete by match
        foreach ($data[$table] as $row) {
            $migrationContent .= "        DB::table('$table')";
            foreach ($row as $k => $v) {
                if ($v !== null) {
                    $migrationContent .= "->where('$k', " . var_export($v, true) . ")";
                }
            }
            $migrationContent .= "->delete();\n";
        }
    }
}

$migrationContent .= "    }\n};\n";

$filename = database_path('migrations/' . date('Y_m_d_His') . '_insert_admin_restaurant_data.php');
file_put_contents($filename, $migrationContent);
echo "Migration generated at: $filename\n";
