<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $table = 'items'; // Specify the table name if different from the default
    protected $fillable = [
        'company_profile_id',
        'client_id',
        'item_type',
        'item_name',
        'item_description',
        'item_sku',
        'item_price',
        'item_unit',
        'item_hsn_code',
    ];
}
