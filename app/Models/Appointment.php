<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'consultingroom_id',
        'activity_id',
        'date_time_start',
        'duration',
        'date_time_finish',
        'observations',
        'state',
        'user_id_creates',
        'user_id_fulfill',
        'user_id_unfulfill',
        'user_id_cancel',
        'reason_cancellation_id',
        'clinic_history_id',
        'billing_pending_billing_id',
        'clinic_history_service_id',
    ];

    public function pet () {
        return $this->belongsTo('App\Models\Pet');
    }

    public function consultingroom () {
        return $this->belongsTo('App\Models\Consultingroom');
    }

    public function activity () {
        return $this->belongsTo('App\Models\Activity');
    }

    public function reasonCancellation () {
        return $this->belongsTo('App\Models\ReasonCancellation');
    }

    public function userThatCreates () {
        return $this->belongsTo('App\Models\User', 'user_id_creates');
    }

    public function userThatFulFill () {
        return $this->belongsTo('App\Models\User', 'user_id_fulfill');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }

    public function userThatUnfulfill () {
        return $this->belongsTo('App\Models\User', 'user_id_unfulfill');
    }

    public function userThatCancel () {
        return $this->belongsTo('App\Models\User', 'user_id_cancel');
    }
}
