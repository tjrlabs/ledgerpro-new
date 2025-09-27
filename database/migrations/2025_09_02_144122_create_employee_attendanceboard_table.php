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
        Schema::create('employee_attendanceboard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained('attendance')->onDelete('cascade');
            $table->float('per_day_salary')->default(0);
            $table->float('per_hour_salary')->default(0);
            $table->integer('present_days')->default(0);
            $table->float('overtime_hours')->default(0);
            $table->float('working_days_salary')->default(0);
            $table->float('overtime_amount')->default(0);
            $table->float('bonus_amount')->default(0);
            $table->float('total_salary')->default(0);
            $table->float('advance_deducted')->default(0);
            $table->float('previous_balance')->default(0);
            $table->float('net_salary_after_deductions')->default(0);
            $table->float('paid_amount')->default(0);
            $table->float('balance_carry_forward')->default(0);
//            $table->float('advance_due')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendanceboard');
    }
};
