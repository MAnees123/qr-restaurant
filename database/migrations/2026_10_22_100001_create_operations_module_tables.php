<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'staff_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('staff_role', 20)->nullable(); // waiter | cashier (only meaningful for role = staff)
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type', 20)->default('dine_in')->index(); // dine_in | pickup | delivery
            }
            // orders.customer_name / orders.customer_phone are added by the customer_mgmt migration (2026_10_22_2*), which runs after this one.
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'delivery_status')) {
                $table->string('delivery_status', 20)->nullable()->index(); // pending | assigned | out | delivered
            }
            if (!Schema::hasColumn('orders', 'driver_name')) {
                $table->string('driver_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'driver_phone')) {
                $table->string('driver_phone', 30)->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'rejection_reason')) {
                $table->string('rejection_reason')->nullable();
            }
            if (!Schema::hasColumn('orders', 'placed_by_user_id')) {
                $table->unsignedBigInteger('placed_by_user_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'loyalty_points_redeemed')) {
                $table->unsignedInteger('loyalty_points_redeemed')->default(0);
            }
        });

        Schema::create('operations_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('online_ordering_open')->default(true);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('delivery_min_order', 10, 2)->default(0);
            $table->boolean('loyalty_enabled')->default(true);
            $table->decimal('loyalty_points_per_currency', 8, 2)->default(1);  // points earned per 1 currency unit paid
            $table->decimal('loyalty_point_value', 8, 2)->default(1);          // currency discount per redeemed point
            $table->unsignedInteger('loyalty_min_redeem')->default(10);
            $table->timestamps();
        });

        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 30);
            $table->string('name')->nullable();
            $table->integer('balance')->default(0);
            $table->integer('lifetime_earned')->default(0);
            $table->timestamps();
            $table->unique(['restaurant_id', 'phone']);
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_account_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('type', 20); // earn | redeem | refund | adjust
            $table->integer('points');  // signed
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_accounts');
        Schema::dropIfExists('operations_settings');
        // Columns added to users/orders are intentionally left in place on rollback (shared tables).
    }
};
