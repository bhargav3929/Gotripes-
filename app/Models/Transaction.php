<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
   
 public function referee()
    {
        return $this->belongsTo(Referee::class, 'refereeID', 'id');
    } 
    public function referrer()
    {
        return $this->belongsTo(Referrer::class, 'referrerID', 'id');
    } 
    public function previousstate()
    {
        return $this->belongsTo(tolval::class, 'previousState', 'id');
    }
    public function currentstate()
    {
        return $this->belongsTo(tolval::class, 'currentState', 'id');
    }
    
}
