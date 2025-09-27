<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'mobile_number',
        'status',
        'salary',
        'salary_hours',
        'department',
        'designation',
        'joining_date',
        'leaving_date',
        'advance_due',
        'outstanding_balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'salary' => 'integer',
        'salary_hours' => 'integer',
        'advance_due' => 'decimal:2',
    ];

    /**
     * Get the employee's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the employee's initials.
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /**
     * Check if the employee is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get all salary records for the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaries()
    {
        return $this->hasMany(EmployeeSalaries::class);
    }

    /**
     * Get the latest salary record for the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestSalary()
    {
        return $this->hasOne(EmployeeSalaries::class)->latest('effective_date');
    }

    /**
     * Get the current effective salary for the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentSalary()
    {
        return $this->hasOne(EmployeeSalaries::class)
                    ->where('effective_date', '<=', now())
                    ->latest('effective_date');
    }

    /**
     * Scope a query to filter by department.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to only include active employees.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive employees.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
