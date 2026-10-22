<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * A customer's booking made through the package wizard. price_breakdown is
 * the quote exactly as shown at checkout; the charge always equals `total`.
 */
class TourPackageBooking extends Model
{
    use BelongsToCompany;

    public const ORDER_PREFIX = 'ORDTPK';

    protected $fillable = [
        'company_id', 'package_id', 'order_id', 'customer_name', 'customer_email', 'customer_phone',
        'nationality', 'start_date', 'end_date', 'days', 'hotel_stars', 'traveler_type',
        'adults', 'children', 'infants', 'child_ages', 'selections', 'price_breakdown',
        'net_total', 'total', 'currency', 'visa_requirement', 'payment_status', 'status', 'special_requests',
    ];

    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date', 'child_ages' => 'array', 'selections' => 'array',
        'price_breakdown' => 'array', 'net_total' => 'float', 'total' => 'float',
        'days' => 'integer', 'hotel_stars' => 'integer', 'adults' => 'integer', 'children' => 'integer', 'infants' => 'integer',
    ];

    /** Unscoped: payment callbacks and emails run outside the tenant's host. */
    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id')->withoutGlobalScope(\App\Scopes\CompanyScope::class);
    }

    public static function idFromOrder(string $orderId): ?int
    {
        return preg_match('/^' . self::ORDER_PREFIX . '(\d+)$/', $orderId, $m) ? (int) $m[1] : null;
    }

    public function travellerSummary(): string
    {
        $parts = [$this->adults . ' adult' . ($this->adults === 1 ? '' : 's')];
        if ($this->children) $parts[] = $this->children . ' child' . ($this->children === 1 ? '' : 'ren');
        if ($this->infants) $parts[] = $this->infants . ' infant' . ($this->infants === 1 ? '' : 's');
        return implode(', ', $parts);
    }
}
