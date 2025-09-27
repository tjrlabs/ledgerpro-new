<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_profile_id',
        'client_name',
        'display_name',
        'client_email',
        'client_phone',
        'client_type',
        'client_tax_number',
        'billing_address',
        'shipping_address',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the company profile that owns the client.
     */
    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }
}
