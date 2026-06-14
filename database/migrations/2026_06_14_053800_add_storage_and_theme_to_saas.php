<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'max_storage_mb')) {
                $table->integer('max_storage_mb')->default(100)->after('max_tables');
            }
            if (!Schema::hasColumn('restaurants', 'theme')) {
                $table->string('theme')->default('default')->after('cuisine_type');
            }
        });

        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'max_storage_mb')) {
                $table->integer('max_storage_mb')->default(100)->after('max_tables');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['max_storage_mb', 'theme']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['max_storage_mb']);
        });
    }
};
