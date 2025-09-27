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
        Schema::create('employees_balance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profile')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->integer('month')->index();
            $table->integer('year')->index();
            $table->decimal('opening_advance_balance', 15, 2)->default(0.00);
            $table->decimal('opening_amount_balance', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_balance');
    }
};
