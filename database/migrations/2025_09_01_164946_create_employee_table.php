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
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('mobile_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('salary')->default(0); // Monthly salary
            $table->integer('salary_hours')->default(8);
            $table->enum('department', ['MI', 'SMT', 'Other']);
            $table->string('designation')->nullable();
            $table->float('advance_due')->default(0);
            $table->float('outstanding_balance')->default(0);
            $table->date('joining_date');
            $table->date('leaving_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
