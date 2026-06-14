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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('role');
            $table->boolean('is_suspended')->default(false)->after('is_super_admin');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('subscription_plan')->default('free')->after('is_active');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_plan');
            $table->string('domain')->nullable()->unique()->after('name');
            $table->boolean('is_suspended')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'is_suspended']);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'subscription_ends_at', 'domain', 'is_suspended']);
        });
    }
};
