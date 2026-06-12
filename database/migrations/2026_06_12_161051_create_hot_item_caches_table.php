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
        Schema::create('hot_item_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('rank_score', 8, 2)->default(0);
            $table->string('badge_type')->nullable();
            $table->string('period_type');
            $table->integer('quantity_sold')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('growth_percentage', 8, 2)->default(0);
            
            $table->index(['restaurant_id', 'period_type', 'rank_score']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hot_item_caches');
    }
};
