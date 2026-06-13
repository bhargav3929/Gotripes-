<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FifaSetting extends Model
{
    protected $table = 'fifa_settings';

    protected $fillable = ['markup_percent', 'display_currency'];

    protected $casts = [
        'markup_percent' => 'decimal:2',
    ];

    /**
     * The single global config row (created by migration; self-heals if missing).
     */
    public static function current(): self
    {
        return static::first() ?? static::create([
            'markup_percent'   => 20,
            'display_currency' => 'USD',
        ]);
    }

    public static function markupPercent(): float
    {
        return (float) static::current()->markup_percent;
    }

    public static function currency(): string
    {
        return static::current()->display_currency ?: 'USD';
    }
}
