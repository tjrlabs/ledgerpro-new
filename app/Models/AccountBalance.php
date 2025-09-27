<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountBalance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts_balance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'client_id',
        'month',
        'year',
        'opening_balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opening_balance' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Get the company profile that owns the account balance.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get the client that owns the account balance.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the month name.
     *
     * @return string
     */
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return $months[$this->month] ?? 'Unknown';
    }

    /**
     * Get the formatted opening balance.
     *
     * @return string
     */
    public function getFormattedOpeningBalanceAttribute(): string
    {
        return '₹' . number_format($this->opening_balance, 2);
    }

    /**
     * Scope a query to only include balances for a specific month and year.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPeriod($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope a query to only include balances for a specific company.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $companyProfileId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, int $companyProfileId)
    {
        return $query->where('company_profile_id', $companyProfileId);
    }

    /**
     * Scope a query to only include balances for a specific client.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $clientId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Get or create an account balance record.
     *
     * @param int $companyProfileId
     * @param int $clientId
     * @param int $month
     * @param int $year
     * @param float $openingBalance
     * @return AccountBalance
     */
    public static function createOrUpdate(int $companyProfileId, int $clientId, int $month, int $year, float $openingBalance = 0.00): AccountBalance
    {
        return self::updateOrCreate(
            [
                'company_profile_id' => $companyProfileId,
                'client_id' => $clientId,
                'month' => $month,
                'year' => $year,
            ],
            [
                'opening_balance' => $openingBalance,
            ]
        );
    }
}
