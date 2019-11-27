<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function scopeWithUser($query){
        return $query->where('user_id', request()->user()->id);
    }
}
