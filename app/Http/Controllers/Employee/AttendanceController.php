<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTO\Employee\CreateAttendanceDTO;
use App\DTO\Employee\UpdateAttendanceDTO;
use App\Repositories\AttendanceRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\ActionLogRepository;
use App\Classes\ErrorData;
use App\Classes\SuccessData;
use App\Models\Attendance;
use App\Models\EmployeeAttendanceboard;

class AttendanceController extends Controller
{
    protected AttendanceRepository $attendanceRepository;
    protected EmployeeRepository $employeeRepository;
    protected ActionLogRepository $actionLogRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        EmployeeRepository $employeeRepository,
        ActionLogRepository $actionLogRepository
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->employeeRepository = $employeeRepository;
        $this->actionLogRepository = $actionLogRepository;
    }

    /**
     * Display all attendance summaries in the system
     */
    public function index(Request $request)
    {
        // Get all attendance summaries from repository
        $attendances = $this->attendanceRepository->getAllAttendances();

        return view('pages.attendance.index', compact(
            'attendances'
        ));
    }

    /**
     * Show the form for creating a new attendance board
     */
    public function create()
    {
        // Get form options from repository
        $formOptions = $this->attendanceRepository->getFormOptions();

        return view('pages.attendance.create', $formOptions);
    }

    /**
     * Store a newly created attendance board in storage
     */
    public function store(Request $request)
    {
        // Create DTO from request and validate
        $attendanceDTO = CreateAttendanceDTO::from($request->all());

        // Handle validation errors
        if ($attendanceDTO instanceof ErrorData) {
            return back()->withErrors($attendanceDTO->getErrorMessages())->withInput();
        }

        // Use repository to create attendance
        $result = $this->attendanceRepository->createAttendance($attendanceDTO);

        // Handle repository response
        if ($result instanceof ErrorData) {
            return back()->withErrors($result->getErrorMessages())->withInput();
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance board created successfully');
    }

    /**
     * Display the specified attendance record with employee details
     */
    public function show(int $id)
    {
        // Get attendance record from repository
        $attendance = $this->attendanceRepository->findById($id);

        if (!$attendance) {
            return redirect()->route('attendance.index')
                ->with('error', 'Attendance record not found');
        }

        // Get employee attendance records for this period
        $employeeAttendance = $this->attendanceRepository->getEmployeeAttendanceByPeriod($id);


        return view('pages.attendance.show', compact(
            'attendance',
            'employeeAttendance'
        ));
    }

    /**
     * Show the form for editing the specified attendance record
     */
    public function edit(int $id)
    {
        // Get attendance record from repository
        $attendance = $this->attendanceRepository->findById($id);

        if (!$attendance) {
            return redirect()->route('attendance.index')
                ->with('error', 'Attendance record not found');
        }

        // Get employee attendance records for this period
        $employeeAttendance = $this->attendanceRepository->getEmployeeAttendanceByPeriod($id);

        // Get form options from repository
        $formOptions = $this->attendanceRepository->getFormOptions();

        // Parse the current month and year from attendance_month_year
        $monthYear = explode(' ', $attendance->attendance_month_year);
        $currentMonth = $monthYear[0];
        $currentYear = $monthYear[1];

        // Get active employees for adding to attendance
        $activeEmployees = $this->employeeRepository->getAllEmployees(['status' => 'active']);

        return view('pages.attendance.edit', array_merge($formOptions, compact(
            'attendance',
            'employeeAttendance',
            'currentMonth',
            'currentYear',
            'activeEmployees'
        )));
    }

    /**
     * Update the specified attendance record in storage
     */
    public function update(Request $request, int $id)
    {
        // Create DTO from request and validate
        $requestData = array_merge($request->all(), ['attendance_id' => $id]);
        $updateAttendanceDTO = UpdateAttendanceDTO::from($requestData);

        // Handle validation errors
        if ($updateAttendanceDTO instanceof ErrorData) {
            return back()->withErrors($updateAttendanceDTO->getErrorMessages())->withInput();
        }

        // Use repository to update attendance board
        $result = $this->attendanceRepository->updateAttendanceBoard($updateAttendanceDTO);

        // Handle repository response
        if ($result instanceof ErrorData) {
            return redirect()->route('attendance.edit', $id)
                ->with('error', $result->getErrorMessages()[0] ?? 'Some errors occurred during update');
        }

        // Success case - extract message from SuccessData
        $message = 'Attendance board updated successfully';
        if ($result instanceof SuccessData && isset($result->data['message'])) {
            $message = $result->data['message'];
        }

        return redirect()->route('attendance.edit', $id)
            ->with('success', $message);
    }

    /**
     * Remove the specified attendance record and its associated employee records
     */
    public function destroy(int $id)
    {
        // Use repository to delete attendance
        $result = $this->attendanceRepository->deleteAttendance($id);

        // Handle repository response
        if ($result instanceof ErrorData) {
            return redirect()->route('attendance.index')
                ->with('error', $result->getErrorMessages()[0] ?? 'Failed to delete attendance board');
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance board deleted successfully');
    }

    /**
     * Get all active employees with attendance board status for a specific attendance ID
     */
    public function getEmployeesForAttendance(Attendance $attendance)
    {
        try {
            // Get all active employees
            $activeEmployees = $this->employeeRepository->getAllEmployees(['status' => 'active']);

            // Get employees already present in the attendance board
            $employeesInBoard = $this->attendanceRepository->getEmployeeAttendanceByPeriod($attendance->id)
                ->pluck('employee_id')
                ->toArray();

            // Format the response data
            $employeeData = $activeEmployees->map(function ($employee) use ($employeesInBoard) {
                return [
                    'id' => $employee->id,
                    'employee_name' => $employee->first_name .' '. $employee->last_name,
                    'salary' => $employee->salary,
                    'joining_date' => $employee->joining_date,
                    'working_hours' => $employee->salary_hours,
                    'is_in_board' => in_array($employee->id, $employeesInBoard) ? 1 : 0
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $employeeData,
                'attendance_period' => $attendance->attendance_month_year,
                'message' => 'Employees retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add employees to the attendance board
     */
    public function addEmployeesToAttendance(Request $request, Attendance $attendance)
    {
        try {
            // Validate the request
            $request->validate([
                'employees' => 'required|array|min:1',
                'employees.*' => 'required|integer|exists:employee,id'
            ]);

            $employeeIds = $request->input('employees');

            // Get employees already present in the attendance board
            $existingEmployeeIds = $this->attendanceRepository->getEmployeeAttendanceByPeriod($attendance->id)
                ->pluck('employee_id')
                ->toArray();

            // Filter out employees that already exist in the board
            $newEmployeeIds = array_diff($employeeIds, $existingEmployeeIds);

            if (empty($newEmployeeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected employees are already in the attendance board'
                ], 422);
            }

            // Get employee details for the new employees
            $newEmployees = $this->employeeRepository->getAllEmployees(['status' => 'active'])
                ->whereIn('id', $newEmployeeIds);

            $addedEmployees = [];
            $failedEmployees = [];

            foreach ($newEmployees as $employee) {
                try {
                    // Calculate per day and per hour salary
                    $perDaySalary = $attendance->total_days > 0 ? round($employee->salary / $attendance->total_days, 2) : 0;
                    $perHourSalary = $employee->salary_hours > 0 ? round($perDaySalary / $employee->salary_hours, 2) : 0;

                    // Create employee attendance record
                    $employeeAttendanceData = [
                        'employee_id' => $employee->id,
                        'attendance_id' => $attendance->id,
                        'per_day_salary' => $perDaySalary,
                        'per_hour_salary' => $perHourSalary,
                        'present_days' => 0,
                        'overtime_hours' => 0,
                        'working_days_salary' => 0,
                        'overtime_amount' => 0,
                        'bonus_amount' => 0,
                        'total_salary' => 0,
                        'advance_deducted' => 0,
                        'previous_balance' => 0,
                        'net_salary_after_deductions' => 0,
                        'paid_amount' => 0,
                        'balance_carry_forward' => 0,
//                        'advance_due' => 0,
                        'remarks' => null
                    ];

                    // Use repository to create the attendance record
                    $result = $this->attendanceRepository->addEmployeeToAttendance($employeeAttendanceData);

                    if ($result instanceof ErrorData) {
                        $failedEmployees[] = $employee->first_name . ' ' . $employee->last_name;
                    } else {
                        $addedEmployees[] = $employee->first_name . ' ' . $employee->last_name;
                    }

                } catch (\Exception $e) {
                    $failedEmployees[] = $employee->first_name . ' ' . $employee->last_name;
                }
            }

            // Update attendance employee count
            $totalEmployeesInBoard = count($existingEmployeeIds) + count($addedEmployees);
            $attendance->update(['employee_count' => $totalEmployeesInBoard]);

            $message = count($addedEmployees) . ' employee(s) added successfully';
            if (!empty($failedEmployees)) {
                $message .= '. Failed to add: ' . implode(', ', $failedEmployees);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'added_count' => count($addedEmployees),
                    'failed_count' => count($failedEmployees),
                    'added_employees' => $addedEmployees,
                    'failed_employees' => $failedEmployees,
                    'total_employees_in_board' => $totalEmployeesInBoard
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove an employee from the attendance board
     */
    public function removeEmployeeFromAttendance(Attendance $attendance, int $employeeAttendanceId)
    {
        try {
            // Find the employee attendance record
            $employeeAttendance = EmployeeAttendanceboard::where('id', $employeeAttendanceId)
                ->where('attendance_id', $attendance->id)
                ->first();

            if (!$employeeAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee attendance record not found'
                ], 404);
            }

            // Get employee name for response
            $employeeName = $employeeAttendance->employee->first_name . ' ' . $employeeAttendance->employee->last_name;

            // Delete the employee attendance record
            $employeeAttendance->delete();

            // Update attendance employee count
            $currentCount = EmployeeAttendanceboard::where('attendance_id', $attendance->id)->count();
            $attendance->update(['employee_count' => $currentCount]);

            return response()->json([
                'success' => true,
                'message' => $employeeName . ' has been removed from the attendance board',
                'data' => [
                    'removed_employee' => $employeeName,
                    'remaining_employees_count' => $currentCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update attendance summary totals
     */
    private function updateAttendanceSummary(Attendance $attendance)
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
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            \Log::error('Failed to update attendance summary: ' . $e->getMessage());
        }
    }

    /**
     * Process salary payment for an employee attendance record
     */
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

    /**
     * Log an action to the action logs table using ActionLogRepository
     */
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
