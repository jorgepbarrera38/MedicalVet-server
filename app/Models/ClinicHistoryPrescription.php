<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_history_id',
        'pet_id',
        'observations',
        'user_id',
    ];

    public function clinicHistoryPrescriptionDetails () {
        return $this->hasMany('App\Models\ClinicHistoryPrescriptionDetail', 'prescription_id');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }

    public function pet () {
        return $this->belongsTo('App\Models\Pet');
    }

    public function user () {
        return $this->belongsTo('App\Models\User');
    }
}
