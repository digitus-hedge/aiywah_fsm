<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $guarded = [];

    /**
     * Roles that have some access to this permission.
     * The pivot carries the access level and grant flag.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot(['access', 'can_grant'])
            ->withTimestamps();
    }
}