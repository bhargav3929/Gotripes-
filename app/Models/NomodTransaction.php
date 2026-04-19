<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class NomodTransaction extends Model
{
    use BelongsToCompany;

    protected $table = 'nomod_transactions';

    protected $fillable = [
        'company_id',
        'checkout_id',
        'order_id',
        'status',
        'amount',
        'discount',
        'currency',
        'booking_type',
        'checkout_url',
        'items',
        'customer',
        'charges',
        'metadata',
        'response_data',
    ];

    protected $casts = [
        'items' => 'array',
        'customer' => 'array',
        'charges' => 'array',
        'metadata' => 'array',
        'response_data' => 'array',
    ];
}
