<?php
// app/Models/UAEActivityDetail.php
// app/Models/UAEActivityDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UAEActivityDetail extends Model
{
    protected $table = 'tbl_UAEActivityDetails';
    protected $primaryKey = 'detailsID';
    public $timestamps = false;  // set true if you add timestamps
    protected $fillable = [
        'detailsOverview',
        'detailsIminfo',
        'detailsHighlights',
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'activityID',
        'activityImage'
    ];

    public function activity()
    {
        return $this->belongsTo(UAEActivity::class, 'activityID', 'activityID');
    }
}


