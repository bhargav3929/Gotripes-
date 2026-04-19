<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'title',
        'image',
        'description',
        'link',
        'isActive',
    ];
}
