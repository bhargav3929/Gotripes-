<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEActivityDetail extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_UAEActivityDetails';
    protected $primaryKey = 'id';
    // This table uses the project's manual audit columns (createdBy/createdDate/
    // modifiedBy/modifiedDate) like every other model here, and has no
    // created_at/updated_at columns on production. Leaving timestamps on caused
    // "Unknown column 'updated_at'" on insert/update.
    public $timestamps = false;
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
