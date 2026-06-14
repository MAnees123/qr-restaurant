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
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete()->after('id');
            $table->json('granted_features')->nullable()->after('subscription_plan'); // ['pos','inventory','kitchen']
            $table->string('owner_name')->nullable()->after('name');
            $table->string('country')->nullable()->after('address');
            $table->string('city')->nullable()->after('country');
            $table->string('timezone')->default('UTC')->after('city');
            $table->string('currency')->default('USD')->after('timezone');
            $table->string('billing_cycle')->default('monthly')->after('subscription_ends_at'); // monthly|yearly|lifetime
            $table->string('payment_status')->default('unpaid')->after('billing_cycle'); // paid|unpaid|trial
            $table->timestamp('trial_ends_at')->nullable()->after('payment_status');
            $table->integer('max_branches')->default(1)->after('trial_ends_at');
            $table->integer('max_users')->default(5)->after('max_branches');
            $table->integer('max_tables')->default(20)->after('max_users');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'plan_id','granted_features','owner_name','country','city','timezone',
                'currency','billing_cycle','payment_status','trial_ends_at',
                'max_branches','max_users','max_tables',
            ]);
        });
    }
};
