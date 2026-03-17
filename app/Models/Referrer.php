<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
     public function loantype()
    {
        return $this->belongsTo(Loantype::class, 'tolID', 'id');
    }
}
