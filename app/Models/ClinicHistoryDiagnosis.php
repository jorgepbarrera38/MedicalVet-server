<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryDiagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'diagnosis_id',
        'type',
    ];

    public function diagnosis () {
        return $this->belongsTo('App\Models\Diagnosis');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }
}
