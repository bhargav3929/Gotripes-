<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * What a passport holder needs to enter a destination. A rule with
 * nationality '*' covers every nationality that has no rule of its own.
 */
class DestinationVisaRule extends Model
{
    use BelongsToCompany;

    public const REQUIREMENTS = [
        'visa_free'       => 'Visa not required',
        'visa_on_arrival' => 'Visa on arrival',
        'evisa'           => 'e-Visa before travel',
        'visa_required'   => 'Visa must be arranged before travel',
    ];

    public const EVERYONE = '*';

    protected $fillable = [
        'company_id', 'destination', 'nationality', 'requirement', 'max_stay_days',
        'processing_days', 'fee', 'documents', 'notes',
    ];

    protected $casts = ['max_stay_days' => 'integer', 'processing_days' => 'integer', 'fee' => 'float'];

    public static function lookup(string $destination, string $nationality): ?self
    {
        return static::where('destination', $destination)
            ->whereIn('nationality', [$nationality, self::EVERYONE])
            ->orderByRaw("CASE WHEN nationality = ? THEN 0 ELSE 1 END", [$nationality])
            ->first();
    }

    /** green: no paperwork before travel; amber: paperwork before travel. */
    public function tone(): string
    {
        return in_array($this->requirement, ['visa_free', 'visa_on_arrival'], true) ? 'ok' : 'warn';
    }

    public function label(): string
    {
        return self::REQUIREMENTS[$this->requirement] ?? $this->requirement;
    }
}
