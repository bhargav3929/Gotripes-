<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One physical eSIM inside an order.
 *
 * A single-eSIM order has exactly one of these; a tour operator buying 20 has
 * twenty, each separately assigned at the provider with its own ICCID and its
 * own QR. Keeping them as rows rather than a JSON blob means a half-provisioned
 * order can be seen, reported on and retried unit by unit.
 */
class EsimOrderUnit extends Model
{
    protected $table = 'esim_order_units';

    protected $fillable = [
        'esim_order_id',
        'unit_number',
        'monty_order_id',
        'monty_iccid',
        'reservation_status',
        'smdp_address',
        'matching_id',
        'activation_code',
        'monty_response',
    ];

    protected $casts = [
        'monty_response' => 'array',
        'unit_number'    => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EsimOrder::class, 'esim_order_id');
    }

    /**
     * The LPA string an eSIM QR encodes. Prefers the provider's own activation
     * code and falls back to composing it from its parts. Null when the
     * provider gave us neither, in which case there is nothing to draw.
     */
    public function lpaString(): ?string
    {
        if ($this->activation_code) {
            return $this->activation_code;
        }

        if ($this->smdp_address && $this->matching_id) {
            return 'LPA:1$' . $this->smdp_address . '$' . $this->matching_id;
        }

        return null;
    }
}
