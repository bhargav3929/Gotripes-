<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FifaTicket extends Model
{
    protected $table = 'fifa_tickets';

    protected $fillable = [
        'match_id', 'quantity', 'category', 'block', 'seat_row',
        'supplier_price', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'supplier_price' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(FifaMatch::class, 'match_id');
    }

    /**
     * Customer-facing price = supplier cost + global markup %.
     */
    public function getCustomerPriceAttribute(): float
    {
        $markup = FifaSetting::markupPercent();
        return round((float) $this->supplier_price * (1 + $markup / 100), 2);
    }

    public function getSeatLabelAttribute(): string
    {
        $parts = array_filter([$this->block ? "Block {$this->block}" : null, $this->seat_row ? "Row {$this->seat_row}" : null]);
        return implode(' · ', $parts);
    }
}
