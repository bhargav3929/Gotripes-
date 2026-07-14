<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SaudiVisaType extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_saudi_visa_types';

    protected $fillable = [
        'company_id',
        'name',
        'price',
        'isActive',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'isActive' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }
}
