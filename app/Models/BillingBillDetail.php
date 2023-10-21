<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingBillDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_bill_id',
        'code',
        'description',
        'product_id',
        'service_id',
        'price',
        'quantity',
        'billing_pending_billing_id',
    ];

    public function billingBill () {
        return $this->belongsTo('App\Models\BillingBill');
    }
}
