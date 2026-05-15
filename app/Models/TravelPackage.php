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
        'title',
        'country',
        'image',
        'price',
        'description',
        'duration',
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'price' => 'decimal:2',
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }
}
