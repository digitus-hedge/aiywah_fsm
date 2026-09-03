<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     *
     * Values mirror the ROLE_META object used on the User Provisioning page
     * so the front-end and database stay in sync.
     */
    public function run(): void
    {
        $roles = [
            [
                'code'         => 'SA',
                'name'         => 'Super Admin',
                'tagline'      => 'Unrestricted access to all portal modules and system configuration.',
                'icon'         => 'bi-shield-fill-check',
                'color'        => '#7A6140',
                'bg'           => 'rgba(122,97,64,.1)',
                'is_grantable' => false,
                'sort_order'   => 1,
            ],
            [
                'code'         => 'AD',
                'name'         => 'Admin',
                'tagline'      => 'Full operational control - includes all Head of Projects access plus user and system management.',
                'icon'         => 'bi-person-gear',
                'color'        => '#9A7B4F',
                'bg'           => 'rgba(154,123,79,.1)',
                'is_grantable' => false,
                'sort_order'   => 2,
            ],
            [
                'code'         => 'HP',
                'name'         => 'Head of Projects',
                'tagline'      => 'Manages approvals, dispatch, QC, and team-level reporting.',
                'icon'         => 'bi-briefcase-fill',
                'color'        => '#8A6E47',
                'bg'           => 'rgba(138,110,71,.1)',
                'is_grantable' => false,
                'sort_order'   => 3,
            ],
            [
                'code'         => 'ML',
                'name'         => 'Maintenance Lead',
                'tagline'      => 'Field execution only - own pipeline, punch in/out, and expense logging.',
                'icon'         => 'bi-tools',
                'color'        => '#10b981',
                'bg'           => 'rgba(16,185,129,.1)',
                'is_grantable' => false,
                'sort_order'   => 4,
            ],

       
            [
                'code'         => 'FD',
                'name'         => 'Front Desk Executive',
                'tagline'      => 'Handles intake and client onboarding. Admin can optionally extend selected permissions.',
                'icon'         => 'bi-person-badge-fill',
                'color'        => '#f97316',
                'bg'           => 'rgba(249,115,22,.1)',
                'is_grantable' => true,
                'sort_order'   => 5,
            ],
            [
                'code'         => 'AC',
                'name'         => 'Accounts / AR',
                'tagline'      => 'Restricted to out-of-warranty financial flows - quotations, invoices, and expense reconciliation.',
                'icon'         => 'bi-calculator-fill',
                'color'        => '#a8802a',
                'bg'           => 'rgba(251,188,6,.1)',
                'is_grantable' => false,
                'sort_order'   => 6,
            ],


                 [
    'code'         => 'SE',
    'name'         => 'Service Engineer',
    'tagline'      => 'Field execution only - own pipeline, punch in/out, and expense logging.',
    'icon'         => 'bi-wrench-adjustable',
    'color'        => '#2563eb',
    'bg'           => 'rgba(37,99,235,.1)',
    'is_grantable' => true,
    'sort_order'   => 7,
],

        ];

        foreach ($roles as $role) {
            // Idempotent - safe to re-run without duplicating rows.
            DB::table('roles')->updateOrInsert(
                ['code' => $role['code']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}