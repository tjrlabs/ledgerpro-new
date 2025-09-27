<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_profile';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone',
        'company_website',
        'logo',
        'billing_address',
        'shipping_address',
        'is_default',
    ];

    /**
     * Get the user that owns the company profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media that owns the logo.
     */
    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo');
    }

    /**
     * Get the billing address.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Addressbook::class, 'billing_address');
    }

    /**
     * Get the shipping address.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Addressbook::class, 'shipping_address');
    }
}
