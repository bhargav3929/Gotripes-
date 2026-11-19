<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_travel_packages';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'agent_id',
        'title',
        'country',
        'package_type',
        'partner_email',
        'partner_whatsapp',
        'notification_emails',
        'image',
        'price',
        'price_adult',
        'price_child',
        'price_infant',
        'description',
        'duration',
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'margin_percent',
        'insurance_required',
        'child_max_age',
        'infant_max_age',
        'child_land_percent',
        'child_sharing_max_age',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'price' => 'decimal:2',
        'price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'price_infant' => 'decimal:2',
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime',
        'margin_percent' => 'float',
        'insurance_required' => 'boolean',
        'child_max_age' => 'integer',
        'infant_max_age' => 'integer',
        'child_land_percent' => 'float',
        'child_sharing_max_age' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    /**
     * Parsed recipients to notify when a customer enquires about this package.
     *
     * @return array<int, string>
     */
    public function getNotificationEmailListAttribute(): array
    {
        return parse_emails($this->notification_emails);
    }

    public function images()
    {
        return $this->hasMany(TravelPackageImage::class, 'package_id')->orderBy('sort_order');
    }

    /**
     * All gallery image paths, cover image first. Falls back to the single
     * `image` column when no gallery images exist (backward compatible).
     */
    public function galleryImages(): array
    {
        $paths = $this->images->pluck('image_path')->all();
        if ($this->image && !in_array($this->image, $paths, true)) {
            array_unshift($paths, $this->image);
        }
        return $paths;
    }

    /**
     * True when this is a ready-made package the customer can pay for directly.
     */
    public function isPurchasable(): bool
    {
        return $this->package_type === 'purchase';
    }

    /** Booked online through the step-by-step wizard, priced from net rates + margin. */
    public function isWizard(): bool
    {
        return $this->package_type === 'wizard';
    }

    /**
     * Priced per room and per group size: at least one hotel level with room
     * prices and at least one land price. Packages set up before that still
     * have per-person rates (rates()) and are priced from those.
     */
    public function usesRoomPricing(): bool
    {
        $this->loadMissing(['hotels', 'landRates']);
        return $this->landRates->isNotEmpty() && $this->hotels->contains(fn ($h) => $h->isPriced());
    }

    /** Priced either way, so the wizard can quote it. */
    public function hasPricing(): bool
    {
        if ($this->usesRoomPricing()) {
            return true;
        }
        return $this->relationLoaded('rates') ? $this->rates->isNotEmpty() : $this->rates()->exists();
    }

    /** A wizard package with prices: the customer can book and pay online. */
    public function isBookableOnline(): bool
    {
        return $this->isWizard() && $this->hasPricing();
    }

    /** Where a package card should go: straight into the booking wizard when there is one. */
    public function bookingUrl(): string
    {
        return $this->isBookableOnline()
            ? route('tour-packages.book', $this->id)
            : route('tour-packages.show', $this->id);
    }

    /** "From" price for a card: the wizard's lowest rate, otherwise the package price. */
    public function cardFromPrice(): ?float
    {
        $p = $this->isBookableOnline() ? $this->wizardFromPrice() : (float) $this->price;
        return $p > 0 ? $p : null;
    }

    public function landRates()
    {
        return $this->hasMany(TourPackageLandRate::class, 'package_id')->orderBy('days')->orderBy('min_group');
    }

    public function rates()
    {
        return $this->hasMany(TourPackageRate::class, 'package_id')->orderBy('days')->orderBy('hotel_stars');
    }

    public function hotels()
    {
        return $this->hasMany(TourPackageHotel::class, 'package_id')->orderBy('hotel_stars');
    }

    public function addons()
    {
        return $this->hasMany(TourPackageAddon::class, 'package_id')->orderBy('category')->orderBy('sort_order')->orderBy('id');
    }

    public function itineraryDays()
    {
        return $this->hasMany(TourPackageDay::class, 'package_id')->orderBy('day_number');
    }

    public function seasons()
    {
        return $this->hasMany(TourPackageSeason::class, 'package_id')->orderBy('starts_on');
    }

    public function wizardBookings()
    {
        return $this->hasMany(TourPackageBooking::class, 'package_id');
    }

    /** Lowest selling price per adult, two sharing a double room (for "From AED x"). */
    public function wizardFromPrice(): ?float
    {
        if ($this->usesRoomPricing()) {
            $prices = $this->hotels->filter(fn ($h) => $h->isPriced())
                ->map(fn ($h) => \App\Services\TourPackages\TourPackageQuote::fromPerAdult($this, $h->hotel_stars))
                ->filter();
            return $prices->isEmpty() ? null : $prices->min();
        }
        $this->loadMissing('rates');
        $min = $this->rates->min('adult_net');
        return $min === null ? null : round($min * (1 + (float) $this->margin_percent / 100), 2);
    }
}
