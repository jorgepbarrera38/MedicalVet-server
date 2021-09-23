<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'product_id',
        'next_date'
    ];

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }

    public function pet () {
        return $this->belongsTo('App\Models\Pet');
    }
}
