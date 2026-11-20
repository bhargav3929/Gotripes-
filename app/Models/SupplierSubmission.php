<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * One filled-in supplier rate form. Prices are the supplier's NET prices in
 * the currency they chose; they reach a package only when staff apply them.
 */
class SupplierSubmission extends Model
{
    use BelongsToCompany;

    public const CURRENCIES = ['AED', 'USD', 'EUR', 'GBP', 'SAR'];

    /** AED for 1 unit, a starting point staff can change when applying. */
    public const AED_RATES = ['AED' => 1, 'USD' => 3.6725, 'EUR' => 4.0, 'GBP' => 4.7, 'SAR' => 0.98];

    /** Extras a supplier can offer; insurance stays ours. */
    public const EXTRA_CATEGORIES = [
        'guide_language'   => 'Guide (other language)',
        'transport'        => 'Transport / vehicle',
        'airport_transfer' => 'Airport transfer',
        'meal_plan'        => 'Meals',
    ];

    protected $fillable = ['company_id', 'supplier_invite_id', 'status', 'data', 'photos', 'applied_package_id', 'applied_at', 'applied_by'];

    protected $casts = ['data' => 'array', 'photos' => 'array', 'applied_at' => 'datetime'];

    public function invite()
    {
        return $this->belongsTo(SupplierInvite::class, 'supplier_invite_id');
    }

    public function appliedPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'applied_package_id');
    }

    public function get(string $key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }

    /** Hotels keyed by star level, only the ones the supplier named. */
    public function hotels(): array
    {
        return array_filter((array) $this->get('hotels', []), fn ($h) => filled($h['name'] ?? null));
    }
}
