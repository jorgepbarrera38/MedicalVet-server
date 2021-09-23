<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryReviewBySystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tegumentary_system_state',
        'tegumentary_system_observations',
        'nervous_system_state',
        'nervous_system_observations',
        'genitourinary_system_state',
        'genitourinary_system_observations',
        'lymph_nodes_state',
        'lymph_nodes_observations',
        'musculoskeletal_system_state',
        'musculoskeletal_system_observations',
        'reproductive_system_state',
        'reproductive_system_observations',
        'digestive_system_state',
        'digestive_system_observations',
        'oral_cavity_state',
        'oral_cavity_observations',
    ];
}
