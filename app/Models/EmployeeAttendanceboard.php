<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendanceboard extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_attendanceboard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'attendance_id',
        'per_day_salary',
        'per_hour_salary',
        'present_days',
        'overtime_hours',
        'working_days_salary',
        'overtime_amount',
        'bonus_amount',
        'total_salary',
        'advance_deducted',
        'previous_balance',
        'net_salary_after_deductions',
        'paid_amount',
        'balance_carry_forward',
//        'advance_due',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'per_day_salary' => 'decimal:2',
        'per_hour_salary' => 'decimal:2',
        'present_days' => 'integer',
        'overtime_hours' => 'decimal:2',
        'working_days_salary' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'advance_deducted' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'net_salary_after_deductions' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_carry_forward' => 'decimal:2',
//        'advance_due' => 'decimal:2',
    ];

    /**
     * Get the employee that owns this attendance board record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the attendance period this record belongs to.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Scope to filter by employee.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to filter by attendance period.
     */
    public function scopeForAttendancePeriod($query, $attendanceId)
    {
        return $query->where('attendance_id', $attendanceId);
    }

    /**
     * Scope to filter employees with outstanding advances.
     */
    public function scopeWithAdvanceDue($query)
    {
        return $query->where('advance_due', '>', 0);
    }

    /**
     * Scope to filter employees with balance carry forward.
     */
    public function scopeWithBalanceCarryForward($query)
    {
        return $query->where('balance_carry_forward', '>', 0);
    }

    /**
     * Get the attendance percentage for this employee.
     */
    public function getAttendancePercentageAttribute()
    {
        if ($this->attendance && $this->attendance->working_days > 0) {
            return round(($this->present_days / $this->attendance->working_days) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the total earnings (salary + overtime + bonus).
     */
    public function getTotalEarningsAttribute()
    {
        return $this->working_days_salary + $this->overtime_amount + $this->bonus_amount;
    }

    /**
     * Get the final amount to be paid (total - advances - previous balance).
     */
    public function getFinalPayableAmountAttribute()
    {
        return $this->total_salary - $this->advance_deducted - $this->previous_balance;
    }
}
