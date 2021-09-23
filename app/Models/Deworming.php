<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deworming extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'dose',
        'unity_id',
        'next_date'
    ];

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }

    public function unity () {
        return $this->belongsTo('App\Models\Unity');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }
}
