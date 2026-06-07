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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('badge_text')->nullable()->default('FEATURED');
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->string('prep_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['badge_text', 'original_price', 'discounted_price', 'prep_time']);
        });
    }
};
