<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmrahDeparture extends Model
{
    protected $table = 'tbl_umrah_departures';

    protected $fillable = [
        'umrah_package_id',
        'departure_date',
        'seats_available',
        'seats_booked',
        'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'seats_available' => 'integer',
        'seats_booked' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'umrah_package_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
