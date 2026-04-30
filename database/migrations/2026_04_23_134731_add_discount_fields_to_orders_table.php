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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->after('table_id')->default(0);
            $table->string('coupon_code')->nullable()->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
            // total_amount is already there, it will now represent subtotal - discount_amount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'coupon_code', 'discount_amount']);
        });
    }
};
