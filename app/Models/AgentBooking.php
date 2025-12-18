<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentBooking extends Model
{
    use HasFactory;

    protected $table = 'agent_bookings';

    protected $fillable = [
        'agent_name',
        'client_name',
        'client_email',
        'client_phone',
        'service_type',
        'amount',
        'currency',
        'payment_status',
        'order_id',
        'details',
    ];
}
