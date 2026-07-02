<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emirates extends Model
{
    protected $table = 'tbl_emirates';
    protected $primaryKey = 'emiratesID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'emiratesName',
        'country',
        'emiratesDescription',
        'emiratesImage',
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
    ];

    protected $dates = [
        'createdDate',
        'modifiedDate',
    ];

    // Relationship with UAE Activities
    public function activities()
    {
        return $this->hasMany(UAEActivity::class, 'emiratesID', 'emiratesID');
    }

    // Relationship with UAE Visa Packages
    public function packages()
    {
        return $this->hasMany(UAEVisaPackage::class, 'emirates_id', 'emiratesID');
    }

    // Get active emirates
    public static function getActiveEmirates()
    {
        return self::where('isActive', 1)->orderBy('emiratesName')->get();
    }

    // Get emirates with activity count, optionally limited to one country.
    public static function getEmiratesWithActivityCount($country = null)
    {
        $companyId = current_company_id();
        return self::where('isActive', 1)
            ->when($country, fn($q) => $q->where('country', $country))
            ->withCount(['activities' => function ($query) use ($companyId) {
                // Bypass tenant scope so platform/shared activities (company_id = NULL)
                // are counted alongside the tenant's own activities.
                $query->withoutGlobalScope(\App\Scopes\CompanyScope::class)
                      ->where('isActive', 1)
                      ->where(function ($q) use ($companyId) {
                          $q->where('company_id', $companyId)
                            ->orWhereNull('company_id');
                      });
            }])
            ->orderBy('emiratesName')
            ->get();
    }

    /**
     * Distinct countries that actually have at least one active activity.
     * Used to decide whether the public Activities page should show a country
     * picker (more than one country) or go straight to the emirates (one country).
     */
    public static function getCountriesWithActivities()
    {
        $companyId = current_company_id();
        return self::where('isActive', 1)
            ->withCount(['activities' => function ($q) use ($companyId) {
                $q->withoutGlobalScope(\App\Scopes\CompanyScope::class)
                  ->where('isActive', 1)
                  ->where(function ($q2) use ($companyId) {
                      $q2->where('company_id', $companyId)->orWhereNull('company_id');
                  });
            }])
            ->get()
            ->filter(fn($e) => $e->activities_count > 0)
            ->groupBy('country')
            ->map(fn($group, $country) => [
                'country'        => $country ?: 'United Arab Emirates',
                'activity_count' => $group->sum('activities_count'),
            ])
            ->values();
    }
}
