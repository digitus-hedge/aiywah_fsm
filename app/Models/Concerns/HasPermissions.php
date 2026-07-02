<?php

namespace App\Models\Concerns;

/**
 * Permission helpers for the User model.
 *
 * Usage: add `use HasPermissions;` inside the User class.
 *
 * Access resolution (per permission key):
 *   1. Super Admin (role code 'SA') → always allowed.
 *   2. Role pivot access === 'yes'  → allowed.
 *   3. Role pivot access 'rls'/'no' → denied (rls links are hidden per spec).
 *   4. Key present in the user's fd_grants array → allowed (Admin-granted extension).
 *
 * fd_grants is expected to be a JSON/array column of permission KEYS.
 */
trait HasPermissions
{
    /**
     * In-request cache of the role's permission access map: ['key' => 'yes|no|rls'].
     */
    protected ?array $permissionAccessCache = null;

    /**
     * Does this user have (unfiltered) access to the given permission key?
     */
    public function hasAccess(?string $key): bool
    {
        if (!$key) {
            return false;
        }

        // 1. Super Admin bypasses all checks.
        if ($this->isSuperAdmin()) {
            return true;
        }

        // 2. Role-granted access ('yes' only; 'rls' and 'no' are treated as no access).
        $map = $this->permissionAccessMap();
        if (($map[$key] ?? 'no') === 'yes') {
            return true;
        }

        // 3. Extra per-user grants (e.g. Admin extending a Front Desk user).
        return $this->hasGrant($key);
    }

    /**
     * Does the user have any access at all — including filtered ('rls')?
     * Useful if you ever want to show filtered links elsewhere.
     */
    public function hasAnyAccess(?string $key): bool
    {
        if (!$key) {
            return false;
        }
        if ($this->isSuperAdmin()) {
            return true;
        }
        $access = $this->permissionAccessMap()[$key] ?? 'no';
        return in_array($access, ['yes', 'rls'], true) || $this->hasGrant($key);
    }

    /**
     * Is this permission key explicitly granted to the user via fd_grants?
     */
    public function hasGrant(?string $key): bool
    {
        if (!$key) {
            return false;
        }
        $grants = $this->fd_grants ?? [];
        return is_array($grants) && in_array($key, $grants, true);
    }

    /**
     * Convenience: is the user a Super Admin?
     */
    public function isSuperAdmin(): bool
    {
        return optional($this->role)->code === 'SA';
    }

    /**
     * Build (and cache) the ['permission_key' => 'access'] map for this user's role.
     */
    protected function permissionAccessMap(): array
    {
        if ($this->permissionAccessCache !== null) {
            return $this->permissionAccessCache;
        }

        $role = $this->relationLoaded('role') ? $this->role : $this->role()->first();

        if (!$role) {
            return $this->permissionAccessCache = [];
        }

        // Expects Role::permissions() belongsToMany with pivot column `access`.
        $this->permissionAccessCache = $role->permissions
            ->pluck('pivot.access', 'key')
            ->toArray();

        return $this->permissionAccessCache;
    }
}