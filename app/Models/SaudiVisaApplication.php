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
        'first_name',
        'last_name',
        'email',
        'phone',
        'nationality',
        'passport_number',
        'passport_expiry',
        'dob',
        'gender',
        'passport_path',
        'photo_path',
        'additional_doc_path',
        'price',
        'payment_status',
        'status',
        'internal_notes',
        'order_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'dob' => 'date',
        'passport_expiry' => 'date',
    ];

    public function visaType()
    {
        return $this->belongsTo(SaudiVisaType::class, 'saudi_visa_type_id');
    }
}
