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
     *   'yes'      → access=yes,  can_grant=false, is_readonly=false
     *   'no'       → access=no,   can_grant=false, is_readonly=false
     *   'rls'      → access=rls,  can_grant=false, is_readonly=false
     *   'grant'    → access=no,   can_grant=true,  is_readonly=false
     *   'view'     → access=yes,  can_grant=false, is_readonly=true
     *   'view_rls' → access=rls,  can_grant=false, is_readonly=true
     *
     * `access` answers "which rows?", `is_readonly` answers "may they change
     * them?". The two are independent, which is why 'view_rls' exists.
     */
    private const TOKENS = ['yes', 'no', 'rls', 'grant', 'view', 'view_rls'];

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
            ['user_directory',         'User Directory',         'System',            'users',          'user_directory',        18],
            ['user_provisioning',      'User Provisioning',      'System',            'shield',         'user_provisioning',     19],
            ['master_data',            'Master Data',            'System',            'database',       'masters.index',         20],
            ['wa_notification_log',    'WhatsApp Notifications', 'System',            'message-circle', 'wa_notification_log',   21],
            ['activity-log',           'Activity Log',           'System',            'activity',       'activity-log',          22],
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
                'quotation_desk'         => 'view',
                'invoice_panel'          => 'yes',
                'qc_review'              => 'yes',
                'completed'              => 'view',
                'expense_ledger'         => 'view',
                'analytics'              => 'rls',
                'wa_notification_log'    => 'view',
                'rework_sr'              => 'yes',
                'master_data'            => 'view',
                'user_directory'         => 'view',
                'user_provisioning'      => 'view',
            ]),

            // Service Engineer — own tickets only.
            'SE' => array_merge($deny, [
                'dashboard'        => 'yes',
                'sr_explorer'      => 'view_rls',
                'kanban_view'      => 'view_rls',
                'project_site_directory' => 'view',
                'client_directory' => 'view',
                'dispatch_engine'  => 'rls',
                'assigned'         => 'rls',
                'qc_review'        => 'rls',
                'completed'        => 'rls',
                'wa_notification_log'    => 'view_rls',
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
                'sr_explorer'       => 'view_rls',
                'kanban_view'       => 'view_rls',
                'client_accounts'   => 'yes',
                'client_directory'  => 'yes',
                'project_site_directory' => 'yes',
                'inquiry_approval'  => 'grant',
                'dispatch_engine'   => 'grant',
                'qc_review'         => 'grant',
                'assigned'          => 'rls',
                'expense_ledger'    => 'grant',
                'analytics'         => 'grant',
                'user_provisioning' => 'grant',
                'wa_notification_log'    => 'view_rls',
            ]),

            // Accounts / AR — out-of-warranty financial flows only.
            'AC' => array_merge($deny, [
                'dashboard'      => 'yes',
                'kanban_view'    => 'rls',
                'quotation_desk' => 'grant',
                'invoice_panel'  => 'grant',
                'expense_ledger' => 'yes',
            ]),
        ];

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

                $canGrant   = $token === 'grant';
                $isReadonly = str_starts_with($token, 'view');

                $access = match ($token) {
                    'grant'    => 'no',
                    'view'     => 'yes',
                    'view_rls' => 'rls',
                    default    => $token,   // yes | no | rls
                };

                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permId],
                    [
                        'access'      => $access,
                        'can_grant'   => $canGrant,
                        'is_readonly' => $isReadonly,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]
                );
            }
        }
    }
}  