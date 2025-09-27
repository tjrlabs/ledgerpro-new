<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reports\PaymentsBoard;
use App\Models\Client;

class ClientsPayments extends Model
{
    use HasFactory;

    protected $table = 'clients_payments';

    protected $fillable = [
        'payments_board_id',
        'client_id',
        'cash_sales',
        'pre_gst_amount',
        'gst_amount',
        'tds',
        'subtotal_amount',
        'previous_balance',
        'total_amount',
        'paid_amount',
        'remarks',
    ];

    protected $casts = [
        'cash_sales' => 'decimal:2',
        'pre_gst_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'tds' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Get the payments board that this client payment belongs to.
     */
    public function paymentsBoard()
    {
        return $this->belongsTo(PaymentsBoard::class, 'payments_board_id');
    }

    /**
     * Get the client that this payment belongs to.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Calculate the outstanding amount (total - paid).
     */
    public function getOutstandingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Calculate the payment completion percentage.
     */
    public function getPaymentPercentageAttribute()
    {
        if ($this->total_amount > 0) {
            return round(($this->paid_amount / $this->total_amount) * 100, 2);
        }
        return 0;
    }

    /**
     * Check if payment is fully paid.
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * Check if payment is partially paid.
     */
    public function getIsPartiallyPaidAttribute()
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total_amount;
    }

    /**
     * Get the net amount after TDS deduction.
     */
    public function getNetAmountAttribute()
    {
        return $this->subtotal_amount - $this->tds;
    }

    /**
     * Scope to filter by payments board.
     */
    public function scopeForBoard($query, $boardId)
    {
        return $query->where('payments_board_id', $boardId);
    }

    /**
     * Scope to filter by client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to get fully paid payments.
     */
    public function scopeFullyPaid($query)
    {
        return $query->whereRaw('paid_amount >= total_amount');
    }

    /**
     * Scope to get partially paid payments.
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('paid_amount', '>', 0)
                    ->whereRaw('paid_amount < total_amount');
    }

    /**
     * Scope to get unpaid payments.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid_amount', 0);
    }

    /**
     * Scope to get payments with outstanding amounts.
     */
    public function scopeWithOutstanding($query)
    {
        return $query->whereRaw('paid_amount < total_amount');
    }
}
