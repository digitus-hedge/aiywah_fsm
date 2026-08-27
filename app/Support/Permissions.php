<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class Permissions
{
    /**
     * Returns the access level token for a role on a permission key,
     * e.g. 'yes', 'no', 'rls', 'view', 'view_rls', 'edit_own', or null.
     */
    public static function for(?string $roleCode, string $permissionKey): ?string
    {
        if (!$roleCode) {
            return null;
        }

        $row = DB::table('permission_role')
            ->join('roles', 'roles.id', '=', 'permission_role.role_id')
            ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
            ->where('roles.code', $roleCode)
            ->where('permissions.key', $permissionKey)
            ->select('permission_role.access', 'permission_role.is_readonly')
            ->first();

        if (!$row) {
            return null;
        }

        // Reconstruct the original token from the stored columns.
        if ($row->access === 'rls' && $row->is_readonly) {
            return 'view_rls';
        }
        if ($row->access === 'yes' && $row->is_readonly) {
            return 'view';
        }

        return $row->access; // 'yes' | 'no' | 'rls'
    }
}