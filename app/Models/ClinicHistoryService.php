<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'quantity',
        'observations'
    ];

    public function service () {
        return $this->belongsTo('App\Models\Service');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }
}
