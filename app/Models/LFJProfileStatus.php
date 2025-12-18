<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LFJProfileStatus extends Model
{
    protected $table = 'tbl_LFJProfileStatus';
    protected $primaryKey = 'id';

    // Add fillable or guarded properties as needed (example)
    protected $fillable = [
        'status_name',
    ];

    // Relationship: STATUS has many PROFILES using this status
    public function profiles()
    {
        return $this->hasMany(LFJProfile::class, 'LFJProfile_status', 'id');
    }
}
