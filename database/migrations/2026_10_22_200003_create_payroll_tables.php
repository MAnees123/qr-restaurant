<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('overtime_rate', 10, 2)->default(0); // per hour
            $table->unsignedTinyInteger('working_days')->default(26);
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('month', 7); // YYYY-MM
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['restaurant_id', 'month']);
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('days_present', 5, 1)->default(0);
            $table->decimal('days_absent', 5, 1)->default(0);
            $table->decimal('overtime_hours', 7, 2)->default(0);
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('overtime_pay', 12, 2)->default(0);
            $table->decimal('absence_deduction', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->string('status', 10)->default('pending'); // pending, paid
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['payroll_run_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('salary_profiles');
    }
};
