<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add payment_method column to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('payment_status');
        });

        // Update payments method enum to include safepay and bitcoin
        // SQLite doesn't support ALTER COLUMN, so we use a workaround for MySQL
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'jazzcash', 'easypaisa', 'card', 'safepay', 'bitcoin') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash', 'jazzcash', 'easypaisa', 'card') NOT NULL");
    }
};
