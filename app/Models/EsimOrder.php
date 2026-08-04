<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * A customer's eSIM purchase.
 *
 * `$fillable` previously also listed user_id, total_amount, status and
 * referral_agent_id, with `user()` and `referralAgent()` relations to match —
 * none of which exist as columns on `esim_orders`. Assigning any of them threw,
 * and the relations could never resolve, so they are gone. Money lives on
 * selling_price / monty_cost_price; lifecycle lives on payment_status and
 * reservation_status.
 */
class EsimOrder extends Model
{
    use BelongsToCompany;

    protected $table = 'esim_orders';

    protected $fillable = [
        'company_id',
        'order_reference',
        'customer_name',
        'customer_email',
        'customer_phone',
        'country_code',
        'country_name',
        'bundle_code',
        'bundle_name',
        'quantity',           // how many eSIMs this one order covers
        'data_amount',
        'validity_days',
        'monty_cost_price',   // what the bundle costs us, in AED (per unit)
        'selling_price',      // order total the customer pays, in AED
        'unit_selling_price', // price of a single eSIM, in AED
        'currency',
        'monty_order_id',     // provider order id, set once assigned
        'monty_iccid',
        'reservation_status', // pending | completed | assign_failed | error
        'payment_status',     // unpaid | paid | failed
        'monty_response',
        'qr_sent_at',         // when WE emailed the customer their QR
        'isActive',
    ];

    protected $casts = [
        'monty_response'     => 'array',
        'isActive'           => 'boolean',
        'monty_cost_price'   => 'decimal:2',
        'selling_price'      => 'decimal:2',
        'unit_selling_price' => 'decimal:2',
        'quantity'           => 'integer',
        'qr_sent_at'         => 'datetime',
    ];

    /** The individual eSIMs this order covers, in purchase order. */
    public function units()
    {
        return $this->hasMany(EsimOrderUnit::class, 'esim_order_id')->orderBy('unit_number');
    }

    /** How many eSIMs this order covers. Older rows predate the column. */
    public function unitCount(): int
    {
        return max(1, (int) ($this->quantity ?: 1));
    }
}
