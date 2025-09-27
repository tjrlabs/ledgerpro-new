<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBalance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees_balance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'employee_id',
        'month',
        'year',
        'opening_advance_balance',
        'opening_amount_balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'opening_advance_balance' => 'decimal:2',
        'opening_amount_balance' => 'decimal:2',
    ];

    /**
     * Get the company profile that owns the employee balance.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get the employee that owns the balance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to filter by month and year.
     */
    public function scopeForPeriod($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Get the total opening balance (advance + amount).
     */
    public function getTotalOpeningBalanceAttribute(): float
    {
        return $this->opening_advance_balance + $this->opening_amount_balance;
    }
}
