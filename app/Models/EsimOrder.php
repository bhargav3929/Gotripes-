<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class EsimOrder extends Model
{
    use BelongsToCompany;

    protected $table = 'esim_orders';

    protected $fillable = [
        'order_reference',
        'customer_name',
        'customer_email',
        'customer_phone',
        'country_code',
        'country_name',
        'bundle_code',
        'bundle_name',
        'data_amount',
        'validity_days',
        'monty_cost_price',
        'selling_price',
        'currency',
        'monty_order_id',
        'monty_iccid',
        'reservation_status',
        'payment_status',
        'monty_response',
        'isActive',
        'company_id',
        'user_id',
        'total_amount',
        'status',
        'referral_agent_id',
    ];

    protected $casts = [
        'monty_response' => 'array',
        'isActive' => 'boolean',
        'monty_cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referralAgent()
    {
        return $this->belongsTo(ReferralAgent::class);
    }
}
