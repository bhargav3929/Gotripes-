<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class UmrahHotel extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_umrah_hotels';

    protected $fillable = [
        'company_id',
        'name',
        'city',
        'star_rating',
        'address',
        'images',
        'amenities',
        'description',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'images' => 'array',
        'amenities' => 'array',
        'star_rating' => 'integer',
    ];

    public function packages()
    {
        return $this->belongsToMany(
            UmrahPackage::class,
            'tbl_umrah_package_hotels',
            'umrah_hotel_id',
            'umrah_package_id'
        );
    }
}
