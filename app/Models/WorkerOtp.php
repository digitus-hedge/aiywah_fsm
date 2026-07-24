<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class WorkerOtp extends Model
{
    protected $fillable = ['email', 'otp_hash', 'attempts', 'expires_at', 'verified_at'];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function matches(string $otp): bool
    {
        return Hash::check($otp, $this->otp_hash);
    }
}