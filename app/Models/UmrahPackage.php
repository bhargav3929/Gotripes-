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

    public function bookings()
    {
        return $this->hasMany(UmrahBooking::class, 'umrah_package_id');
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
}
