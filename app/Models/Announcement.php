<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'tbl_announcements';
    public $timestamps = false;

    protected $fillable = [
        'flagImgPath',
        'description',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'isActive',
        'AnnouncementImportance',
        'tagType'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'AnnouncementImportance' => 'integer',
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime'
    ];

    // CRITICAL: Add accessor to handle camelCase access
    public function getAnnouncementImportanceAttribute($value)
    {
        return $this->attributes['AnnouncementImportance'] ?? 0;
    }

    // CRITICAL: Add mutator to handle camelCase setting
    public function setAnnouncementImportanceAttribute($value)
    {
        $this->attributes['AnnouncementImportance'] = (int) $value;
    }

    public function getTagLabelAttribute(): string
    {
        return match ($this->tagType) {
            'breaking' => 'BREAKING',
            'trending' => 'TRENDING',
            'exclusive' => 'EXCLUSIVE',
            'alert' => 'NEW',
            'hot' => 'HOT',
            default => '',
        };
    }

    public function getTagCssClassAttribute(): string
    {
        return match ($this->tagType) {
            'breaking' => 'badge-new',
            'trending' => 'tag-gold',
            'exclusive' => 'tag-green',
            'alert' => 'tag-blue',
            'hot' => 'tag-yellow',
            default => '',
        };
    }
}
