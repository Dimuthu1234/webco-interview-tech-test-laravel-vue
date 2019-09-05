<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telephone extends Model
{
    protected $fillable = ['customer_id', 'telephone'];

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
}
