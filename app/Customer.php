<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'username', 'name', 'lastName',
    ];

    public function beers()
    {
        return $this->belongsToMany('App\Beer');
    }
}
