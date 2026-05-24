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
        Schema::create('profit_loss_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profile')->cascadeOnDelete();
            $table->foreignId('payments_board_id')->constrained('payments_board')->cascadeOnDelete();
            $table->string('board_month_year');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->decimal('total_cash_sales', 15, 2)->default(0);
            $table->decimal('total_pre_gst_sales', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->decimal('profit_loss', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['company_profile_id', 'board_month_year'], 'profit_loss_reports_company_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_loss_reports');
    }
};
