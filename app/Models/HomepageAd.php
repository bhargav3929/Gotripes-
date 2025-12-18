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
        'createddate' => 'datetime',
        'modifieddate' => 'datetime'
    ];

    // Scope for active records only
    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }
}
