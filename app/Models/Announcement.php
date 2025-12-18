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
        'AnnouncementImportance'  // Keep as-is to match database column
    ];

    // CRITICAL: Add attribute casting for proper type handling
    protected $casts = [
        'isActive' => 'boolean',
        'AnnouncementImportance' => 'integer',  // Ensure integer casting
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
}
