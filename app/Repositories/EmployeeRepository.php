<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\DTO\Employee\ManageEmployeeDTO;
use App\Models\Employee;
use App\Models\EmployeeSalaries;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class EmployeeRepository
{
    /**
     * Create a new class instance.
     */
    protected ActionLogRepository $actionLogRepository;
    public function __construct(){
        $this->actionLogRepository = new ActionLogRepository();
    }

    /**
     * Get all employees with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllEmployees(array $filters = []): Collection
    {
        $query = Employee::with(['currentSalary']);

        // Filter by status (active/inactive)
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by department
        if (isset($filters['department']) && !empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        // Filter by gender
        if (isset($filters['gender']) && !empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Search by name or mobile number
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $searchTerm = $filters['search'];
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by joining date range
        if (isset($filters['joining_from']) && !empty($filters['joining_from'])) {
            $query->whereDate('joining_date', '>=', $filters['joining_from']);
        }

        if (isset($filters['joining_to']) && !empty($filters['joining_to'])) {
            $query->whereDate('joining_date', '<=', $filters['joining_to']);
        }

        return $query->orderBy('joining_date', 'desc')
                    ->orderBy('first_name', 'asc')
                    ->get();
    }

    /**
     * Store a new employee in the database
     *
     * @param ManageEmployeeDTO $employeeDTO
     * @return ResponseData
     */
    public function storeEmployee(ManageEmployeeDTO $employeeDTO): ResponseData
    {
        try {
            // Create a new employee using DTO data
            $employee = Employee::create([
                'first_name' => $employeeDTO->firstName,
                'last_name' => $employeeDTO->lastName,
                'gender' => $employeeDTO->gender,
                'mobile_number' => $employeeDTO->mobileNumber,
                'status' => $employeeDTO->status,
                'salary' => $employeeDTO->salary,
                'salary_hours' => $employeeDTO->salaryHours,
                'department' => $employeeDTO->department,
                'designation' => $employeeDTO->designation,
                'joining_date' => $employeeDTO->joiningDate,
                'leaving_date' => $employeeDTO->leavingDate,
            ]);

            // Create initial salary record if salary is provided
            if ($employeeDTO->salary > 0) {
                EmployeeSalaries::create([
                    'employee_id' => $employee->id,
                    'salary' => $employeeDTO->salary,
                    'effective_date' => $employeeDTO->joiningDate,
                ]);
            }

            // Load the employee with relationships
            $employee->load(['currentSalary']);

            return new SuccessData($employee->toArray());
        } catch (Exception $e) {

            // Log the error
            Log::error('Failed to create employee: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to create employee: ' . $e->getMessage()]);
        }
    }

    /**
     * Find an employee by ID
     *
     * @param int $id
     * @return Employee|null
     */
    public function findEmployee(int $id): ?Employee
    {
        return Employee::with(['salaries', 'currentSalary', 'latestSalary'])->find($id);
    }

    /**
     * Update an employee's information
     *
     * @param int $id
     * @param ManageEmployeeDTO $employeeDTO
     * @return ResponseData
     */
    public function updateEmployee(int $id, ManageEmployeeDTO $employeeDTO): ResponseData
    {
        try {
            $employee = Employee::findOrFail($id);

            // Update employee data excluding salary
            $employee->update([
                'first_name' => $employeeDTO->firstName,
                'last_name' => $employeeDTO->lastName,
                'gender' => $employeeDTO->gender,
                'mobile_number' => $employeeDTO->mobileNumber,
                'status' => $employeeDTO->status,
                'salary_hours' => $employeeDTO->salaryHours,
                'department' => $employeeDTO->department,
                'designation' => $employeeDTO->designation,
                'joining_date' => $employeeDTO->joiningDate,
                'leaving_date' => $employeeDTO->leavingDate,
            ]);

            return new SuccessData(['message' => 'Employee updated successfully']);
        } catch (Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return new ErrorData(['Failed to update employee information.']);
        }
    }

    /**
     * Delete an employee
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteEmployee(int $id): ResponseData
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                return new ErrorData(['Employee not found']);
            }

            // Delete related salary records first (cascade should handle this, but being explicit)
            $employee->salaries()->delete();

            // Delete the employee
            $employee->delete();

            return new SuccessData(['message' => 'Employee deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete employee: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete employee: ' . $e->getMessage()]);
        }
    }

    /**
     * Get employee statistics
     *
     * @param array $filters
     * @return array
     */
    public function getEmployeeStatistics(array $filters = []): array
    {
        $query = Employee::query();

        // Apply filters
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['department']) && !empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        if (isset($filters['joining_from']) && !empty($filters['joining_from'])) {
            $query->whereDate('joining_date', '>=', $filters['joining_from']);
        }

        if (isset($filters['joining_to']) && !empty($filters['joining_to'])) {
            $query->whereDate('joining_date', '<=', $filters['joining_to']);
        }

        return [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::active()->count(),
            'inactive_employees' => Employee::inactive()->count(),
            'total_monthly_salary' => Employee::active()->sum('salary'),
            'department_breakdown' => Employee::active()
                ->selectRaw('department, count(*) as count')
                ->groupBy('department')
                ->pluck('count', 'department')
                ->toArray(),
            'gender_breakdown' => Employee::active()
                ->selectRaw('gender, count(*) as count')
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray(),
        ];
    }

    /**
     * Get distinct departments
     *
     * @return SupportCollection
     */
    public function getDistinctDepartments(): SupportCollection
    {
        return Employee::distinct()->pluck('department');
    }



    /**
     * Search employees by name or mobile
     *
     * @param string $searchTerm
     * @return Collection
     */
    public function searchEmployees(string $searchTerm): Collection
    {
        return Employee::where(function($query) use ($searchTerm) {
                    $query->where('first_name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%");
                })
                ->orderBy('first_name', 'asc')
                ->get();
    }

    /**
     * Create or update salary record for an employee
     *
     * @param int $employeeId
     * @param int $salary
     * @param string $effectiveDate
     * @return ResponseData
     */
    public function updateEmployeeSalary(int $employeeId, int $salary, string $effectiveDate): ResponseData
    {
        try {
            $employee = Employee::find($employeeId);

            if (!$employee) {
                return new ErrorData(['Employee not found']);
            }

            // Create new salary record
            $salaryRecord = EmployeeSalaries::create([
                'employee_id' => $employeeId,
                'salary' => $salary,
                'effective_date' => $effectiveDate,
            ]);

            // Update the employee's current salary field
            $employee->update(['salary' => $salary]);

            return new SuccessData($salaryRecord->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update employee salary: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update employee salary: ' . $e->getMessage()]);
        }
    }

    /**
     * Get salary history for an employee
     *
     * @param int $employeeId
     * @return Collection
     */
    public function getEmployeeSalaryHistory(int $employeeId): Collection
    {
        return EmployeeSalaries::where('employee_id',$employeeId)
                              ->latest()
                              ->get();
    }

    /**
     * Pay advance amount to employee
     *
     * @param int $employeeId
     * @param float $advanceAmount
     * @param string|null $reason
     * @return ResponseData
     */
    public function payAdvanceToEmployee(int $employeeId, float $advanceAmount, ?string $reason = null): ResponseData
    {
        try {
            $employee = Employee::find($employeeId);

            if (!$employee) {
                return new ErrorData(['Employee not found']);
            }

            // Get current advance_due amount (assuming it exists in employees table)
            $currentAdvanceDue = $employee->advance_due ?? 0;

            // Add the new advance amount to existing advance due
            $newAdvanceDue = $currentAdvanceDue + $advanceAmount;

            // Update the employee's advance_due field
            $employee->update([
                'advance_due' => $newAdvanceDue
            ]);

            $this->actionLogRepository->logAdvancePayment(
                $employeeId,
                (string) $advanceAmount,
                auth()->id(),
                $reason
            );

            return new SuccessData([
                'updated_advance_due' => $newAdvanceDue,
                'message' => 'Advance payment processed successfully'
            ]);
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to process advance payment: ' . $e->getMessage()]);
        }
    }
}
