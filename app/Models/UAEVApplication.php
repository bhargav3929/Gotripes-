<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UAEVApplication extends Model
{
    use BelongsToCompany;

    // The actual table on disk is named with mixed case (`UAEV_application`).
    // SQLite is case-insensitive on table names so the lowercase form worked
    // locally, but Linux MySQL is case-sensitive and threw "Table doesn't exist".
    protected $table = 'UAEV_application';

    protected $fillable = [
        'company_id',
        'UAEV_nationality',
        'UAEV_residence',
        'UAEV_emirate',
        'UAEV_package_name',
        'UAEV_visa_type',
        'UAEV_traveller_type',
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
        'UAEV_addons',
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
