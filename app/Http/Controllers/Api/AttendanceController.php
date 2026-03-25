<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceboard;
use App\Repositories\AttendanceRepository;
use App\Repositories\ActionLogRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceRepository $attendanceRepository;
    protected ActionLogRepository $actionLogRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        ActionLogRepository $actionLogRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->actionLogRepository = $actionLogRepository;
    }

    public function index(Request $request): JsonResponse {
        $attendances = $this->attendanceRepository->getAllAttendances();
        return response()->json(['error' => false, 'message' => 'Attendances fetched successfully', 'data' => $attendances], 200);
    }

    public function show(int $id): JsonResponse
    {
        // Get attendance record from repository
        $attendance = $this->attendanceRepository->findById($id);

        if (!$attendance) {
            return response()->json(['error' => true, 'message' => 'Attendance record not found', 'data' => null], 404);
        }

        // Get employee attendance records for this period
        $employeeAttendance = $this->attendanceRepository->getEmployeeAttendanceByPeriod($id);


        return response()->json(['error' => false, 'message' => 'Attendance record fetched successfully', 'data' => [
            'attendance' => $attendance,
            'employee_attendance' => $employeeAttendance
        ]], 200);
    }

    public function processSalaryPayment(Request $request, int $recordId)
    {
        try {
            // Validate the request
            $request->validate([
                'amount_paid' => 'required|numeric|min:0.01',
                'record_id' => 'required|integer'
            ]);

            // Get the employee attendance record
            $employeeAttendance = EmployeeAttendanceboard::with('employee')->find($recordId);

            if (!$employeeAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee attendance record not found.'
                ], 404);
            }

            $amountPaid = (float) $request->input('amount_paid');
            $employee = $employeeAttendance->employee;

            // Start database transaction
            \DB::beginTransaction();

            // Update the paid amount in attendance record
            $employeeAttendance->paid_amount = $amountPaid;

            // Calculate balance carry forward
            $netSalary = $employeeAttendance->net_salary_after_deductions;
            $newBalanceCf = max(0, $amountPaid - $netSalary);
            $employeeAttendance->balance_carry_forward = $newBalanceCf;

            $employeeAttendance->save();

            // Update employee's advance_due and outstanding_balance
            if ($employeeAttendance->advance_deducted > 0) {
                $employee->advance_due = max(0, $employee->advance_due - $employeeAttendance->advance_deducted);

                // Log advance cleared
                $this->logAction(
                    'advance_cleared',
                    "Advance of ₹" . number_format($employeeAttendance->advance_deducted, 2) . " cleared for {$employee->first_name} {$employee->last_name}",
                    $employee->id,
                    $employeeAttendance->advance_deducted
                );
            }

            // Update employee's outstanding_balance with new balance carry forward
            $employee->outstanding_balance = $newBalanceCf;
            $employee->save();

            // Create action log for salary payment
            $this->logAction(
                'salary_paid',
                "Salary payment of ₹" . number_format($amountPaid, 2) . " made to {$employee->first_name} {$employee->last_name}",
                $employee->id,
                $amountPaid
            );

            // Log balance cleared if there was a previous balance
            if ($employeeAttendance->previous_balance > 0) {
                $this->logAction(
                    'balance_cleared',
                    "Previous balance of ₹" . number_format($employeeAttendance->previous_balance, 2) . " cleared for {$employee->first_name} {$employee->last_name}",
                    $employee->id,
                    $employeeAttendance->previous_balance
                );
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!',
                'data' => [
                    'new_paid_amount' => $amountPaid,
                    'balance_carry_forward' => $newBalanceCf,
                    'employee_advance_due' => $employee->advance_due,
                    'employee_outstanding_balance' => $employee->outstanding_balance
                ]
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function logAction(string $action, string $description, int $employeeId, float $amount = 0)
    {
        $this->actionLogRepository->createActionLog(
            \App\Models\ActionLog::RESOURCE_TYPE_EMPLOYEE,
            $employeeId,
            $action,
            (string) $amount,
            $description
        );
    }
}
