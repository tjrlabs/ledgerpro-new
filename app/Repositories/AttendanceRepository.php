<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\DTO\Employee\CreateAttendanceDTO;
use App\DTO\Employee\UpdateAttendanceDTO;
use App\Models\Attendance;
use App\Models\EmployeeAttendanceboard;
use App\Models\Employee;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    /**
     * Get all attendance records with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllAttendances(array $filters = []): Collection
    {
        $query = Attendance::query();

        // Filter by year
        if (isset($filters['year']) && !empty($filters['year'])) {
            $query->forYear($filters['year']);
        }

        // Filter by period
        if (isset($filters['period']) && !empty($filters['period'])) {
            $query->where('attendance_month_year', $filters['period']);
        }

        return $query->orderBy('attendance_month_year', 'desc')->get();
    }

    /**
     * Create a new attendance record
     *
     * @param CreateAttendanceDTO $attendanceDTO
     * @return ResponseData
     */
    public function createAttendance(CreateAttendanceDTO $attendanceDTO): ResponseData
    {
        try {
            DB::beginTransaction();

            // Check if attendance for this period already exists
            $existingAttendance = $this->findByPeriod($attendanceDTO->attendanceMonthYear);
            if ($existingAttendance) {
                return new ErrorData(['Attendance board already exists for ' . $attendanceDTO->getFormattedPeriod()]);
            }

            // Create the main attendance record
            $attendance = Attendance::create([
                'attendance_month_year' => $attendanceDTO->attendanceMonthYear,
                'start_date' => Carbon::create($attendanceDTO->attendanceYear, $attendanceDTO->attendanceMonth, 1, '00', '00', '00')->startOfMonth()->toDateTimeString(),
                'end_date' => Carbon::create($attendanceDTO->attendanceYear, $attendanceDTO->attendanceMonth, 1, '23', '59', '59')->endOfMonth()->toDateTimeString(),
                'total_days' => $attendanceDTO->totalDays,
                'employee_count' => 0,
                'total_salary_paid' => 0,
                'total_advance_paid' => 0,
                'total_overtime_hours' => 0,
                'total_overtime_paid' => 0,
                'previous_balance_adjusted' => 0,
                'balance_carry_forward' => 0,
            ]);

            DB::commit();

            return new SuccessData([ 'message' => 'Attendance board created successfully for ' . $attendanceDTO->getFormattedPeriod() ]);

        } catch (Exception $e) {
            DB::rollBack();
            return new ErrorData(['Failed to create attendance board: ' . $e->getMessage()]);
        }
    }

    /**
     * Find attendance by period
     *
     * @param string $period
     * @return Attendance|null
     */
    public function findByPeriod(string $period): ?Attendance
    {
        return Attendance::where('attendance_month_year', $period)->first();
    }

    /**
     * Find attendance by ID
     *
     * @param int $id
     * @return Attendance|null
     */
    public function findById(int $id): ?Attendance
    {
        return Attendance::find($id);
    }

    /**
     * Update attendance record
     *
     * @param int $id
     * @param array $data
     * @return ResponseData
     */
    public function updateAttendance(int $id, array $data): ResponseData
    {
        try {
            $attendance = $this->findById($id);

            if (!$attendance) {
                return new ErrorData(['Attendance record not found']);
            }

            $attendance->update($data);

            return new SuccessData($attendance->toArray(), 'Attendance board updated successfully');

        } catch (Exception $e) {

            return new ErrorData(['Failed to update attendance board: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete attendance record
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteAttendance(int $id): ResponseData
    {
        try {
            DB::beginTransaction();

            $attendance = $this->findById($id);

            if (!$attendance) {
                return new ErrorData(['Attendance record not found']);
            }

            // Delete associated employee attendance board records
            EmployeeAttendanceboard::where('attendance_id', $id)->delete();

            // Delete the main attendance record
            $attendance->delete();

            DB::commit();


            return new SuccessData(['message' => 'Attendance board deleted successfully']);

        } catch (Exception $e) {
            DB::rollBack();

            return new ErrorData(['Failed to delete attendance board: ' . $e->getMessage()]);
        }
    }

    /**
     * Get employee attendance records for a specific attendance period
     *
     * @param int $attendanceId
     * @return Collection
     */
    public function getEmployeeAttendanceByPeriod(int $attendanceId): Collection
    {
        return EmployeeAttendanceboard::with('employee')
            ->where('attendance_id', $attendanceId)
            ->orderBy('employee_id')
            ->get();
    }

    /**
     * Add an employee to the attendance board
     *
     * @param array $employeeAttendanceData
     * @return ResponseData
     */
    public function addEmployeeToAttendance(array $employeeAttendanceData): ResponseData
    {
        try {
            // Check if employee is already in the attendance board
            $existingRecord = EmployeeAttendanceboard::where('attendance_id', $employeeAttendanceData['attendance_id'])
                ->where('employee_id', $employeeAttendanceData['employee_id'])
                ->first();

            if ($existingRecord) {
                return new ErrorData(['Employee is already in the attendance board']);
            }

            // Create the employee attendance record
            $employeeAttendance = EmployeeAttendanceboard::create($employeeAttendanceData);

            return new SuccessData($employeeAttendance->toArray(), 'Employee added to attendance board successfully');

        } catch (Exception $e) {
            return new ErrorData(['Failed to add employee to attendance board: ' . $e->getMessage()]);
        }
    }

    /**
     * Update attendance board with employee data
     *
     * @param UpdateAttendanceDTO $updateAttendanceDTO
     * @return ResponseData
     */
    public function updateAttendanceBoard(UpdateAttendanceDTO $updateAttendanceDTO): ResponseData
    {
        try {
            DB::beginTransaction();

            $attendance = $this->findById($updateAttendanceDTO->getAttendanceId());

            if (!$attendance) {
                return new ErrorData(['Attendance record not found']);
            }

            $employees = $updateAttendanceDTO->getEmployees();
            $updatedCount = 0;
            $errors = [];

            foreach ($employees as $employeeData) {
                try {
                    $result = $this->updateEmployeeAttendance($employeeData, $attendance->id);

                    if ($result instanceof ErrorData) {
                        $errors[] = "Failed to update employee ID {$employeeData['employee_id']}: " . implode(', ', $result->getErrorMessages());
                    } else {
                        $updatedCount++;
                    }
                } catch (Exception $e) {
                    $errors[] = "Failed to update employee ID {$employeeData['employee_id']}: " . $e->getMessage();
                }
            }

            // Update attendance summary
            $this->updateAttendanceSummary($attendance);

            DB::commit();

            if (!empty($errors)) {
                return new ErrorData([
                    "Updated {$updatedCount} employee(s) successfully. Some errors occurred: " . implode(', ', $errors)
                ]);
            }

            return new SuccessData([
                'updated_count' => $updatedCount,
                'message' => "Successfully updated {$updatedCount} employee attendance record(s)"
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return new ErrorData(['Failed to update attendance records: ' . $e->getMessage()]);
        }
    }

    /**
     * Update individual employee attendance record
     *
     * @param array $employeeData
     * @param int $attendanceId
     * @return ResponseData
     */
    public function updateEmployeeAttendance(array $employeeData, int $attendanceId): ResponseData
    {
        try {
            // Find the employee attendance record
            $employeeAttendance = EmployeeAttendanceboard::where('id', $employeeData['id'])
                ->where('attendance_id', $attendanceId)
                ->first();

            if (!$employeeAttendance) {
                return new ErrorData(['Employee attendance record not found']);
            }

            // Calculate salary based on present days and overtime
            $perDayRate = $employeeAttendance->per_day_salary ?? 0;
            $perHourRate = $employeeAttendance->per_hour_salary ?? 0;

            $workingDaysSalary = $employeeData['present_days'] * $perDayRate;
            $overtimeAmount = $employeeData['overtime_hours'] * $perHourRate;
            $bonusAmount = $employeeData['bonus_amount'];
            $advanceDeducted = $employeeData['advance_deducted'];
            //$previousBalance = $employeeAttendance->previous_balance ?? 0;
            $previousBalance = $employeeAttendance->employee->outstanding_balance ?? 0;

            $totalSalary = ceil($workingDaysSalary + $overtimeAmount + $bonusAmount);
            $netSalaryAfterDeductions = ceil($totalSalary - $advanceDeducted - $previousBalance);

            // Update the employee attendance record
            $employeeAttendance->update([
                'present_days' => $employeeData['present_days'],
                'overtime_hours' => $employeeData['overtime_hours'],
                'working_days_salary' => $workingDaysSalary,
                'overtime_amount' => $overtimeAmount,
                'bonus_amount' => $bonusAmount,
                'total_salary' => $totalSalary,
                'advance_deducted' => $advanceDeducted,
                'previous_balance' => $previousBalance,
                'net_salary_after_deductions' => $netSalaryAfterDeductions,
                'remarks' => $employeeData['remarks'],
            ]);

            return new SuccessData($employeeAttendance->toArray(), 'Employee attendance updated successfully');

        } catch (Exception $e) {
            return new ErrorData(['Failed to update employee attendance: ' . $e->getMessage()]);
        }
    }

    /**
     * Update attendance summary totals
     *
     * @param Attendance $attendance
     * @return void
     */
    public function updateAttendanceSummary(Attendance $attendance): void
    {
        try {
            $employeeAttendanceRecords = EmployeeAttendanceboard::where('attendance_id', $attendance->id)->get();

            $totalSalaryPaid = $employeeAttendanceRecords->sum('total_salary');
            $totalAdvancePaid = $employeeAttendanceRecords->sum('advance_deducted');
            $totalOvertimeHours = $employeeAttendanceRecords->sum('overtime_hours');
            $totalOvertimePaid = $employeeAttendanceRecords->sum('overtime_amount');
            $employeeCount = $employeeAttendanceRecords->count();

            $attendance->update([
                'employee_count' => $employeeCount,
                'total_salary_paid' => $totalSalaryPaid,
                'total_advance_paid' => $totalAdvancePaid,
                'total_overtime_hours' => $totalOvertimeHours,
                'total_overtime_paid' => $totalOvertimePaid,
            ]);
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            \Log::error('Failed to update attendance summary: ' . $e->getMessage());
        }
    }

    /**
     * Get available attendance periods for dropdown
     *
     * @return Collection
     */
    public function getAvailablePeriods(): Collection
    {
        return Attendance::select('attendance_month_year')
            ->distinct()
            ->orderBy('attendance_month_year', 'desc')
            ->pluck('attendance_month_year');
    }

    /**
     * Get month and year options for form dropdowns
     *
     * @return array
     */
    public function getFormOptions(): array
    {
        // Generate month options (01-12)
        $monthOptions = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthName = Carbon::createFromFormat('m', $monthNumber)->format('F');
            $monthOptions[$monthNumber] = $monthName;
        }

        // Generate year options (2025 to current year + 1)
        $yearOptions = [];
        $startYear = 2025;
        $endYear = (int)Carbon::now()->format('Y') + 1;
        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearOptions[$year] = $year;
        }

        return [
            'monthOptions' => $monthOptions,
            'yearOptions' => $yearOptions,
            'currentMonth' => Carbon::now()->format('m'),
            'currentYear' => Carbon::now()->format('Y'),
        ];
    }
}
