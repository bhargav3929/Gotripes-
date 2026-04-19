<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ActivityBooking extends Model
{
    use BelongsToCompany;

    protected $table = "activitybookings";

    protected $fillable = [
        'company_id',
        'isActive',
        'createdBy',
        'createDate',
        'modifiedBy',
        'modifiedDate',
        'activityId',
        'name',
        'date',
        'email',
        'phone',
        'address',
        'nationality',
        'adults',
        'childrens',
        'remarks',
        'amount',
        'currency',
        'paymentOption',
        'transfer',
        'transportCharges', // Added after migration
        'actionType',       // Added after migration
        'status'           // Added after migration
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'createDate' => 'datetime',
        'modifiedDate' => 'datetime',
        'date' => 'date',
        'transportCharges' => 'float',
        'amount' => 'float'
    ];

    // Disable Laravel's default timestamps since you use createDate/modifiedDate
    public $timestamps = false;
}
