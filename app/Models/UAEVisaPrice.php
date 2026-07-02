<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEVisaPrice extends Model
{
    use BelongsToCompany;

    protected $table = 'uae_visa_prices';

    protected $fillable = [
        'visa_package_id',
        'entry_type',
        'duration',
        'traveller_type',
        'price',
        'isActive',
        'company_id',
    ];

    public function package()
    {
        return $this->belongsTo(UAEVisaPackage::class, 'visa_package_id');
    }
}
