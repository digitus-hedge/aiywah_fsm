<?php

namespace Database\Seeders;

use App\Models\AlertType;
use Illuminate\Database\Seeder;

/**
 * Master list of Daily Summary alert headings, per role.
 *
 * Admin + HoP headings were pulled directly from admin_daily_summary.html
 * and hop_daily_summary.html (card titles / KPI labels, de-duplicated where
 * a KPI tile and a card open the same detail sheet).
 *
 * SE + ML headings are the lists you gave for those two roles.
 *
 * All rows are seeded with is_default_checked = true. This table only
 * defines WHAT exists per role - the actual checked/unchecked state per
 * user lives in user_alert_permissions (see UserAlertPermissionSeeder).
 */
class AlertTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [

            AlertType::ROLE_ADMIN => [
                ['key' => 'admin_logged',    'title' => 'SRs Logged',           'description' => 'SRs logged yesterday - split by source/type.'],
                ['key' => 'admin_completed', 'title' => 'Completed',            'description' => 'SRs completed yesterday.'],
                ['key' => 'admin_open',      'title' => 'Open - Carried',       'description' => 'Open SRs by status, carried forward.'],
                ['key' => 'admin_wa',        'title' => 'WA Failures',          'description' => 'WhatsApp notification delivery failures.'],
                ['key' => 'admin_rework',    'title' => 'Return Works',         'description' => 'SRs returned/rejected at QC (return works).'],
                ['key' => 'admin_hours',     'title' => 'Technician Field Hours', 'description' => 'Field hours logged by technicians yesterday.'],
                ['key' => 'admin_expenses',  'title' => 'Expense Submissions',  'description' => 'Expense submissions yesterday - receipts submitted vs pending.'],
                ['key' => 'admin_new',       'title' => 'New Additions',        'description' => 'New clients and new projects added yesterday.'],
                ['key' => 'admin_cancelled', 'title' => 'Cancelled SRs',        'description' => 'SRs cancelled yesterday, with reason.'],
            ],

            AlertType::ROLE_HOP => [
                ['key' => 'hop_pending',   'title' => 'Pending Review',    'description' => 'SRs awaiting HoP triage - action required.'],
                ['key' => 'hop_approved',  'title' => 'Approved',          'description' => 'SRs approved yesterday and moved forward (dispatched / awaiting SE / routed to Accounts).'],
                ['key' => 'hop_qc',        'title' => 'QC Pending',        'description' => 'QC reviews pending / carried forward, including those awaiting HoP where SE QC permission is ON.'],
                ['key' => 'hop_rework',    'title' => 'Rework Cases',      'description' => 'Rework cases from yesterday - QC rejected.'],
                ['key' => 'hop_realloc',   'title' => 'Re-allocations',    'description' => 'Category re-allocations / corrections.'],
                ['key' => 'hop_completed', 'title' => 'Completed',         'description' => 'SRs completed yesterday, with client ratings.'],
            ],

            AlertType::ROLE_SE => [
                ['key' => 'se_assigned',      'title' => 'SRs Assigned by HoP',   'description' => 'SRs assigned to them yesterday by HoP - how many they dispatched vs how many still pending.'],
                ['key' => 'se_rework',        'title' => 'Rework Cases',          'description' => 'Rework cases sitting in their queue from yesterday.'],
                ['key' => 'se_avg_dispatch',  'title' => 'Avg. Dispatch Time',    'description' => 'Average time taken to dispatch yesterday.'],
                ['key' => 'se_in_field',      'title' => 'SRs In Field',          'description' => 'SRs currently in field from their category - live carry-forward count.'],
                ['key' => 'se_qc',            'title' => 'QC Reviews Done',       'description' => 'QC reviews done yesterday (only shown if QC permission is ON) - approved vs rejected.'],
            ],

            AlertType::ROLE_ML => [
                ['key' => 'ml_completed',  'title' => 'Jobs Completed',        'description' => 'Jobs they completed yesterday.'],
                ['key' => 'ml_punch',      'title' => 'Punch In/Out',          'description' => 'Jobs they punched in and out on.'],
                ['key' => 'ml_rework',     'title' => 'Rework',                'description' => 'Any jobs that came back as rework from their previous submissions.'],
                ['key' => 'ml_pending',    'title' => 'Pending Jobs',          'description' => 'Pending jobs assigned to them - what is waiting today.'],
                ['key' => 'ml_expenses',   'title' => 'Expense Submissions',   'description' => 'Expense submissions from yesterday - submitted vs pending receipts.'],
                ['key' => 'ml_ratings',    'title' => 'Client Ratings',        'description' => 'Client ratings received on jobs they closed.'],
            ],

        ];

        foreach ($roles as $role => $items) {
            foreach ($items as $index => $item) {
                AlertType::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'role' => $role,
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'sort_order' => $index + 1,
                        'is_default_checked' => true,
                    ]
                );
            }
        }
    }
}