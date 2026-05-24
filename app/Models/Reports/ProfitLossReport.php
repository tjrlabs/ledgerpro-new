<?php

namespace App\Models\Reports;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProfitLossReport extends Model
{
    use HasFactory;

    protected $table = 'profit_loss_reports';

    protected $fillable = [
        'company_profile_id',
        'payments_board_id',
        'board_month_year',
        'start_date',
        'end_date',
        'total_cash_sales',
        'total_pre_gst_sales',
        'total_gst',
        'total_income',
        'total_expenses',
        'profit_loss',
    ];

    protected $casts = [
        'company_profile_id' => 'integer',
        'payments_board_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_cash_sales' => 'decimal:2',
        'total_pre_gst_sales' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'profit_loss' => 'decimal:2',
    ];

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function paymentsBoard(): BelongsTo
    {
        return $this->belongsTo(PaymentsBoard::class, 'payments_board_id');
    }

    public function getFormattedMonthYearAttribute(): string
    {
        return Carbon::createFromFormat('m-Y', $this->board_month_year)->format('F Y');
    }

    public function getIsProfitAttribute(): bool
    {
        return (float) $this->profit_loss >= 0;
    }

    public function scopeForCompany($query, int $companyProfileId)
    {
        return $query->where('company_profile_id', $companyProfileId);
    }

    public function scopeByMonthYear($query, string $monthYear)
    {
        return $query->where('board_month_year', $monthYear);
    }
}
