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
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->cascadeOnDelete();
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->cascadeOnDelete();
        });

        Schema::table('employee_attendanceboard', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->cascadeOnDelete();
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->cascadeOnDelete();
        });

        Schema::table('clients_payments', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable(false)->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_payments', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->nullOnDelete();
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->nullOnDelete();
        });

        Schema::table('employee_attendanceboard', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->nullOnDelete();
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->nullOnDelete();
        });

        Schema::table('employee', function (Blueprint $table) {
            $table->dropForeign(['company_profile_id']);
            $table->unsignedBigInteger('company_profile_id')->nullable()->change();
            $table->foreign('company_profile_id')->references('id')->on('company_profile')->nullOnDelete();
        });
    }
};