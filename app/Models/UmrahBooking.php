<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UmrahBooking extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_umrah_bookings';

    protected $fillable = [
        'company_id',
        'umrah_package_id',
        'departure_date',
        'adults',
        'children',
        'infants',
        'payment_gateway',
        'installment_months',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'passenger_details',
        'payment_status',
        'order_id',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'installment_months' => 'integer',
        'total_price' => 'decimal:2',
        'passenger_details' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'umrah_package_id');
    }
}
