<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nit',
        'slogan',
        'address',
        'email',
        'email_host',
        'email_port',
        'email_password',
        'email_encryption'
    ];
}
