<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\HasPermissions; 

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasPermissions; 
    protected $fillable = ['name', 'email', 'password', 'role_id', 'domains', 'fd_grants', 'status'];

    protected $casts = [
         'password'  => 'hashed',
        'domains'   => 'array',
        'fd_grants' => 'array',
    ];

    public function role() { 
        return $this->belongsTo(Role::class);
     }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
