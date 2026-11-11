<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UmrahPackage extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_umrah_packages';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'title',
        'category',       // bus | air
        'sub_category',   // economy | standard | premium | vip
        'image',
        'price',
        'discount_price',
        'adult_price',
        'child_price',
        'infant_price',
        'currency',
        'description',
        'duration',
        'transport',
        'hotels',
        'inclusions',
        'exclusions',
        'itinerary',
        'gallery_images',
        'tag',
        'features',
        'isFeatured',
        'sortOrder',
        'isActive',
        'status',         // active | disabled | archived
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'category_id',
        'airline',
        'flight_number',
        'departure_airport',
        'arrival_airport',
        'cabin_class',
        'baggage',
        'transit_details',
    ];

    protected $casts = [
        'isActive'       => 'boolean',
        'isFeatured'     => 'boolean',
        'price'          => 'decimal:2',
        'discount_price' => 'decimal:2',
        'adult_price'    => 'decimal:2',
        'child_price'    => 'decimal:2',
        'infant_price'   => 'decimal:2',
        'features'       => 'array',
        'inclusions'     => 'array',
        'exclusions'     => 'array',
        'itinerary'      => 'array',
        'gallery_images' => 'array',
        'createdDate'    => 'datetime',
        'modifiedDate'   => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    public function departures()
    {
        return $this->hasMany(UmrahDeparture::class, 'umrah_package_id');
    }

    /**
     * Keep a rolling run of weekly departures open so the booking calendar is
     * never empty. Departures used to be typed in by hand; once the last one
     * passed, every package showed a calendar with nothing to pick.
     *
     * Only dates with no row at all are created, so a date staff closed or
     * edited in the manager panel is left as they set it. New dates fall on the
     * package's usual weekday (Wednesday for bus) with the seat count of its
     * most recent departure.
     */
    public function ensureUpcomingDepartures(int $weeks = 12): void
    {
        $latest = $this->departures()->orderByDesc('departure_date')->first();

        $weekday = $this->category === 'bus' || !$latest
            ? 3
            : (int) $latest->departure_date->format('w');
        $seats = (int) ($latest?->seats_total ?: $latest?->seats_available ?: 45);

        $date = now()->addDays(5)->startOfDay();
        while ((int) $date->format('w') !== $weekday) {
            $date->addDay();
        }
        $end = now()->addWeeks($weeks);

        $existing = $this->departures()
            ->where('departure_date', '>=', $date->toDateString())
            ->pluck('departure_date')
            ->map(fn ($d) => $d->toDateString())
            ->all();

        for (; $date->lte($end); $date->addWeek()) {
            if (in_array($date->toDateString(), $existing, true)) {
                continue;
            }
            $this->departures()->create([
                'departure_date'  => $date->toDateString(),
                'seats_total'     => $seats,
                'seats_available' => $seats,
                'seats_booked'    => 0,
                'status'          => 'available',
            ]);
        }
    }

    public function bookings()
    {
        return $this->hasMany(UmrahBooking::class, 'umrah_package_id');
    }

    public function umrahCategory()
    {
        return $this->belongsTo(UmrahCategory::class, 'category_id');
    }

    public function mapped_hotels()
    {
        return $this->belongsToMany(
            UmrahHotel::class,
            'tbl_umrah_package_hotels',
            'umrah_package_id',
            'umrah_hotel_id'
        );
    }

    /** Price for a given passenger type, falling back to base price for adults */
    public function priceFor(string $type): float
    {
        $base = $this->effectivePrice();
        return match ($type) {
            'child'  => (float) ($this->child_price  ?? $base * 0.5),
            'infant' => (float) ($this->infant_price ?? 0),
            default  => (float) ($this->adult_price  ?? $base),
        };
    }

    /** Effective display price (discounted if set) */
    public function effectivePrice(): float
    {
        return (float) ($this->discount_price && $this->discount_price < $this->price
            ? $this->discount_price
            : $this->price);
    }

    public function getStartingPriceAttribute(): float
    {
        $base = $this->price > 0 ? $this->price : ($this->adult_price ?? 0);
        return (float) ($this->discount_price && $this->discount_price < $base
            ? $this->discount_price
            : $base);
    }
    
    public function getOriginalPriceAttribute(): float
    {
        return (float) ($this->price > 0 ? $this->price : ($this->adult_price ?? 0));
    }
}
