<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Base policy for any resource governed by a permission key.
 *
 * Extend it and set $permissionKey:
 *
 *   class ServiceRequestPolicy extends PermissionPolicy
 *   {
 *       protected string $permissionKey = 'assigned';
 *   }
 *
 * Register in AppServiceProvider::boot():
 *   Gate::policy(ServiceRequest::class, ServiceRequestPolicy::class);
 *
 * This is where 'edit_own' is actually enforced. Middleware sees only the
 * route, not the record, so it cannot tell whose ticket is being updated.
 */
abstract class PermissionPolicy
{
    protected string $permissionKey;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyAccess($this->permissionKey);
    }

    public function view(User $user, Model $record): bool
    {
        if (! $user->hasAnyAccess($this->permissionKey)) {
            return false;
        }

        // Under 'rls' the read scope is per-record too.
        if ($user->isFiltered($this->permissionKey)) {
            return method_exists($record, 'isOwnedBy') && $record->isOwnedBy($user);
        }

        return true;
    }

    public function create(User $user): bool
    {
        // Nothing to own yet - page-level write capability is the whole test.
        return $user->canWrite($this->permissionKey);
    }

    public function update(User $user, Model $record): bool
    {
        return $user->canWriteRecord($this->permissionKey, $record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $user->canWriteRecord($this->permissionKey, $record);
    }
}