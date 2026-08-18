<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEVisaPackageDeposit extends Model
{
    use BelongsToCompany;

    protected $table = 'uae_visa_package_deposits';

    protected $fillable = [
        'visa_package_id',
        'nationality',
        'security_deposit',
        'deposit_admin_fee',
        'isActive',
        'company_id',
    ];

    protected $casts = [
        'security_deposit'  => 'float',
        'deposit_admin_fee' => 'float',
    ];

    public function package()
    {
        return $this->belongsTo(UAEVisaPackage::class, 'visa_package_id');
    }
}
