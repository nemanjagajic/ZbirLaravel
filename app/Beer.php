<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    protected $fillable = [
        'name', 'price', 'onStock',
    ];

    public function customers()
    {
        return $this->belongsToMany('App\Customer')->withTimestamps();
    }
}
