<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Global config for the Fluxir e-Visa storefront. Mirrors {@see FifaSetting}:
 * a single self-healing row holding the markup percentage applied to Fluxir's
 * net fee to produce the customer-facing price.
 */
class EvisaSetting extends Model
{
    protected $table = 'evisa_settings';

    protected $fillable = ['markup_percent'];

    protected $casts = [
        'markup_percent' => 'decimal:2',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create(['markup_percent' => 15]);
    }

    public static function markupPercent(): float
    {
        return (float) static::current()->markup_percent;
    }

    /**
     * Customer-facing price for a given Fluxir net fee, after markup.
     * Rounded up to the nearest whole unit so we never under-charge.
     */
    public static function customerPrice(float $netFee): int
    {
        return (int) ceil($netFee * (1 + static::markupPercent() / 100));
    }
}
