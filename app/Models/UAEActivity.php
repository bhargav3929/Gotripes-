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
        'agent_id',
    ];

    /**
     * Public-facing scope: includes the current tenant's activities AND
     * platform/shared activities (company_id = NULL, pre-multi-tenancy rows).
     * Use this for all visitor-facing pages instead of the default global scope
     * which strictly filters to company_id = X and hides shared activities.
     */
    public function scopePublicVisible($query)
    {
        $companyId = current_company_id();
        return $query->withoutGlobalScope(\App\Scopes\CompanyScope::class)
                     ->where(function ($q) use ($companyId) {
                         $q->where('company_id', $companyId)
                           ->orWhereNull('company_id');
                     });
    }

    /**
     * Distinct countries that have at least one active activity visible to
     * the current tenant (own + platform/shared). Used to decide whether the
     * public Activities page shows a country picker or goes straight to the
     * UAE emirates grid.
     */
    public static function countriesWithActivities()
    {
        return self::publicVisible()
            ->where('isActive', 1)
            ->get(['country'])
            ->groupBy(fn($a) => ($a->country && trim($a->country) !== '') ? trim($a->country) : 'United Arab Emirates')
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
