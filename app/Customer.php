<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'nic', 'address'];

    public function telephones()
    {
        return $this->hasMany('App\Telephone', 'customer_id', 'id');
    }

}
