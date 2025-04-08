<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EmailVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired()
    {
        return Carbon::now()->gt($this->expires_at);
    }
}
