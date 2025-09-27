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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->string('attendance_month_year')->unique();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('total_days')->default(0);
            $table->integer('employee_count')->default(0);
            $table->float('total_salary_paid')->default(0);
            $table->float('total_bonus_paid')->default(0);
            $table->float('total_advance_paid')->default(0);
            $table->integer('total_overtime_hours')->default(0);
            $table->float('total_overtime_paid')->default(0);
            $table->float('previous_balance_adjusted')->default(0);
            $table->float('balance_carry_forward')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
