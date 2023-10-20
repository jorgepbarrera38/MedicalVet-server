<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deworming extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'product_id',
        'registration_date',
        'application_date',
        'next_date',
        'user_id'
    ];

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }

    public function user () {
        return $this->belongsTo('App\Models\User');
    }
}
