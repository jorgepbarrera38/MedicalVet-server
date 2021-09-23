<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_purposes_id',
        'reason_consultation',
        'indications',
        'symptoms',
        'anamnesis',
        'pet_id',
        'user_id'
    ];

    public function pet () {
        return $this->belongsTo('App\Models\Pet');
    }

    public function user () {
        return $this->belongsTo('App\Models\User');
    }

    public function consultationPurposes () {
        return $this->belongsTo('App\Models\ConsultationPurposes');
    }

    public function diagnoses () {
        return $this->hasMany('App\Models\ClinicHistoryDiagnosis');
    }

    public function vaccines () {
        return $this->hasMany('App\Models\Vaccine');
    }

    public function dewormings () {
        return $this->hasMany('App\Models\Deworming');
    }

    public function products () {
        return $this->hasMany('App\Models\ClinicHistoryProduct');
    }

    public function services () {
        return $this->hasMany('App\Models\ClinicHistoryService');
    }

    public function records () {
        return $this->hasMany('App\Models\Record');
    }

    public function physiologicalConstants () {
        return $this->hasOne('App\Models\PhysiologicalConstant');
    }

    public function clinicHistoryAttachments () {
        return $this->hasMany('App\Models\ClinicHistoryAttachment');
    }

    public function clinicHistoryPrescription () {
        return $this->hasOne('App\Models\ClinicHistoryPrescription');
    }

    public function clinicHistoryReviewBySystem () {
        return $this->hasOne('App\Models\ClinicHistoryReviewBySystem');
    }
}
