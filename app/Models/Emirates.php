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

    // Get active emirates
    public static function getActiveEmirates()
    {
        return self::where('isActive', 1)->orderBy('emiratesName')->get();
    }

    // Get emirates with activity count, optionally limited to one country.
    public static function getEmiratesWithActivityCount($country = null)
    {
        return self::where('isActive', 1)
            ->when($country, fn($q) => $q->where('country', $country))
            ->withCount(['activities' => function($query) {
                $query->where('isActive', 1);
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
        return self::where('isActive', 1)
            ->withCount(['activities' => fn($q) => $q->where('isActive', 1)])
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
