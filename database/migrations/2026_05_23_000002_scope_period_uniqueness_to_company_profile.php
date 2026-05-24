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
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('attendance_attendance_month_year_unique');
            $table->unique(
                ['company_profile_id', 'attendance_month_year'],
                'attendance_company_profile_period_unique'
            );
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->unique(
                ['company_profile_id', 'board_month_year'],
                'payments_board_company_profile_period_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments_board', function (Blueprint $table) {
            $table->dropUnique('payments_board_company_profile_period_unique');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('attendance_company_profile_period_unique');
            $table->unique('attendance_month_year');
        });
    }
};