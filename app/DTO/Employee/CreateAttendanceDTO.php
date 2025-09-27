<?php

namespace App\DTO\Employee;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;
use Carbon\Carbon;

class CreateAttendanceDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $attendanceMonth,
        public int $attendanceYear,
        public int $totalDays,
        public string $attendanceMonthYear
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|CreateAttendanceDTO
     */
    public static function from(array $data): ErrorData|CreateAttendanceDTO
    {
        $attendanceMonth = intval($data['attendance_month'] ?? 0);
        $attendanceYear = intval($data['attendance_year'] ?? 0);

        // Calculate total days in the selected month
        $totalDays = 0;
        if ($attendanceMonth > 0 && $attendanceYear > 0) {
            $totalDays = Carbon::create($attendanceYear, $attendanceMonth, 1)->daysInMonth;
        }

        // Format as "Full month name, Year" (e.g., "September, 2025")
        $attendanceMonthYear = Carbon::create($attendanceYear, $attendanceMonth, 1)->format('F, Y');

        return (new self(
            $attendanceMonth,
            $attendanceYear,
            $totalDays,
            $attendanceMonthYear
        ))->validate();
    }

    /**
     * Validate the DTO data.
     *
     * @return ErrorData|CreateAttendanceDTO
     */
    public function validate(): ErrorData|CreateAttendanceDTO
    {
        $validator = Validator::make([
            'attendance_month' => $this->attendanceMonth,
            'attendance_year' => $this->attendanceYear,
            'total_days' => $this->totalDays,
            'attendance_month_year' => $this->attendanceMonthYear,
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
        return [
            'attendance_month' => 'required|integer|min:1|max:12',
            'attendance_year' => 'required|integer|min:2025',
            'total_days' => 'required|integer|min:28|max:31',
            'attendance_month_year' => 'required|string|unique:attendance,attendance_month_year',
        ];
    }

    /**
     * Get the formatted period for display.
     *
     * @return string
     */
    public function getFormattedPeriod(): string
    {
        return Carbon::create($this->attendanceYear, $this->attendanceMonth, 1)->format('F Y');
    }

    /**
     * Get total days in the selected month.
     *
     * @return int
     */
    public function getTotalDaysInMonth(): int
    {
        return $this->totalDays;
    }

    /**
     * Convert DTO to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attendance_month' => $this->attendanceMonth,
            'attendance_year' => $this->attendanceYear,
            'total_days' => $this->totalDays,
            'attendance_month_year' => $this->attendanceMonthYear,
        ];
    }
}
