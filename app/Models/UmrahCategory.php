<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class UmrahCategory extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_umrah_categories';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function packages()
    {
        return $this->hasMany(UmrahPackage::class, 'category_id');
    }
}
