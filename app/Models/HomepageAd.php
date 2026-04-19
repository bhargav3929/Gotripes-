<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class HomepageAd extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_homepageads';

    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'imgPath',
        'mediaType',
        'slotOrder',
        'displayOrder',
        'duration',
        'title',
        'description',
        'linkUrl',
        'isActive',
        'createdby',
        'createddate',
        'modifiedby',
        'modifieddate'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'slotOrder' => 'integer',
        'displayOrder' => 'integer',
        'duration' => 'integer',
        'createddate' => 'datetime',
        'modifieddate' => 'datetime'
    ];

    public function isVideo(): bool
    {
        return $this->mediaType === 'video';
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    /**
     * Get all active media grouped by slot (TV).
     */
    public static function getGroupedBySlot(int $maxSlots = 5)
    {
        return static::where('isActive', 1)
            ->orderBy('slotOrder', 'asc')
            ->orderBy('displayOrder', 'asc')
            ->get()
            ->groupBy('slotOrder')
            ->take($maxSlots);
    }
}
