<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentsBoard extends Model
{
    use HasFactory;

    protected $table = 'payments_board';

    protected $fillable = [
        'board_month_year',
        'start_date',
        'end_date',
        'total_days',
        'clients_count',
        'total_pre_gst_amount',
        'total_gst_amount',
        'total_cash_sales',
        'total_tds',
        'total_previous_balance',
        'total_amount',
        'total_net_amount',
        'total_paid_amount',
        'total_unpaid_amount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'clients_count' => 'integer',
        'total_pre_gst_amount' => 'decimal:2',
        'total_gst_amount' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_tds' => 'decimal:2',
        'total_previous_balance' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
        'total_paid_amount' => 'decimal:2',
        'total_unpaid_amount' => 'decimal:2',
    ];

    /**
     * Get the month and year from board_month_year
     */
    public function getMonthAttribute()
    {
        return Carbon::createFromFormat('m-Y', $this->board_month_year)->format('F');
    }

    /**
     * Get the year from board_month_year
     */
    public function getYearAttribute()
    {
        return Carbon::createFromFormat('m-Y', $this->board_month_year)->year;
    }

    /**
     * Get formatted month year for display
     */
    public function getFormattedMonthYearAttribute()
    {
        return Carbon::createFromFormat('m-Y', $this->board_month_year)->format('F Y');
    }

    /**
     * Get total amount collected (percentage)
     */
    public function getCollectionPercentageAttribute()
    {
        if ($this->total_amount > 0) {
            return round(($this->total_paid_amount / $this->total_amount) * 100, 2);
        }
        return 0;
    }

    /**
     * Get outstanding amount
     */
    public function getOutstandingAmountAttribute()
    {
        return $this->total_amount - $this->total_paid_amount;
    }

    /**
     * Scope to filter by month and year
     */
    public function scopeByMonthYear($query, $monthYear)
    {
        return $query->where('board_month_year', $monthYear);
    }

    /**
     * Scope to filter by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('board_month_year', 'LIKE', '%-' . $year);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    /**
     * Get the latest payments board
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('start_date', 'desc');
    }
}
