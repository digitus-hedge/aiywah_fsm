<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\LogsActivity;
class Role extends Model
{
     use LogsActivity;
    protected $casts = [
        'is_grantable' => 'boolean',
    ];
    
    /**
     * Permissions attached to this role.
     * Pivot carries: access (yes|no|rls), can_grant (bool), is_readonly (bool).
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot(['access', 'can_grant', 'is_readonly'])
            ->withTimestamps()
            ->orderBy('permissions.sort_order');
    }
}