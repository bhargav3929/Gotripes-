<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentResponse extends Model
{
    protected $fillable = [
        'booking_id',
        'order_id',
        'order_status',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(ActivityBooking::class, 'booking_id');
    }
}

