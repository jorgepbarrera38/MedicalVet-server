<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillBillingAuthorizationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_billing_authorization_id',
        
        'active',

        'resolution_number',
        'resolution_date',

        'initial_bill',
        'final_bill',

        'initial_date',
        'final_date',
    ];

    public function billBillingAuthorization () {
        return $this->belongsTo('App\Models\BillBillingAuthorization');
    }
}
