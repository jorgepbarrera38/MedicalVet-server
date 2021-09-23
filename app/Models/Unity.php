<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'user_id'
    ];

    public function dewormings () {
        return $this->hasMany('App\Models\Deworming');
    }
}
