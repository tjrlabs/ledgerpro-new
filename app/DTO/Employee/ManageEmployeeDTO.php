<?php

namespace App\DTO\Employee;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;

class ManageEmployeeDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $gender,
        public string $mobileNumber,
        public string $department,
        public ?string $designation,
        public int $salary,
        public int $salaryHours,
        public string $joiningDate,
        public string $status,
        public ?string $leavingDate,
        public ?int $employeeId = null // Add employee ID for update operations
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|ManageEmployeeDTO
     */
    public static function from(array $data): ErrorData|ManageEmployeeDTO
    {
        return (new self(
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['gender'] ?? '',
            $data['mobile_number'] ?? '',
            $data['department'] ?? '',
            $data['designation'] ?? null,
            intval($data['salary'] ?? 0),
            intval($data['salary_hours'] ?? 8),
            $data['joining_date'] ?? '',
            $data['status'] ?? 'active',
            $data['leaving_date'] ?? null,
            isset($data['employee_id']) ? intval($data['employee_id']) : null
        ))->validate();
    }

    /**
     * Validate the DTO data.
     *
     * @return ErrorData|ManageEmployeeDTO
     */
    public function validate(): ErrorData|ManageEmployeeDTO
    {
        $validator = Validator::make([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'gender' => $this->gender,
            'mobile_number' => $this->mobileNumber,
            'department' => $this->department,
            'designation' => $this->designation,
            'salary' => $this->salary,
            'salary_hours' => $this->salaryHours,
            'joining_date' => $this->joiningDate,
            'status' => $this->status,
            'leaving_date' => $this->leavingDate,
        ], $this->rules());

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
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
        $mobileRule = 'nullable|digits:10';

        // Add unique validation with ignore for updates
        if ($this->employeeId) {
            $mobileRule .= '|unique:employee,mobile_number,' . $this->employeeId;
        } else {
            $mobileRule .= '|unique:employee,mobile_number';
        }

        $rules = [
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'gender' => 'required|in:male,female',
            'mobile_number' => $mobileRule,
            'department' => 'required|string',
            'designation' => 'nullable|string',
            'salary_hours' => 'required|integer|min:1|max:24',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'leaving_date' => 'nullable|date|after:joining_date'
        ];

        // Only validate salary for new employees
        if (!$this->employeeId) {
            $rules['salary'] = 'required|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'mobile_number.unique' => 'This mobile number is already registered with another employee.',
            'mobile_number.digits' => 'Mobile number must be exactly 10 digits.',
            'leaving_date.required_if' => 'Leaving date is required for inactive employees.',
            'leaving_date.after' => 'Leaving date must be after joining date.',
            'joining_date.before_or_equal' => 'Joining date cannot be in the future.',
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters long.',
            'first_name.max' => 'First name cannot exceed 50 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters long.',
            'last_name.max' => 'Last name cannot exceed 50 characters.',
            'gender.required' => 'Please select a gender.',
            'department.required' => 'Please select a department.',
            'salary.required' => 'Monthly salary is required.',
            'salary.min' => 'Salary must be greater than 0.',
        ];
    }

    /**
     * Convert DTO to array for repository usage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'gender' => $this->gender,
            'mobile_number' => $this->mobileNumber,
            'department' => $this->department,
            'designation' => $this->designation,
            'salary' => $this->salary,
            'salary_hours' => $this->salaryHours,
            'joining_date' => $this->joiningDate,
            'status' => $this->status,
            'leaving_date' => $this->leavingDate,
        ];
    }
}
