<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Roles table (global & tenant scoped)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained('restaurants')->onDelete('cascade');
            $table->timestamps();
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Role-Permission many‑to‑many pivot
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // Restaurant‑Role pivot (assign roles to a tenant)
        Schema::create('restaurant_role', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['restaurant_id', 'role_id']);
        });

        // Restaurant‑Feature pivot for per‑tenant overrides
        Schema::create('restaurant_feature', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('limit_value')->nullable();
            $table->primary(['restaurant_id', 'feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_feature');
        Schema::dropIfExists('restaurant_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
?>
