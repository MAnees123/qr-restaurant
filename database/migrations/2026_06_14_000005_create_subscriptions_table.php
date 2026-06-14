<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();
            $table->foreignId('plan_id')
                  ->constrained('plans')
                  ->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->enum('status', ['active','paused','expired','canceled'])
                  ->default('active');
            $table->dateTime('trial_ends_at')->nullable();
            $table->enum('payment_status', ['paid','unpaid','failed'])
                  ->default('unpaid');
            $table->enum('billing_cycle', ['monthly','yearly','lifetime'])
                  ->default('monthly');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
?>
