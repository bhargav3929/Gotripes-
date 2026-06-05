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
        'country',
        'activityCategory',
        'company_id',
    ];

    /**
     * Distinct countries that have at least one active activity (within the
     * current company scope). Used to decide whether the public Activities page
     * shows a country picker first (more than one country) or goes straight to
     * the emirates (one country, e.g. UAE only).
     */
    public static function countriesWithActivities()
    {
        return self::where('isActive', 1)
            ->get(['country'])
            ->groupBy(fn($a) => $a->country ?: 'United Arab Emirates')
            ->map(fn($group, $country) => ['country' => $country, 'activity_count' => $group->count()])
            ->values();
    }

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
