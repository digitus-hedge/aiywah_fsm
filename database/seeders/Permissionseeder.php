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
            ['kanban_view',       'Ticket Summary (Kanban)','Main',     'clipboard',    'kanban_view',            2],
            ['sr_registration',   'SR Registration',        'Main',     'file-plus',    'sr_registration',        3],

            // Workflow
            ['inquiry_approval',  'Inquiry Approval',       'Workflow', 'clipboard',    'inquiry-approval.index', 4],
            ['client_accounts',   'Client Accounts',        'Workflow', 'users',        'clients.create',         5],
            ['dispatch_engine',   'Dispatch Engine',        'Workflow', 'user-check',   'dispatch_engine',        6],
            ['qc_review',         'QC Review',              'Workflow', 'check-circle', null,                     7],

            // Finance
            ['quotation_desk',    'Quotation Desk',         'Finance',  'file-text',    null,                     8],
            ['invoice_panel',     'Invoice Panel',          'Finance',  'file',         null,                     9],
            ['expense_ledger',    'Expense Ledger',         'Finance',  'check-square', null,                    10],

            // System
            ['analytics',         'Analytics',              'System',   'bar-chart-2',  null,                    11],
            ['user_provisioning', 'User Provisioning',      'System',   'shield',       'user_provisioning',     12],
            ['system_config',     'System Config',          'System',   'settings',     null,                    13],
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
                'dashboard' => 'yes', 'kanban_view' => 'yes', 'sr_registration' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'yes', 'user_provisioning' => 'yes', 'system_config' => 'yes',
            ],
            // Admin — everything except master System Config.
            'AD' => [
                'dashboard' => 'yes', 'kanban_view' => 'yes', 'sr_registration' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'yes', 'user_provisioning' => 'yes', 'system_config' => 'no',
            ],
            // Head of Projects — operational scope, filtered analytics, no finance panels / no user mgmt.
            'HP' => [
                'dashboard' => 'yes', 'kanban_view' => 'yes', 'sr_registration' => 'yes',
                'inquiry_approval' => 'yes', 'client_accounts' => 'yes', 'dispatch_engine' => 'yes', 'qc_review' => 'yes',
                'quotation_desk' => 'no', 'invoice_panel' => 'no', 'expense_ledger' => 'yes',
                'analytics' => 'rls', 'user_provisioning' => 'no', 'system_config' => 'no',
            ],
            // Maintenance Lead — own pipeline only (kanban view of assigned work).
            'ML' => [
                'dashboard' => 'yes', 'kanban_view' => 'rls', 'sr_registration' => 'no',
                'inquiry_approval' => 'no', 'client_accounts' => 'no', 'dispatch_engine' => 'no', 'qc_review' => 'no',
                'quotation_desk' => 'no', 'invoice_panel' => 'no', 'expense_ledger' => 'no',
                'analytics' => 'no', 'user_provisioning' => 'no', 'system_config' => 'no',
            ],
            // Front Desk Executive — intake + client onboarding base; several grantable extensions.
            'FD' => [
                'dashboard' => 'yes', 'kanban_view' => 'rls', 'sr_registration' => 'yes',
                'inquiry_approval' => 'grant', 'client_accounts' => 'yes', 'dispatch_engine' => 'grant', 'qc_review' => 'grant',
                'quotation_desk' => 'no', 'invoice_panel' => 'no', 'expense_ledger' => 'grant',
                'analytics' => 'grant', 'user_provisioning' => 'grant', 'system_config' => 'no',
            ],
            // Accounts / AR — out-of-warranty financial flows only, filtered ticket view.
            'AC' => [
                'dashboard' => 'yes', 'kanban_view' => 'rls', 'sr_registration' => 'no',
                'inquiry_approval' => 'no', 'client_accounts' => 'no', 'dispatch_engine' => 'no', 'qc_review' => 'no',
                'quotation_desk' => 'yes', 'invoice_panel' => 'yes', 'expense_ledger' => 'yes',
                'analytics' => 'no', 'user_provisioning' => 'no', 'system_config' => 'no',
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