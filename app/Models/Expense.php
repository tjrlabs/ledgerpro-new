<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'expense_date',
        'expense_type',
        'base_amount',
        'tax_amount',
        'tax_rate',
        'total_amount',
        'paid',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid' => 'boolean',
    ];

    /**
     * The expense types available.
     */
    public const EXPENSE_TYPES = [
        'cash' => 'Cash',
        'invoice' => 'Invoice',
    ];

    /**
     * Get the company profile that owns the expense.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Scope to filter expenses by company profile.
     */
    public function scopeForCompany($query, $companyProfileId)
    {
        return $query->where('company_profile_id', $companyProfileId);
    }

    /**
     * Scope to filter paid expenses.
     */
    public function scopePaid($query)
    {
        return $query->where('paid', 1);
    }

    /**
     * Scope to filter unpaid expenses.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid', 0);
    }

    /**
     * Scope to filter expenses by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('expense_type', $type);
    }

    /**
     * Scope to filter expenses by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Get the formatted expense type.
     */
    public function getFormattedExpenseTypeAttribute(): string
    {
        return self::EXPENSE_TYPES[$this->expense_type] ?? ucfirst($this->expense_type);
    }

    /**
     * Get the payment status as a string.
     */
    public function getPaymentStatusAttribute(): string
    {
        return $this->paid ? 'Paid' : 'Unpaid';
    }

    /**
     * Check if the expense is paid.
     */
    public function isPaid(): bool
    {
        return (bool) $this->paid;
    }

    /**
     * Check if the expense is unpaid.
     */
    public function isUnpaid(): bool
    {
        return !$this->isPaid();
    }

    /**
     * Mark the expense as paid.
     */
    public function markAsPaid(): bool
    {
        return $this->update(['paid' => 1]);
    }

    /**
     * Mark the expense as unpaid.
     */
    public function markAsUnpaid(): bool
    {
        return $this->update(['paid' => 0]);
    }
}
