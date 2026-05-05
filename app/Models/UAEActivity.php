<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEActivity extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_UAEActivities';
    protected $primaryKey = 'activityID';
    public $incrementing = true;
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'activityID';
    }

    protected $fillable = [
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'activityName',
        'activityLocation',
        'activityImage',
        'activityCurrency',
        'activityChildPrice',
        'activityTransactionCharges',
        'activityPrice',
        'dubaiPrice',
        'abuDhabiPrice',
        'fromAbuDhabiToDubai',
        'emirates',
        'supplierName',
        'supplierEmail',
        'activityRoute',
        'emiratesID',
        'activityCategory',
        'company_id',
    ];

    // Relationship with Emirates
    public function emirate()
    {
        return $this->belongsTo(Emirates::class, 'emiratesID', 'emiratesID');
    }

    public function details()
    {
        return $this->hasOne(UAEActivityDetail::class, 'activityID', 'activityID');
    }

    // Scope for active activities
    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    // Scope for activities by emirate
    public function scopeByEmirate($query, $emiratesID)
    {
        return $query->where('emiratesID', $emiratesID);
    }
}
