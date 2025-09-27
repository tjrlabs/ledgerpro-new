<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'uuid',
        'client_id',
        'transaction_type',
        'transaction_date',
        'sales_type',
        'base_amount',
        'tax_amount',
        'tax_rate',
        'tds',
        'tds_rate',
        'total_amount',
        'due_date',
        'paid',
        'payment_id',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'date',
        'due_date' => 'date',
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tds' => 'decimal:2',
        'tds_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the company profile that owns the transaction.
     */
    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get the client that owns the transaction.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the payment transaction (for sales transactions).
     */
    public function payment()
    {
        return $this->belongsTo(Transaction::class, 'payment_id');
    }

    /**
     * Get the sales transactions that this payment covers.
     */
    public function salesTransactions()
    {
        return $this->hasMany(Transaction::class, 'payment_id');
    }

    /**
     * Scope a query to only include sales transactions.
     */
    public function scopeSales($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    /**
     * Scope a query to only include payment transactions.
     */
    public function scopePayments($query)
    {
        return $query->where('transaction_type', 'payment');
    }

    /**
     * Scope a query to only include cash sales.
     */
    public function scopeCashSales($query)
    {
        return $query->where('transaction_type', 'sale')
                    ->where('sales_type', 'cash');
    }

    /**
     * Scope a query to only include invoice sales.
     */
    public function scopeInvoiceSales($query)
    {
        return $query->where('transaction_type', 'sale')
                    ->where('sales_type', 'invoice');
    }

    /**
     * Scope a query to only include paid sales.
     */
    public function scopePaidSales($query)
    {
        return $query->where('transaction_type', 'sale')
                    ->where('paid', true);
    }

    /**
     * Scope a query to only include unpaid sales.
     */
    public function scopeUnpaidSales($query)
    {
        return $query->where('transaction_type', 'sale')
                    ->where('paid', false);
    }
}
