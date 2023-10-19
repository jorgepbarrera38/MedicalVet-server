<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationPurposes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reason_consultation',
        'symptoms',
        'indications',
        'records',
        'physiological_constants',
        'diagnoses',
        'vaccines',
        'dewormings',
        'products_and_services_applied',
        'attachments',
        'anamnesis',
        'prescription',
        'review_by_systems',
        'user_id',
    ];

    public function clinicHistories () {
        return $this->hasMany('App\Models\ClinicHistory');
    }
}
