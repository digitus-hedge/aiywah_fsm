<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the sidebar pages as permissions, then attach per-role access
     * levels via the permission_role pivot.
     *
     * Matrix tokens
     * ─────────────
     *              access   can_grant  is_readonly  write_own_only
     *   'yes'      yes      false      false        false   see all,  edit all
     *   'no'       no       false      false        false   no access
     *   'rls'      rls      false      false        false   see own,  edit own
     *   'grant'    no       true       false        false   Admin may extend
     *   'view'     yes      false      true         false   see all,  edit none
     *   'view_rls' rls      false      true         false   see own,  edit none
     *   'edit_own' yes      false      false        true    see all,  edit own
     *
     * Three independent axes:
     *   access         → which rows may they READ
     *   is_readonly    → may they write at all
     *   write_own_only → writes narrowed to rows they own
     *
     * 'edit_own' is the one that needs per-record enforcement: the page and
     * the row list are fully visible, so only a policy check on the specific
     * record can stop the write. Middleware cannot catch it.
     */
    private const TOKENS = ['yes', 'no', 'rls', 'grant', 'view', 'view_rls', 'edit_own'];

    public function run(): void
    {
        // ── 1. Sidebar pages ─────────────────────────────────────────
        // [key, name, section, icon, route, sort_order]
        //
        // Keys must be unique: updateOrInsert matches on `key`, so a repeated
        // key silently overwrites the earlier row rather than adding one.
        $permissions = [
            // Main
            ['dashboard',              'Dashboard',              'Main',              'grid',           'dashboard',              1],
            ['sr_registration',        'SR Registration',        'Main',              'file-plus',      'sr_registration',        2],
            ['sr_explorer',            'SR Explorer',            'Main',              'search',         'sr_explorer',            3],
            ['kanban_view',            'Ticket Summary',         'Main',              'trello',         'kanban_view',            4],

            // Customer
            ['client_accounts',        'Customer Accounts',      'Customer',          'user-plus',      'clients.create',         5],
            ['client_directory',       'Customer Directory',     'Customer',          'book-open',      'clients.directory',      6],
            ['project_site_directory', 'Projects & Sites',       'Customer',          'map-pin',        'project_site_directory', 7],

            // Workflow
            ['inquiry_approval',       'Inquiry Approval',       'Workflow',          'inbox',          'inquiry-approval.index', 8],
            ['dispatch_engine',        'Dispatch Engine',        'Workflow',          'send',           'dispatch_engine',        9],
            ['assigned',               'Approved SR',            'Workflow',          'user-check',     'assigned',              10],
            ['qc_review',              'QC Review',              'Workflow',          'check-circle',   'qc_review',             11],
            ['rework_sr',              'Rework SR',              'Workflow',          'rotate-ccw',     'rework_sr',             12],
            ['completed',              'Completed SR',           'Workflow',          'award',          'completed',             13],

            // Finance
            ['quotation_desk',         'Quotation Desk',         'Finance',           'file-text',      'quotation_desk',        14],
            ['invoice_panel',          'Invoice Panel',          'Finance',           'credit-card',    'invoice_panel',         15],
            ['expense_ledger',         'Expense Ledger',         'Finance',           'dollar-sign',    'expense_ledger',        16],

            // System
            ['analytics',              'Analytics',              'System',            'bar-chart-2',    null,                    17],
            ['invoice_hop_approve',    'Invoice HoP Approval',   'Finance',           null,             null,                    18], 
            ['user_directory',         'User Directory',         'System',            'users',          'user_directory',        19],
            ['user_provisioning',      'User Provisioning',      'System',            'shield',         'user_provisioning',     20],
            ['master_data',            'Master Data',            'System',            'database',       'masters.index',         21],
            ['wa_notification_log',    'WhatsApp Notifications', 'System',            'message-circle', 'wa_notification_log',   22],
            ['activity-log',           'Activity Log',           'System',            'activity',       'activity-log',          23],
            ['user_delete',            'Delete Users',           'System',             'trash-2',        null,                   24],
            ['client_delete',          'Delete Customers',       'Customer',           'trash-2',        null,                   25],
        ];

        foreach ($permissions as [$key, $name, $section, $icon, $route, $order]) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $key],
                [
                    'name'       => $name,
                    'section'    => $section,
                    'icon'       => $icon,
                    'route'      => $route,
                    'sort_order' => $order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ── 2. Access matrix: role code → (permission key → token) ───
        // Every key is listed for every role, so the pivot always holds a
        // definitive row rather than relying on a missing row meaning "no".
        $all = array_column($permissions, 0);

        /** Start from a full deny, then override. */
        $deny = array_fill_keys($all, 'no');

        $matrix = [
            // Super Admin — everything.
            'SA' => array_fill_keys($all, 'yes'),

            // Admin — everything.
            'AD' => array_fill_keys($all, 'yes'),

            // Head of Projects — operational scope.
            'HP' => array_merge($deny, [
                'dashboard'              => 'yes',
                'sr_registration'        => 'yes',
                'sr_explorer'            => 'yes',
                'kanban_view'            => 'yes',
                'client_accounts'        => 'yes',
                'client_directory'       => 'yes',
                'project_site_directory' => 'yes',
                'inquiry_approval'       => 'yes',
                'dispatch_engine'        => 'yes',
                'assigned'               => 'yes',
                'quotation_desk'         => 'view',   // finance owns pricing
                'invoice_panel'          => 'yes',
                'invoice_hop_approve'    => 'yes',
                'qc_review'              => 'yes',
                'completed'              => 'view',   // closed work is a record
                'expense_ledger'         => 'view',
                'analytics'              => 'rls',
                'wa_notification_log'    => 'view',   // log, never edited
                'rework_sr'              => 'yes',
                'master_data'            => 'view',   // admin owns config
                'user_directory'         => 'view',
                'user_provisioning'      => 'view',
                'client_delete'          => 'yes',
                'user_delete'            => 'yes',
            ]),

            // Service Engineer — own tickets only.
            'SE' => array_merge($deny, [
                'dashboard'              => 'yes',
                'sr_explorer'            => 'view_rls',  // own SRs, lookup only
                'kanban_view'            => 'view_rls',  // own board, read only
                'client_directory'       => 'view',      // reference data
                'project_site_directory' => 'view',      // reference data
                'dispatch_engine'        => 'rls',       // accepts own jobs
                'assigned'               => 'rls',       // works own tickets
                'qc_review'              => 'rls',
                'completed'              => 'rls',
                'wa_notification_log'    => 'view_rls',  // own message history
            ]),

            // Maintenance Lead — own pipeline only.
            'ML' => array_merge($deny, [
                'dashboard'   => 'yes',
                'kanban_view' => 'rls',
                'assigned'    => 'rls',
            ]),

            // Front Desk Executive — intake + onboarding; several grantable extensions.
            'FD' => array_merge($deny, [
                'dashboard'         => 'yes',
                'sr_registration'   => 'yes',
                'sr_explorer'            => 'view_rls',
                'kanban_view'            => 'view_rls',
                'client_accounts'        => 'yes',       // onboarding is their job
                'client_directory'       => 'yes',
                'project_site_directory' => 'yes',
                'assigned'               => 'rls',
                'inquiry_approval'       => 'grant',
                'dispatch_engine'        => 'grant',
                'qc_review'              => 'grant',
                'expense_ledger'         => 'grant',
                'analytics'              => 'grant',
                'user_provisioning'      => 'grant',
                'wa_notification_log'    => 'view_rls',
            ]),

            // Accounts / AR — out-of-warranty financial flows only.
            'AC' => array_merge($deny, [
                'dashboard'         => 'yes',
                'kanban_view'     => 'view_rls',
                'sr_explorer'     => 'view_rls',
                'quotation_desk' => 'grant',
                'invoice_panel'  => 'grant',
                'expense_ledger' => 'yes',
            ]),
        ];

        // ── 3. Cross-role permission extensions ───────────────────────
        // Format: target role => [ permission_key => source role the permission
        // conceptually belongs to ]. These render as a separate, labeled block
        // in the matrix ("Head of Projects permissions") with individual
        // check/uncheck toggles — distinct from the role's own base access.
        $extensions = [
            'FD' => [
                'inquiry_approval'  => 'HP',
                'dispatch_engine'   => 'HP',
                'qc_review'         => 'HP',
                'expense_ledger'    => 'HP',
                'analytics'         => 'HP',
                'user_provisioning' => 'HP',
            ],
            // add more target-role => [permission => source-role] blocks as needed
        ];

        foreach ($extensions as $targetCode => $perms) {
            $targetRoleId = $roleIds[$targetCode] ?? null;
            if (! $targetRoleId) {
                continue;
            }

            foreach ($perms as $permKey => $sourceCode) {
                $permId       = $permissionIds[$permKey] ?? null;
                $sourceRoleId = $roleIds[$sourceCode] ?? null;

                if (! $permId || ! $sourceRoleId) {
                    continue;
                }

                // Use the source role's own matrix access level as the token
                // applied when this extension is switched on.
                $sourceAccess = $matrix[$sourceCode][$permKey] ?? 'yes';
                $access = match ($sourceAccess) {
                    'grant'    => 'yes',
                    'view'     => 'yes',
                    'view_rls' => 'rls',
                    'edit_own' => 'yes',
                    default    => $sourceAccess,
                };

                DB::table('role_permission_extensions')->updateOrInsert(
                    ['role_id' => $targetRoleId, 'permission_id' => $permId],
                    [
                        'source_role_id' => $sourceRoleId,
                        'access'         => $access,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                );
            }
        }
        // Lookup id maps.
        $roleIds       = DB::table('roles')->pluck('id', 'code');       // ['SA' => 1, ...]
        $permissionIds = DB::table('permissions')->pluck('id', 'key');  // ['dashboard' => 1, ...]

        foreach ($matrix as $roleCode => $perms) {
            $roleId = $roleIds[$roleCode] ?? null;
            if (! $roleId) {
                continue; // role not seeded — skip
            }

            foreach ($perms as $permKey => $token) {
                $permId = $permissionIds[$permKey] ?? null;
                if (! $permId) {
                    continue;
                }

                // Fail loudly on a typo rather than writing a bad row.
                if (! in_array($token, self::TOKENS, true)) {
                    throw new InvalidArgumentException(
                        "Unknown access token '{$token}' for {$roleCode}.{$permKey}"
                    );
                }

                $canGrant     = $token === 'grant';
                $isReadonly   = str_starts_with($token, 'view');
                $writeOwnOnly = $token === 'edit_own';

                $access = match ($token) {
                    'grant'    => 'no',
                    'view'     => 'yes',
                    'view_rls' => 'rls',
                    'edit_own' => 'yes',
                    default    => $token,   // yes | no | rls
                };

                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permId],
                    [
                        'access'         => $access,
                        'can_grant'      => $canGrant,
                        'is_readonly'    => $isReadonly,
                        'write_own_only' => $writeOwnOnly,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                );
            }
        }
    }
}