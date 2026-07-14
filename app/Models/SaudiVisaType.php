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
        'description',
        'required_documents',
        'processing_days',
        'price',
        'isActive',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'isActive'            => 'boolean',
        'processing_days'     => 'integer',
        'required_documents'  => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }
}
