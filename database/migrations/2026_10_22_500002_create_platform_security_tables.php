<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_two_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('secret');                 // encrypted
            $table->text('recovery_codes')->nullable(); // JSON of hashed codes
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('result', 30); // success, failed, two_factor_failed, blocked_ip
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('restaurant_security_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('ip_allowlist')->nullable();
            $table->unsignedTinyInteger('password_min_length')->default(8);
            $table->boolean('password_require_mixed')->default(false);
            $table->boolean('password_require_number')->default(false);
            $table->boolean('password_require_symbol')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_security_settings');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('user_two_factors');
    }
};
