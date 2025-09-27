<?php

namespace App\Models\Sales;

use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'client_id',
        'sale_date',
        'sales_type',
        'base_amount',
        'tax_amount',
        'tax_rate',
        'total_amount',
        'tds',
        'tds_rate',
        'due_date',
        'paid',
        'payment_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tds' => 'decimal:2',
        'tds_rate' => 'decimal:2',
        'paid' => 'boolean',
    ];

    /**
     * Get the company profile that owns the sale.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get the client that owns the sale.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the payment associated with this sale.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Check if the sale is paid.
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * Check if the sale is unpaid.
     */
    public function isUnpaid(): bool
    {
        return !$this->paid;
    }

    /**
     * Get the payment status as a formatted string.
     */
    public function getPaymentStatusAttribute(): string
    {
        return $this->paid ? 'Paid' : 'Unpaid';
    }

    /**
     * Mark the sale as paid.
     */
    public function markAsPaid(?int $paymentId = null): void
    {
        $this->update([
            'paid' => true,
            'payment_id' => $paymentId,
        ]);
    }

    /**
     * Mark the sale as unpaid.
     */
    public function markAsUnpaid(): void
    {
        $this->update([
            'paid' => false,
            'payment_id' => null,
        ]);
    }

    /**
     * Scope a query to only include paid sales.
     */
    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    /**
     * Scope a query to only include unpaid sales.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }

    /**
     * Scope a query to filter by sales type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('sales_type', $type);
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_profile_id', $companyId);
    }

    /**
     * Get the formatted sales type.
     */
    public function getFormattedSalesTypeAttribute(): string
    {
        return $this->sales_type === 'cash' ? 'Cash Sale' : 'Invoice';
    }
}
