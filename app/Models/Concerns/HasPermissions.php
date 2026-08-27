<?php

namespace App\Models\Concerns;

/**
 * Permission helpers for the User model.
 * ...(docblock unchanged)...
 *
 *   4. Key present in fd_grants / ac_grants → allowed (Admin-granted extension).
 *   5. Key present in ext_grants → allowed at whatever access level ('yes'/'rls')
 *      was computed when the extension was saved. This lets a user borrow a
 *      specific permission from a role other than their own — e.g. an FD
 *      account extended with an HP permission, or an SE extended with an
 *      AC permission — while still respecting whether that borrowed access
 *      was full ('yes') or record-filtered ('rls').
 */
trait HasPermissions
{
    protected ?array $permissionAccessCache = null;

    public function hasAccess(?string $key): bool
    {
        if (!$key) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->accessFor($key) === 'yes') {
            return true;
        }

        return $this->hasGrant($key);
    }

    public function hasAnyAccess(?string $key): bool
    {
        if (!$key) {
            return false;
        }
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($this->accessFor($key), ['yes', 'rls'], true)
            || $this->hasGrant($key);
    }

    /**
     * Raw access level for a key: 'yes' | 'rls' | 'no'.
     * Falls back to ext_grants when the role's own pivot has no access
     * at all for this key — a cross-role extension only kicks in where
     * the role wasn't already granted something of its own.
     */
    public function accessFor(?string $key): string
    {
        if (!$key) {
            return 'no';
        }

        $roleAccess = $this->permissionAccessMap()[$key]['access'] ?? 'no';

        if ($roleAccess !== 'no') {
            return $roleAccess;
        }

        return $this->extAccessFor($key);
    }

    /**
     * Access level granted purely via ext_grants (cross-role extension),
     * ignoring the role's own pivot. Returns 'yes' | 'rls' | 'no'.
     */
    protected function extAccessFor(?string $key): string
    {
        if (!$key) {
            return 'no';
        }

        $extGrants = is_array($this->ext_grants) ? $this->ext_grants : [];
        $value = $extGrants[$key] ?? null;

        return in_array($value, ['yes', 'rls'], true) ? $value : 'no';
    }

    public function isFiltered(?string $key): bool
    {
        if (!$key || $this->isSuperAdmin()) {
            return false;
        }

        return $this->accessFor($key) === 'rls';
    }

    public function isReadonly(?string $key): bool
    {
        if (!$key || $this->isSuperAdmin()) {
            return false;
        }

        if ($this->hasGrant($key)) {
            return false;
        }

        // An ext_grants extension carries write capability too, same as
        // fd_grants/ac_grants — it's an extension of capability, not a peek.
        if ($this->extAccessFor($key) !== 'no') {
            return false;
        }

        return (bool) ($this->permissionAccessMap()[$key]['is_readonly'] ?? false);
    }

    public function writesOwnOnly(?string $key): bool
    {
        if (!$key || $this->isSuperAdmin()) {
            return false;
        }

        if ($this->hasGrant($key)) {
            return false;
        }

        if ($this->extAccessFor($key) !== 'no') {
            return false;
        }

        return (bool) ($this->permissionAccessMap()[$key]['write_own_only'] ?? false);
    }

    public function writeNeedsOwnership(?string $key): bool
    {
        return $this->isFiltered($key) || $this->writesOwnOnly($key);
    }

    public function canWrite(?string $key): bool
    {
        return $this->hasAnyAccess($key) && !$this->isReadonly($key);
    }

    public function canWriteRecord(?string $key, $record): bool
    {
        if (! $this->canWrite($key)) {
            return false;
        }

        if (! $this->writeNeedsOwnership($key)) {
            return true;
        }

        return method_exists($record, 'isOwnedBy') && $record->isOwnedBy($this);
    }

    public function hasGrant(?string $key): bool
    {
        if (!$key) {
            return false;
        }
        $grants = array_merge(
            is_array($this->fd_grants) ? $this->fd_grants : [],
            is_array($this->ac_grants) ? $this->ac_grants : [],
        );
        return in_array($key, $grants, true);
    }

    public function canBeGranted(?string $key): bool
    {
        if (!$key) {
            return false;
        }

        return (bool) ($this->permissionAccessMap()[$key]['can_grant'] ?? false);
    }

    protected function permissionAccessMap(): array
    {
        if ($this->permissionAccessCache !== null) {
            return $this->permissionAccessCache;
        }

        $role = $this->relationLoaded('role') ? $this->role : $this->role()->first();

        if (!$role) {
            return $this->permissionAccessCache = [];
        }

        return $this->permissionAccessCache = $role->permissions
            ->mapWithKeys(fn ($permission) => [
                $permission->key => [
                    'access'         => $permission->pivot->access ?? 'no',
                    'can_grant'      => (bool) ($permission->pivot->can_grant ?? false),
                    'is_readonly'    => (bool) ($permission->pivot->is_readonly ?? false),
                    'write_own_only' => (bool) ($permission->pivot->write_own_only ?? false),
                ],
            ])
            ->toArray();
    }

    public function flushPermissionCache(): void
    {
        $this->permissionAccessCache = null;
    }

    /**
 * Can this user reach the User Directory page at all?
 *
 * True if they have direct access to user_directory, OR if they have
 * user_provisioning access — provisioning lives inside the directory
 * page (the "New User" button), so a user granted provisioning rights
 * must be able to open the page even without separate directory access.
 */
public function canAccessUserDirectory(): bool
{
    return $this->hasAnyAccess('user_directory') || $this->hasAccess('user_provisioning');
}
}