<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageAd extends Model
{
    protected $table = 'tbl_homepageads';
    
    // Disable Laravel's default timestamps
    public $timestamps = false;

    protected $fillable = [
        'imgPath',
        'mediaType',
        'slotOrder',
        'displayOrder',
        'duration',
        'title',
        'description',
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
