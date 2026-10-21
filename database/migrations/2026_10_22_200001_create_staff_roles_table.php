<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();
            $table->unique(['restaurant_id', 'name']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('custom_role_id')->nullable()->constrained('staff_roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('custom_role_id');
        });
        Schema::dropIfExists('staff_roles');
    }
};
