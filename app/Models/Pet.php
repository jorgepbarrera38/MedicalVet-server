<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'date_born',
        'sex',
        'species_id',
        'race_id',
        'color',
        'size',
        'observations',
        'user_id',
        'photo',
    ];

    public function owner () {
        return $this->belongsTo('App\Models\Owner');
    }
    
    public function species () {
        return $this->belongsTo('App\Models\Species');
    }

    public function race () {
        return $this->belongsTo('App\Models\Race');
    }

    public function clinicHistories () {
        return $this->hasMany('App\Models\ClinicHistory');
    }

    public function appointments () {
        return $this->hasMany('App\Models\Appointment');
    }
}
