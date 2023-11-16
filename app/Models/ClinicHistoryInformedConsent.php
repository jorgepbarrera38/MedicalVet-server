<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryInformedConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'registration_date',
        'user_id',
        'procedure',
        'risks',
        'informed_consent_template_id',
    ];

    public function pet () {
        return $this->belongsTo('App\Models\Pet');
    }

    public function informedConsentTemplate () {
        return $this->belongsTo('App\Models\InformedConsentTemplate');
    }
}
