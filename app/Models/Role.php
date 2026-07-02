<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $casts = [
        'is_grantable' => 'boolean',
    ];

    /**
     * Permissions attached to this role.
     * Pivot carries: access (yes|no|rls) and can_grant (bool).
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot(['access', 'can_grant'])
            ->withTimestamps()
            ->orderBy('permissions.sort_order');
    }
}