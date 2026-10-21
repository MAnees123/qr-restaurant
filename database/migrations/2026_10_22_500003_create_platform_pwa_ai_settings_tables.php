<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pwa_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('short_name', 30)->nullable();
            $table->string('theme_color', 7)->default('#059669');
            $table->string('background_color', 7)->default('#ffffff');
            $table->timestamps();
        });

        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('llm_url')->nullable();
            $table->text('llm_key')->nullable();    // encrypted
            $table->string('llm_model')->nullable();
            $table->unsignedTinyInteger('target_margin')->default(65); // percent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
        Schema::dropIfExists('pwa_settings');
    }
};
