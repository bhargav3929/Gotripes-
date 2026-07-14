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
        'category',
        'image',
        'price',
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
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'isFeatured' => 'boolean',
        'price' => 'decimal:2',
        'features' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'itinerary' => 'array',
        'gallery_images' => 'array',
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    public function departures()
    {
        return $this->hasMany(UmrahDeparture::class, 'umrah_package_id');
    }
}
