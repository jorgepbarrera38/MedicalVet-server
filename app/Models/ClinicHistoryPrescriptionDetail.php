<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryPrescriptionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'product_id',
        'quantity',
        'posology',
    ];

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }
}
