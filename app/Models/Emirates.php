<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emirates extends Model
{
    protected $table = 'tbl_emirates';
    protected $primaryKey = 'emiratesID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'emiratesName',
        'emiratesDescription', 
        'emiratesImage',
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
    ];

    protected $dates = [
        'createdDate',
        'modifiedDate',
    ];

    // Relationship with UAE Activities
    public function activities()
    {
        return $this->hasMany(UAEActivity::class, 'emiratesID', 'emiratesID');
    }

    // Get active emirates
    public static function getActiveEmirates()
    {
        return self::where('isActive', 1)->orderBy('emiratesName')->get();
    }

    // Get emirates with activity count
    public static function getEmiratesWithActivityCount()
    {
        return self::where('isActive', 1)
            ->withCount(['activities' => function($query) {
                $query->where('isActive', 1);
            }])
            ->orderBy('emiratesName')
            ->get();
    }
}
