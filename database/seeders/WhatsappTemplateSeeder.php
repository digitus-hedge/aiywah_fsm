<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappTemplate;

class WhatsappTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'template_name' => 'Inquiry Logged',
                'trigger_event' => 'Inquiry Logged',
                'description'   => 'SR status → Pending',
                'body'          => "Hello {{CLIENT_NAME}}, your service request *{{SR_ID}}* has been logged successfully.\n\nIssue: {{ISSUE_DESCRIPTION}}\nLogged by: {{LOGGED_BY}}\nDate: {{DATE_TIME}}\n\nOur team will review and respond shortly.",
                'variables'     => ['{{CLIENT_NAME}}','{{SR_ID}}','{{ISSUE_DESCRIPTION}}','{{LOGGED_BY}}','{{DATE_TIME}}','{{SITE_LOCATION}}'],
                'status'        => 1,
            ],
            [
                'template_name' => 'ETA Confirmed',
                'trigger_event' => 'ETA Confirmed',
                'description'   => 'Maintenance Lead accepts job and sets attendance time',
                'body'          => "Dear {{CLIENT_NAME}}, your technician has been assigned for SR *{{SR_ID}}*.\n\nTechnician: {{TECHNICIAN_NAME}}\nExpected Arrival: {{ETA_DATE}} at {{ETA_TIME}}\nSite: {{SITE_LOCATION}}\n\nTrack progress: {{TRACKING_LINK}}",
                'variables'     => ['{{CLIENT_NAME}}','{{SR_ID}}','{{TECHNICIAN_NAME}}','{{ETA_DATE}}','{{ETA_TIME}}','{{SITE_LOCATION}}','{{TRACKING_LINK}}'],
                'status'        => 1,
            ],
            [
                'template_name' => 'Punch In — Work Started',
                'trigger_event' => 'Punch In — Work Started',
                'description'   => 'Maintenance Lead executes Punch In on-site',
                'body'          => "Update for SR *{{SR_ID}}*: Work has commenced on-site.\n\nTechnician: {{TECHNICIAN_NAME}}\nCheck-in Time: {{PUNCH_IN_TIME}}\nLocation: {{LOCATION_LINK}}\n\nWe will notify you once work is completed.",
                'variables'     => ['{{SR_ID}}','{{TECHNICIAN_NAME}}','{{PUNCH_IN_TIME}}','{{LOCATION_LINK}}'],
                'status'        => 1,
            ],
            [
                'template_name' => 'SR Completed',
                'trigger_event' => 'SR Completed',
                'description'   => 'QC passed — In-Warranty close or OoW invoice uploaded',
                'body'          => "Dear {{CLIENT_NAME}}, your service request *{{SR_ID}}* has been completed successfully.\n\nTechnician: {{TECHNICIAN_NAME}}\nCompleted On: {{COMPLETION_DATE}}\nDuration On-Site: {{DURATION}}\n\nWork Summary: {{SUMMARY_LINK}}\n\nThank you for choosing Mattermind.",
                'variables'     => ['{{CLIENT_NAME}}','{{SR_ID}}','{{TECHNICIAN_NAME}}','{{COMPLETION_DATE}}','{{DURATION}}','{{SUMMARY_LINK}}'],
                'status'        => 1,
            ],
            [
                'template_name' => 'SR Cancelled / Rejected',
                'trigger_event' => 'SR Cancelled / Rejected',
                'description'   => 'HoP rejects SR with mandatory reason entered',
                'body'          => "Dear {{CLIENT_NAME}}, your service request *{{SR_ID}}* could not be processed.\n\nReason: {{REJECTION_REASON}}\nReviewed by: {{REVIEWED_BY}}\n\nPlease contact our team for further assistance.",
                'variables'     => ['{{CLIENT_NAME}}','{{SR_ID}}','{{REJECTION_REASON}}','{{REVIEWED_BY}}'],
                'status'        => 1,
            ],
            [
                'template_name' => 'Invoice Finalized',
                'trigger_event' => 'Invoice Finalized',
                'description'   => 'Accounts uploads invoice — OoW SR moves to Completed',
                'body'          => "Dear {{CLIENT_NAME}}, the invoice for SR *{{SR_ID}}* has been finalized.\n\nInvoice Ref: {{INVOICE_REF}}\nAmount: {{INVOICE_AMOUNT}}\nDocument: {{INVOICE_LINK}}\n\nPlease arrange payment at your earliest convenience.",
                'variables'     => ['{{CLIENT_NAME}}','{{SR_ID}}','{{INVOICE_REF}}','{{INVOICE_AMOUNT}}','{{INVOICE_LINK}}'],
                'status'        => 1,
            ],
            [
                'template_name' => 'Feedback Request',
                'trigger_event' => 'Feedback Request',
                'description'   => 'Post-completion — collect client satisfaction rating',
                'body'          => "Dear {{CLIENT_NAME}}, thank you for using Mattermind services for SR *{{SR_ID}}*.\n\nWe'd love your feedback on technician {{TECHNICIAN_NAME}}:\n\n{{FEEDBACK_LINK}}\n\nYour response helps us improve our service quality.",
                'variables'     => ['{{CLIENT_NAME}}','{{SR_ID}}','{{TECHNICIAN_NAME}}','{{FEEDBACK_LINK}}'],
                'status'        => 1,
            ],
        ];

        foreach ($templates as $t) {
            // updateOrCreate keyed on template_name → safe to re-run, never duplicates
            WhatsappTemplate::updateOrCreate(
                ['template_name' => $t['template_name']],
                $t
            );
        }
    }
}