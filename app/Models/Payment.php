<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'company_profile_id',
        'client_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->uuid)) {
                $payment->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the client that owns the payment.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the company profile that owns the payment.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get the formatted amount paid.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount_paid, 2);
    }

    /**
     * Get the formatted payment date.
     */
    public function getFormattedPaymentDateAttribute(): string
    {
        return $this->payment_date->format('M d, Y');
    }

    /**
     * Get the payment method with proper formatting.
     */
    public function getFormattedPaymentMethodAttribute(): string
    {
        return $this->payment_method ? ucfirst(str_replace('_', ' ', $this->payment_method)) : 'Not specified';
    }

    /**
     * Scope a query to only include payments for a specific client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope a query to only include payments within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        return $query->whereDate('payment_date', '>=', $startDate);
    }

    /**
     * Scope a query to only include payments by payment method.
     */
    public function scopeByPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    /**
     * Get payments for the current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
    }

    /**
     * Get payments for the current year.
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }
}
