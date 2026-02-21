<?php
// app/Models/UAEVisaMaster.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UAEVisaMaster extends Model
{
    protected $table = 'uae_visa_master';
    protected $primaryKey = 'vID';
    public $timestamps = false;

    protected $fillable = [
        'UAEVisaDuration', 'UAEVPrice', 'createdBy', 'createdDate', 'modifiedBy', 'modifiedDate', 'isActive'
    ];
}
