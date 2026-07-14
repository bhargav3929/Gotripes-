<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SaudiVisaApplication extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_saudi_visa_applications';

    protected $fillable = [
        'company_id',
        'saudi_visa_type_id',
        'full_name',
        'email',
        'phone',
        'nationality',
        'passport_path',
        'additional_doc_path',
        'price',
        'payment_status',
        'order_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function visaType()
    {
        return $this->belongsTo(SaudiVisaType::class, 'saudi_visa_type_id');
    }
}
