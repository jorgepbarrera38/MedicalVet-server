<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHistoryAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_history_id',
        'attachment_type_id',
    ];

    public function attachmentType () {
        return $this->belongsTo('App\Models\AttachmentType');
    }
}
