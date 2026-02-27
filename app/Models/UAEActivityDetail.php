<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UAEActivityDetail extends Model
{
    protected $table = 'tbl_UAEActivityDetails';
    protected $primaryKey = 'id';
    public $timestamps = true;
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
