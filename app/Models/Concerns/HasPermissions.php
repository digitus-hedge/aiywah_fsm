<?php

namespace App\Models\Concerns;

/**
 * Permission helpers for the User model.
 *
 * Usage: add `use HasPermissions;` inside the User class.
 *
 * Three independent axes per permission key:
 *
 *   access         → WHICH ROWS may they read: 'yes' all, 'rls' own, 'no' none
 *   is_readonly    → MAY THEY EDIT at all: true = page visible but frozen
 *   write_own_only → writes narrowed to rows they own, reads unrestricted
 *
 * Access resolution:
 *   1. Super Admin (role code 'SA') → always allowed, always writable.
 *   2. Role pivot access === 'yes'  → allowed.
 *   3. Role pivot access 'rls'/'no' → denied by hasAccess() (rls links are
 *      hidden per spec); use hasAnyAccess() where filtered views are shown.
 *   4. Key present in fd_grants / ac_grants → allowed (Admin-granted extension).
 *
 * Grant columns are JSON/array columns of permission KEYS.
 * Granted keys are writable — a grant is an extension of capability, not a
 * peek. If you ever need read-only grants, store them in a separate column
 * and add the check to isReadonly().
 *
 * REQUIRES on Role:
 *   ->withPivot('access', 'can_grant', 'is_readonly', 'write_own_only')
 */
trait HasPermissions
{
    /**
     * In-request cache of the role's pivot rows, keyed by permission key.
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
        if ($this->accessFor($key) === 'yes') {
            return true;
        }

        // 3. Extra per-user grants (e.g. Admin extending a Front Desk user).
        return $this->hasGrant($key);
    }

    /**
     * Does the user have any access at all — including filtered ('rls')?
     */
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
     */
    public function accessFor(?string $key): string
    {
        if (!$key) {
            return 'no';
        }

        return $this->permissionAccessMap()[$key]['access'] ?? 'no';
    }

    /**
     * Must the row list on this page be scoped to the user's own records?
     */
    public function isFiltered(?string $key): bool
    {
        if (!$key || $this->isSuperAdmin()) {
            return false;
        }

        return $this->accessFor($key) === 'rls';
    }

    /**
     * Can see the page but must not mutate anything on it.
     */
    public function isReadonly(?string $key): bool
    {
        if (!$key || $this->isSuperAdmin()) {
            return false;
        }

        // A per-user grant carries write capability.
        if ($this->hasGrant($key)) {
            return false;
        }

        return (bool) ($this->permissionAccessMap()[$key]['is_readonly'] ?? false);
    }

    /**
     * Are writes limited to rows the user owns, even though they read
     * everything? (The 'edit_own' token.)
     */
    public function writesOwnOnly(?string $key): bool
    {
        if (!$key || $this->isSuperAdmin()) {
            return false;
        }

        if ($this->hasGrant($key)) {
            return false;
        }

        return (bool) ($this->permissionAccessMap()[$key]['write_own_only'] ?? false);
    }

    /**
     * Does write capability here depend on who owns the record?
     *
     * True for 'rls' — the read scope already limits them, but a crafted
     * request could still target someone else's id — and for 'edit_own',
     * where the read scope does not limit them at all and this check is
     * the only guard.
     */
    public function writeNeedsOwnership(?string $key): bool
    {
        return $this->isFiltered($key) || $this->writesOwnOnly($key);
    }

    /**
     * May the user create / update / delete on this page at all?
     * Page-level only — use canWriteRecord() once you have the record.
     */
    public function canWrite(?string $key): bool
    {
        return $this->hasAnyAccess($key) && !$this->isReadonly($key);
    }

    /**
     * May the user mutate this specific record?
     * Page-level write capability, narrowed by ownership where required.
     */
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

    /**
     * Is this permission key explicitly granted to the user?
     * Reads both grant columns: fd_grants (Front Desk) and ac_grants (Accounts).
     */
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

    /**
     * May an Admin extend this permission to the user?
     * (Drives the checkbox list on the User Provisioning screen.)
     */
    public function canBeGranted(?string $key): bool
    {
        if (!$key) {
            return false;
        }

        return (bool) ($this->permissionAccessMap()[$key]['can_grant'] ?? false);
    }

    /**
     * Build (and cache) the ['permission_key' => pivot data] map for this user's role.
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

        // Expects Role::permissions() belongsToMany with
        // ->withPivot('access', 'can_grant', 'is_readonly', 'write_own_only')
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

    /**
     * Call after changing the user's role or grants within a single request.
     */
    public function flushPermissionCache(): void
    {
        $this->permissionAccessCache = null;
    }
}