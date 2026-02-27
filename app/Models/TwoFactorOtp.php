<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorOtp extends Model
{
    protected $fillable = [
        'user_id',
        'otp_hash',
        'expires_at',
        'verified_at',
        'attempt_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempt_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
