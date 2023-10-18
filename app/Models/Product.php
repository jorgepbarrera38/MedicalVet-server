<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'price',
        'vaccine',
        'deworming',
        'user_id'
    ];
    
    public function clinicHistoryProducts () {
        return $this->hasMany('App\Models\ClinicHistoryProduct');
    }

    public function vaccines () {
        return $this->hasMany('App\Models\Vaccine');
    }

    public function dewormings () {
        return $this->hasMany('App\Models\Deworming');
    }

}
