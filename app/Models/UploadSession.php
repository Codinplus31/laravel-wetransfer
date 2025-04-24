<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UploadSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'expires_at',
        'email_to_notify',
        'password',
        'download_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->token = $model->token ?? Str::random(32);
        });
    }

    public function files()
    {
        return $this->hasMany(UploadFile::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}
