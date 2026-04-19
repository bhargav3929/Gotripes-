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
        'image',
        'price',
        'currency',
        'description',
        'duration',
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
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }
}
