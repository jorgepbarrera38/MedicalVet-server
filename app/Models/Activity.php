<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'service_id',
    ];

    public function appointments () {
        return $this->hasMany('App\Models\Appointment');
    }

    public function service () {
        return $this->belongsTo('App\Models\Service');
    }
}
