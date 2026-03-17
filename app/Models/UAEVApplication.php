<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UAEVApplication extends Model
{
   protected $table = 'uaev_application';

protected $fillable = [
    'UAEV_nationality',
    'UAEV_residence',
    'UAEV_first_name',
    'UAEV_last_name',
    'UAEV_passport_valid',
    'UAEV_not_stay_long',
    'UAEV_gender',
    'UAEV_dob',
    'UAEV_arrival_date',
    'UAEV_departure_date',
    'UAEV_phone',
    'UAEV_email',
    'UAEV_profession',
    'UAEV_marital_status',
    'UAEV_passport_copy',
    'UAEV_passport_photo',
    'UAEV_visaDuration',
    'UAEV_price',
    'UAEV_Created_by',
    'UAEV_created_date',
    'UAEV_isActive',
    'UAEV_status',
];
public $timestamps = false; // Because you're using custom timestamp col
public function uaevStatus()
    {
        return $this->belongsTo(UAEVStatus::class, 'UAEV_status');
    }
}
