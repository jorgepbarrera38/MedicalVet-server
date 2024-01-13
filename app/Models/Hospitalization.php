<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitalization extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'initial_date',
        'final_date',
        'status',
        'service_id',
        'quantity_hours',
        'user_id',
        'user_finalized_id',
        'clinic_history_service_id',
    ];

    public function pet () {
        return $this->belongsTo('App\Models\Pet');
    }

    public function service () {
        return $this->belongsTo('App\Models\Service');
    }
}
