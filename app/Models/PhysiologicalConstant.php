<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysiologicalConstant extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight',
        'temperature',
        'heart_rate',
        'breathing_frequency'
    ];
}
