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
        Schema::table('employee', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });

        Schema::table('employee_attendanceboard', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });

        Schema::table('clients_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });

        Schema::table('employee_attendanceboard', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });

        Schema::table('employee', function (Blueprint $table) {
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
        });
    }
};