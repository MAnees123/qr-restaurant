<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('shift_start', 5)->default('09:00');
            $table->unsignedSmallInteger('late_grace_minutes')->default(15);
            $table->decimal('standard_hours', 4, 1)->default(8);
            $table->timestamps();
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->string('status', 20)->default('present'); // present, late, absent, leave, half_day
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'work_date']);
            $table->index(['restaurant_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_settings');
    }
};
