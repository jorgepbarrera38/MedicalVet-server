<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'price',
        'user_id'
    ];

    public function clinicHistoryServices () {
        return $this->hasMany('App\Models\ClinicHistoryService');
    }
}
