<?php

namespace App\Models\Reports;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PaymentsBoard extends Model
{
    use HasFactory;

    protected $table = 'payments_board';

    protected $fillable = [
        'company_profile_id',
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
        'finalized_at',
    ];

    protected $casts = [
        'company_profile_id' => 'integer',
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
        'finalized_at' => 'datetime',
    ];

    /**
     * Get the company profile that owns this payments board.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

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

    public function getIsFinalizedAttribute(): bool
    {
        return $this->finalized_at !== null;
    }

    /**
     * Scope to filter by month and year
     */
    public function scopeByMonthYear($query, $monthYear)
    {
        return $query->where('board_month_year', $monthYear);
    }

    /**
     * Scope to filter by company.
     */
    public function scopeForCompany($query, int $companyProfileId)
    {
        return $query->where('company_profile_id', $companyProfileId);
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
