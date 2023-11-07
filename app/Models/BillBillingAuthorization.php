<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillBillingAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'handles_prefix',
    ];

    public function billBillingAuthorizationDetails () {
        return $this->hasMany('App\Models\BillBillingAuthorizationDetail');
    }
}
