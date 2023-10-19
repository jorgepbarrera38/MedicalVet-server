<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'number_bill',
        'total',
        'cancelled',
        'owner_id',
        'pet_id',
    ];
}
