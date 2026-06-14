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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // paid, unpaid, trial
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, lifetime
            $table->string('payment_method')->nullable(); // stripe, paypal, rapid, bitcoin
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('activity_logs');
    }
};
