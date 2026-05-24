<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->foreignId('company_profile_id')
                ->nullable()
                ->constrained('company_profile')
                ->nullOnDelete();
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('company_profile_id')
                ->nullable()
                ->constrained('company_profile')
                ->nullOnDelete();
        });

        Schema::table('employee_attendanceboard', function (Blueprint $table) {
            $table->foreignId('company_profile_id')
                ->nullable()
                ->constrained('company_profile')
                ->nullOnDelete();
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->foreignId('company_profile_id')
                ->nullable()
                ->constrained('company_profile')
                ->nullOnDelete();
        });

        Schema::table('clients_payments', function (Blueprint $table) {
            $table->foreignId('company_profile_id')
                ->nullable()
                ->constrained('company_profile')
                ->nullOnDelete();
        });

        $defaultCompanyProfileId = DB::table('company_profile')
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->value('id');

        if (!$defaultCompanyProfileId) {
            return;
        }

        DB::table('employee')
            ->whereNull('company_profile_id')
            ->update(['company_profile_id' => $defaultCompanyProfileId]);

        DB::table('attendance')
            ->whereNull('company_profile_id')
            ->update(['company_profile_id' => $defaultCompanyProfileId]);

        DB::table('payments_board')
            ->whereNull('company_profile_id')
            ->update(['company_profile_id' => $defaultCompanyProfileId]);

        $attendanceCompanyMap = DB::table('attendance')
            ->pluck('company_profile_id', 'id');
        $employeeCompanyMap = DB::table('employee')
            ->pluck('company_profile_id', 'id');

        foreach (DB::table('employee_attendanceboard')
            ->select('id', 'attendance_id', 'employee_id')
            ->whereNull('company_profile_id')
            ->get() as $employeeAttendance) {
            $companyProfileId = $attendanceCompanyMap[$employeeAttendance->attendance_id]
                ?? $employeeCompanyMap[$employeeAttendance->employee_id]
                ?? $defaultCompanyProfileId;

            DB::table('employee_attendanceboard')
                ->where('id', $employeeAttendance->id)
                ->update(['company_profile_id' => $companyProfileId]);
        }

        $paymentsBoardCompanyMap = DB::table('payments_board')
            ->pluck('company_profile_id', 'id');
        $clientCompanyMap = DB::table('clients')
            ->pluck('company_profile_id', 'id');

        foreach (DB::table('clients_payments')
            ->select('id', 'payments_board_id', 'client_id')
            ->whereNull('company_profile_id')
            ->get() as $clientPayment) {
            $companyProfileId = $paymentsBoardCompanyMap[$clientPayment->payments_board_id]
                ?? $clientCompanyMap[$clientPayment->client_id]
                ?? $defaultCompanyProfileId;

            DB::table('clients_payments')
                ->where('id', $clientPayment->id)
                ->update(['company_profile_id' => $companyProfileId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_profile_id');
        });

        Schema::table('payments_board', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_profile_id');
        });

        Schema::table('employee_attendanceboard', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_profile_id');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_profile_id');
        });

        Schema::table('employee', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_profile_id');
        });
    }
};