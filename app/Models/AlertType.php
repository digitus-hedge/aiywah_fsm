<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AlertType extends Model
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_HOP   = 'hop';
    public const ROLE_SE    = 'se';
    public const ROLE_ML    = 'ml';
    public const ROLE_FD    = 'fd';
    public const ROLE_ACC   = 'acc';
    public const ROLE_AC    = 'ac';

    protected $fillable = [
        'role', 'key', 'title', 'description', 'sort_order', 'is_default_checked',
    ];

    protected $casts = [
        'is_default_checked' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeForRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role)->orderBy('sort_order');
    }

    public function userPermissions()
    {
        return $this->hasMany(UserAlertPermission::class);
    }
}