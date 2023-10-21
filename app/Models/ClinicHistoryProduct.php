<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'observations',
        'billing_pending_billing_id'
    ];

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }

    public function clinicHistory () {
        return $this->belongsTo('App\Models\ClinicHistory');
    }

    public function billingPendingBilling () {
        return $this->belongsTo('App\Models\BillingPendingBilling');
    }
}
