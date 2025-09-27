<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attendance_month_year',
        'start_date',
        'end_date',
        'total_days',
        'working_days',
        'employee_count',
        'total_salary_paid',
        'total_advance_paid',
        'total_overtime_hours',
        'total_overtime_paid',
        'previous_balance_adjusted',
        'balance_carry_forward',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_days' => 'integer',
        'working_days' => 'integer',
        'employee_count' => 'integer',
        'total_salary_paid' => 'decimal:2',
        'total_advance_paid' => 'decimal:2',
        'total_overtime_hours' => 'integer',
        'total_overtime_paid' => 'decimal:2',
        'previous_balance_adjusted' => 'decimal:2',
        'balance_carry_forward' => 'decimal:2',
    ];

    /**
     * Get the employee attendance boards for this attendance period.
     */
    public function employeeAttendanceboards(): HasMany
    {
        return $this->hasMany(EmployeeAttendanceboard::class);
    }

    /**
     * Scope to filter by month and year.
     */
    public function scopeForPeriod($query, $monthYear)
    {
        return $query->where('attendance_month_year', $monthYear);
    }

    /**
     * Scope to filter by year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('attendance_month_year', 'like', '%' . $year);
    }

    /**
     * Get the formatted month year for display.
     */
    public function getFormattedPeriodAttribute()
    {
        // Assuming format is like "01-2025" or "January-2025"
        return str_replace('-', ' ', $this->attendance_month_year);
    }

    /**
     * Get the total net salary after all calculations.
     */
    public function getTotalNetSalaryAttribute()
    {
        return $this->total_salary_paid + $this->total_overtime_paid + $this->total_bonus_paid - $this->total_advance_paid - $this->previous_balance_adjusted;
    }
}
