<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelPackageImage extends Model
{
    protected $table = 'tbl_travel_package_images';

    protected $fillable = [
        'package_id',
        'image_path',
        'sort_order',
    ];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
