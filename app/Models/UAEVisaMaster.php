<?php
// app/Models/UAEVisaMaster.php
namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEVisaMaster extends Model
{
    use BelongsToCompany;

    protected $table = 'uae_visa_master';
    protected $primaryKey = 'vID';
    public $timestamps = false;

    protected $fillable = [
        'UAEVisaDuration', 'UAEVPrice', 'createdBy', 'createdDate', 'modifiedBy', 'modifiedDate', 'isActive', 'company_id',
    ];
}
