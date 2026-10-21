<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
            $table->unique(['restaurant_id', 'phone']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            // customer_name / customer_phone may already exist (added by other modules' migrations)
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            // customer_name / customer_phone are shared with other modules and left in place.
        });
        Schema::dropIfExists('customers');
    }
};
