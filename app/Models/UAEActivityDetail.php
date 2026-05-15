<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEActivityDetail extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_UAEActivityDetails';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'company_id',
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
