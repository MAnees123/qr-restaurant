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
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade')->after('restaurant_id');
            $table->foreignId('portion_id')->nullable()->constrained()->onDelete('cascade')->after('branch_id');
            $table->string('secure_token')->nullable()->unique()->after('status');
            
            // Unique constraint to prevent duplicate table names in same branch and portion
            // We use a combination of branch_id, portion_id, and table_number
            $table->unique(['branch_id', 'portion_id', 'table_number'], 'table_branch_portion_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['portion_id']);
            $table->dropUnique('table_branch_portion_unique');
            
            $table->dropColumn(['branch_id', 'portion_id', 'secure_token']);
        });
    }
};
