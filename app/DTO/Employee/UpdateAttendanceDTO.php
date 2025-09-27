<?php

namespace App\DTO\Employee;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;

class UpdateAttendanceDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $attendanceId,
        public array $employees
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|UpdateAttendanceDTO
     */
    public static function from(array $data): ErrorData|UpdateAttendanceDTO
    {
        $attendanceId = intval($data['attendance_id'] ?? 0);
        $employees = $data['employees'] ?? [];

        // Process and validate each employee data
        $processedEmployees = [];
        foreach ($employees as $index => $employeeData) {
            $processedEmployees[] = [
                'id' => intval($employeeData['id'] ?? 0),
                'employee_id' => intval($employeeData['employee_id'] ?? 0),
                'present_days' => intval($employeeData['present_days'] ?? 0),
                'overtime_hours' => floatval($employeeData['overtime_hours'] ?? 0),
                'bonus_amount' => floatval($employeeData['bonus_amount'] ?? 0),
                'advance_deducted' => floatval($employeeData['advance_deducted'] ?? 0),
                'remarks' => $employeeData['remarks'] ?? null,
            ];
        }

        return (new self(
            $attendanceId,
            $processedEmployees
        ))->validate();
    }

    /**
     * Validate the DTO data.
     *
     * @return ErrorData|UpdateAttendanceDTO
     */
    public function validate(): ErrorData|UpdateAttendanceDTO
    {
        $validator = Validator::make([
            'attendance_id' => $this->attendanceId,
            'employees' => $this->employees,
        ], $this->rules());

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        // Additional validation for each employee
        foreach ($this->employees as $index => $employee) {
            $employeeValidator = Validator::make($employee, $this->employeeRules(), $this->employeeMessages($index));

            if ($employeeValidator->fails()) {
                return new ErrorData($employeeValidator->errors()->all());
            }
        }

        return $this;
    }

    /**
     * Get validation rules for the DTO.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'attendance_id' => 'required|integer|exists:attendance,id',
            'employees' => 'required|array|min:1',
        ];
    }

    /**
     * Get validation rules for individual employees.
     *
     * @return array
     */
    public function employeeRules(): array
    {
        return [
            'id' => 'required|integer|exists:employee_attendanceboard,id',
            'employee_id' => 'required|integer|exists:employee,id',
            'present_days' => 'required|integer|min:0|max:31',
            'overtime_hours' => 'nullable|numeric',
            'bonus_amount' => 'nullable|numeric|min:0',
            'advance_deducted' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages for employees.
     *
     * @param int $index
     * @return array
     */
    public function employeeMessages(int $index): array
    {
        return [
            'id.required' => "Employee record ID is required for employee at position " . ($index + 1),
            'id.exists' => "Invalid employee attendance record for employee at position " . ($index + 1),
            'employee_id.required' => "Employee ID is required for employee at position " . ($index + 1),
            'employee_id.exists' => "Invalid employee ID for employee at position " . ($index + 1),
            'present_days.required' => "Present days is required for employee at position " . ($index + 1),
            'present_days.min' => "Present days cannot be negative for employee at position " . ($index + 1),
            'present_days.max' => "Present days cannot exceed 31 for employee at position " . ($index + 1),
            //'overtime_hours.min' => "Overtime hours cannot be negative for employee at position " . ($index + 1),
            'bonus_amount.min' => "Bonus amount cannot be negative for employee at position " . ($index + 1),
            'advance_deducted.min' => "Advance deducted cannot be negative for employee at position " . ($index + 1),
            'remarks.max' => "Remarks cannot exceed 500 characters for employee at position " . ($index + 1),
        ];
    }

    /**
     * Get the attendance ID.
     *
     * @return int
     */
    public function getAttendanceId(): int
    {
        return $this->attendanceId;
    }

    /**
     * Get the employees data.
     *
     * @return array
     */
    public function getEmployees(): array
    {
        return $this->employees;
    }

    /**
     * Get employee count.
     *
     * @return int
     */
    public function getEmployeeCount(): int
    {
        return count($this->employees);
    }

    /**
     * Convert DTO to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attendance_id' => $this->attendanceId,
            'employees' => $this->employees,
        ];
    }
}
