<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the 13 sidebar pages as permissions, then attach per-role access
     * levels via the permission_role pivot.
     *
     * Access values: 'yes' = full, 'no' = none, 'rls' = filtered/partial.
     * 'grant' (in the matrix below) means access is 'no' by default but an
     * Admin may extend it — stored as access='no', can_grant=true.
     */
    public function run(): void
    {
        // ── 1. The 13 sidebar pages ──────────────────────────────────
        // [key, name, section, icon, route, sort_order]
        $permissions = [
            // Main
            ['dashboard',         'Dashboard',              'Main',     'layout',       'dashboard',              1],
            ['sr_registration',   'SR Registration',        'Main',     'file-plus',    'sr_registration',        2],
            ['sr_explorer',       'SR Explorer',            'Main',     'badge-pill',   'sr_explorer',            3],
            ['kanban_view',       'Ticket Summary',         'Main',     'clipboard',    'kanban_view',            4],

             // Client Management
            ['client_accounts',   'Client Accounts',        'Client Management', 'users',        'clients.create', 5],
            ['client_directory',  'Client Directory',        'Client Management', 'bi bi-buildings',        'clients.directory',6],
            
            // Workflow
            ['inquiry_approval',  'Inquiry Approval',       'Workflow', 'clipboard',    'inquiry-approval.index', 7],
            ['client_accounts',   'Client Accounts',        'Workflow', 'users',        'clients.create',         8],
            ['dispatch_engine',   'Dispatch Engine',        'Workflow', 'user-check',   'dispatch_engine',        9],
            ['assigned',          'Approved SR',            'Workflow', 'clipboard',    'assigned',               10],
            ['qc_review',         'QC Review',              'Workflow', 'check-circle', null,                     11],
            ['completed',         'Completed SR',           'Workflow', 'clipboard',    'completed',              12],

            // Finance
            ['quotation_desk',    'Quotation Desk',         'Finance',  'file-text',    null,                     13],
            ['invoice_panel',     'Invoice Panel',          'Finance',  'file',         null,                     14],
            ['expense_ledger',    'Expense Ledger',         'Finance',  'check-square', null,                    15],

            // System
            ['analytics',         'Analytics',              'System',   'bar-chart-2',  null,                    16],
            ['user_directory',    'User Directory',         'System',   'database',     'user_directory',        17],
            ['user_provisioning', 'User Provisioning',      'System',   'shield',       'user_provisioning',     18],
            ['master_data',       'Master Data',            'System',   'database',     'masters.index',         19],
            ['wa_notification_log','WhatsApp Notifications','System',   'settings',     'wa_notification_log',   20],
            ['activity-log',     'Activity Log',          'System',     'activity',       'activity-log',        21],
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

        // ── 2. Access matrix: role code → (permission key → access) ──
        // Values: 'yes' | 'no' | 'rls' | 'grant'
        //   'grant' = default no, but Admin can extend (can_grant=true).
        // Anything omitted defaults to 'no'.
        $matrix = [
            // Super Admin — everything.
            'SA' => [
                'dashboard' => 'yes', 'sr_registration' => 'yes', 'sr_explorer' => 'yes', 'kanban_view' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes','client_directory' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes','assigned' => 'yes','completed' => 'yes',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'yes','user_directory' => 'yes', 'user_provisioning' => 'yes','master_data' => 'yes', 'wa_notification_log' => 'yes','activity-log' => 'yes',
            ],
            // Admin — everything except master System Config.
            'AD' => [
                'dashboard' => 'yes' ,'sr_registration' => 'yes', 'sr_explorer' => 'yes', 'kanban_view' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes','client_directory' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes','assigned' => 'yes','completed' => 'yes',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'yes','user_directory' => 'yes','user_provisioning' => 'yes','master_data' => 'yes', 'wa_notification_log' => 'yes','activity-log' => 'yes',
            ],
            // Head of Projects — operational scope, filtered analytics, no finance panels / no user mgmt.
            'HP' => [
                'dashboard' => 'yes', 'kanban_view' => 'yes', 'sr_registration' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes',
                'quotation_desk' => 'no', 'invoice_panel' => 'no', 'expense_ledger' => 'yes',
                'analytics' => 'rls', 'user_provisioning' => 'no', 'activity-log' => 'no',
            ],
            // Maintenance Lead — own pipeline only (kanban view of assigned work).
            'ML' => [
                'dashboard' => 'yes', 'kanban_view' => 'rls', 'sr_registration' => 'no',
                'inquiry_approval' => 'no', 'client_accounts' => 'no', 'dispatch_engine' => 'no', 'qc_review' => 'no',
                'quotation_desk' => 'no', 'invoice_panel' => 'no', 'expense_ledger' => 'no',
                'analytics' => 'no', 'user_provisioning' => 'no', 'activity-log' => 'no',
            ],
            // Front Desk Executive — intake + client onboarding base; several grantable extensions.
            'FD' => [
                'dashboard' => 'yes', 'kanban_view' => 'rls', 'sr_registration' => 'yes',
                'inquiry_approval' => 'grant', 'client_accounts' => 'yes', 'dispatch_engine' => 'grant', 'qc_review' => 'grant',
                'quotation_desk' => 'no', 'invoice_panel' => 'no', 'expense_ledger' => 'grant',
                'analytics' => 'grant', 'user_provisioning' => 'grant', 'activity-log' => 'no',
            ],
            // Accounts / AR — out-of-warranty financial flows only, filtered ticket view.
            'AC' => [
                'dashboard' => 'yes', 'kanban_view' => 'rls', 'sr_registration' => 'no',
                'inquiry_approval' => 'no', 'client_accounts' => 'no', 'dispatch_engine' => 'no', 'qc_review' => 'no',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'no', 'user_provisioning' => 'no', 'activity-log' => 'no',
            ],

             'SE' => [
                'dashboard' => 'yes' ,'sr_registration' => 'yes', 'sr_explorer' => 'yes', 'kanban_view' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes','client_directory' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes','assigned' => 'yes','completed' => 'yes',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'yes','user_directory' => 'yes','user_provisioning' => 'yes','master_data' => 'yes', 'wa_notification_log' => 'yes','activity-log' => 'yes',
            ],

        ];

        // Lookup id maps.
        $roleIds       = DB::table('roles')->pluck('id', 'code');       // ['SA' => 1, ...]
        $permissionIds = DB::table('permissions')->pluck('id', 'key');  // ['dashboard' => 1, ...]

        foreach ($matrix as $roleCode => $perms) {
            $roleId = $roleIds[$roleCode] ?? null;
            if (! $roleId) {
                continue; // role not seeded — skip
            }

            foreach ($perms as $permKey => $value) {
                $permId = $permissionIds[$permKey] ?? null;
                if (! $permId) {
                    continue;
                }

                // Translate 'grant' → access 'no' + can_grant true.
                $canGrant = $value === 'grant';
                $access   = $canGrant ? 'no' : $value; // yes | no | rls

                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permId],
                    [
                        'access'     => $access,
                        'can_grant'  => $canGrant,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}