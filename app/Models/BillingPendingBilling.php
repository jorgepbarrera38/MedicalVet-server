<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingPendingBilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'code',
        'description',
        'product_id',
        'service_id',
        'price',
        'quantity',
        'owner_id',
        'pet_id',
        'billed'
    ];

    public function billingBillDetail () {
        return $this->hasOne('App\Models\BillingBillDetail');
    }
}
