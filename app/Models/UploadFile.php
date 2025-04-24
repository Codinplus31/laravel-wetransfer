<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_session_id',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function uploadSession()
    {
        return $this->belongsTo(UploadSession::class);
    }
}
