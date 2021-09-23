<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'identification',
        'names',
        'surnames',
        'address',
        'phonenumber',
        'phonenumber2',
        'email',
        'city_id',
        'user_id'
    ];

    public function pets()
    {
        return $this->hasMany('App\Models\Pet');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }
}
