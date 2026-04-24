<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    use HasFactory;

    protected $table = 'referral_settings';

    protected $fillable = [
        'commission_type',
        'commission_value',
        'auto_approve',
        'min_withdrawal_amount',
        'signup_enabled',
    ];

    protected $casts = [
        'commission_value'      => 'decimal:2',
        'min_withdrawal_amount' => 'decimal:2',
        'auto_approve'          => 'boolean',
        'signup_enabled'        => 'boolean',
    ];

    /**
     * Get the singleton settings row, creating it with defaults if it doesn't exist.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate([], [
            'commission_type'       => 'percentage',
            'commission_value'      => 10.00,
            'auto_approve'          => false,
            'min_withdrawal_amount' => 100.00,
            'signup_enabled'        => true,
        ]);
    }
}
