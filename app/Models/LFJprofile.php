<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LFJprofile extends Model
{
    // Explicitly specify the table if it doesn't match the Laravel convention
    protected $table = 'tbllfjprofiles';

    // Specify the primary key if it's not `id`
    protected $primaryKey = 'LFJid';

    // If the PK is auto-incrementing and not an integer, set $incrementing and $keyType accordingly
    public $incrementing = true;
    protected $keyType = 'int';

    // Add fillable or guarded properties as needed (example)
    protected $fillable = [
        'LFJProfile_status',
        'LFJName',
        'LFJMobile',
        'LFJEmail',
        'LFJAge',
        'LFJNationality',
        'LFJProfession',
        'LFJExperience',
        'LFJVisaStatus',
        'LFJExpectedSalary',
        'LFJLastCompany',
        'LFJLastLocation',
        'LFJPreferredLocation',
        'LFJNoticePeriod',
        'LFJReferenceName',
        'LFJReferencePosition',
        'LFJReferenceMobile',
        'LFJResume',
        'LFJLocationStatus', // ADD THIS NEW COLUMN
        // ...add all other columns you expect to mass-assign
    ];

    // Relationship: Each profile STATUS belongs to one status row
    public function status()
    {
        return $this->belongsTo(LFJProfileStatus::class, 'LFJProfile_status', 'id');
    }
}
