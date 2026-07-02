<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEVisaPackage extends Model
{
    use BelongsToCompany;

    protected $table = 'uae_visa_packages';

    protected $fillable = [
        'emirates_id',
        'name',
        'description',
        'isActive',
        'company_id',
    ];

    public function emirate()
    {
        return $this->belongsTo(Emirates::class, 'emirates_id', 'emiratesID');
    }

    public function prices()
    {
        return $this->hasMany(UAEVisaPrice::class, 'visa_package_id');
    }
}
