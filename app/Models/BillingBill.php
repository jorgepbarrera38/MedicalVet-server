<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'prefix',
        'number_bill',
        'prefix_and_number_bill',
        'total',
        'money_given',
        'cancelled',
        'owner_id',
        'pet_id',
        'bill_billing_authorization_detail_id',
    ];

    public function billingBillDetails () {
        return $this->hasMany('App\Models\BillingBillDetail');
    }

    public function owner () {
        return $this->belongsTo('App\Models\Owner');
    }

    public function scopeNumberBill ($query, $numberBill) {
        if ($numberBill) {
            $query->where('prefix_and_number_bill', 'like', '%' . $numberBill . '%');
        }
    }

    public function billBillingAuthorizationDetail () {
        return $this->belongsTo('App\Models\BillBillingAuthorizationDetail');
    }
}
