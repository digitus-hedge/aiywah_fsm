<?php

namespace App\Services;

use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Support\PortalLink;
use App\Models\ServiceRequest;
class WhatsAppService
{
    /* =========================================================
       Low-level transport
       ========================================================= */

    private function endpoint(): string
    {
        return "https://graph.facebook.com/"
            . config('services.whatsapp.version') . "/"
            . config('services.whatsapp.phone_number_id')
            . "/messages";
    }

public function sendTemplate($phone, $template, $lang = 'en_US', $components = [])
{
    $payload = [
        "messaging_product" => "whatsapp",
        "to"                => $phone,
        "type"              => "template",
        "template"          => [
            "name"     => $template,
            "language" => ["code" => $lang],
        ],
    ];

    if (!empty($components)) {
        $payload["template"]["components"] = $components;
    }

    Log::info('WhatsApp sending', [
        'to'       => $phone,
        'template' => $template,
        'lang'     => $lang,
    ]);

    $response = Http::withToken(config('services.whatsapp.token'))
        ->timeout(15)
        ->post($this->endpoint(), $payload);

    Log::info('WhatsApp response', [
        'to'     => $phone,
        'http'   => $response->status(),
        'body'   => $response->json() ?? $response->body(),
    ]);

    return $response->json();
}

    /** "971" + "0501234567" → "971501234567" (trunk zero stripped). */
    public function formatWhatsAppNumber(?string $country, ?string $mobile): ?string
    {
        if (!$mobile) {
            return null;
        }

        $country = preg_replace('/\D/', '', (string) $country);
        $mobile  = ltrim(preg_replace('/\D/', '', $mobile), '0');

        $full = $country . $mobile;

        return strlen($full) >= 10 ? $full : null;
    }

    public function buildRef(\App\Models\ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }

    /** Collapse whitespace — Meta rejects params containing newlines or tabs. */
    private function cleanParam($value): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    private function txt($value): array
    {
        return ["type" => "text", "text" => (string) $value];
    }

    /* =========================================================
       Recipients
       ========================================================= */

    /**
     * Primary contact plus every notify=1 stakeholder, deduped by phone.
     * Returns [['name' => ..., 'phone' => ...], ...]
     */
    private function recipients($client): array
    {
        $out  = [];
        $seen = [];

        $primary = $this->formatWhatsAppNumber(
            $client->primary_country ?? null,
            $client->primary_mobile ?? null
        );

        if ($primary) {
            $out[]  = ['name' => $client->contact_name ?: 'Customer', 'phone' => $primary];
            $seen[] = $primary;
        }

        if ($client && method_exists($client, 'mobiles')) {
            foreach ($client->mobiles()->where('notify', 1)->get() as $m) {
                $phone = $this->formatWhatsAppNumber($m->country, $m->mobile);
                if (!$phone || in_array($phone, $seen, true)) {
                    continue;
                }
                $seen[] = $phone;
                $out[]  = [
                    'name'  => $m->name ?: ($client->contact_name ?: 'Customer'),
                    'phone' => $phone,
                ];
            }
        }

        return $out;
    }

    /* =========================================================
       Shared context builders
       ========================================================= */

    /** Fields shared by every SR-based template. */
    private function srContext(\App\Models\ServiceRequest $sr): array
    {
        $sr->loadMissing('project');
        $project = $sr->project;

        return [
            'ref'      => $this->buildRef($sr),
            'project'  => $this->cleanParam(optional($project)->project_name) ?: 'N/A',
            'location' => $this->cleanParam($sr->project_site ?: optional($project)->site_name) ?: 'N/A',
            'handover' => $this->fmtDate(optional($project)->completion_date),
            'expiry'   => $this->fmtDate(optional($project)->warranty_end_date),
            'issue'    => Str::limit($this->cleanParam($sr->issue_description), 400) ?: 'N/A',
        ];
    }

    private function techContext(\App\Models\ServiceRequest $sr): array
    {
        $sr->loadMissing('assignedUser');
        $tech = $sr->assignedUser;

        return [
            'name'  => $this->cleanParam(optional($tech)->name) ?: 'To be confirmed',
            'phone' => $this->cleanParam(
                optional($tech)->phone
                    ?? optional($tech)->mobile
                    ?? optional($tech)->contact_number
            ) ?: 'N/A',
        ];
    }

    private function fmtDate($d, string $fallback = 'N/A'): string
    {
        return $d ? Carbon::parse($d)->format('d M Y') : $fallback;
    }

    /** Human-readable warranty scope for the quote_pending_accounts template. */
    private function warrantyScopeLabel(\App\Models\ServiceRequest $sr): string
    {
        if (!empty($sr->warranty_scope)) {
            return $sr->warranty_scope === 'oow' ? 'Warranty Expired' : 'Additional Work';
        }

        return 'N/A';
    }
    /* =========================================================
       Core logged sender
       ========================================================= */

    public function sendLogged(
        ?\App\Models\ServiceRequest $sr,
        $client,
        string $phone,
        string $event,
        string $template,
        array $components,
        string $preview,
        string $lang = 'en_US',
        ?string $refOverride = null
    ): ?WhatsappLog {
        $log = null;

        try {
            $log = WhatsappLog::create([
                'service_request_id' => $sr?->id,
                'client_id'          => $client->id ?? null,
                'sr_reference'       => $sr ? $this->buildRef($sr) : $refOverride,
                'recipient'          => $phone,
                'client_name'        => $client->contact_name ?? null,
                'event'              => $event,
                'status'             => WhatsappLog::STATUS_PENDING,
                'template'           => $template,
                'message'            => $preview,
                'payload'            => $components,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp log row create failed', ['error' => $e->getMessage()]);
        }

        try {
            $result = $this->sendTemplate($phone, $template, $lang, $components);
            $wamid  = $result['messages'][0]['id'] ?? null;

            $log?->update([
                'status'   => $wamid ? WhatsappLog::STATUS_SENT : WhatsappLog::STATUS_FAILED,
                'wamid'    => $wamid,
                'response' => $result,
                'error'    => $result['error']['message'] ?? null,
                'sent_at'  => $wamid ? now() : null,
            ]);

            if (!$wamid) {
                Log::warning('WhatsApp NOT accepted by Meta', [
                    'log_id'   => $log?->id,
                    'phone'    => $phone,
                    'template' => $template,
                    'error'    => $result['error'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            $log?->update([
                'status' => WhatsappLog::STATUS_FAILED,
                'error'  => $e->getMessage(),
            ]);
            Log::error('WhatsApp send failed', ['log_id' => $log?->id, 'error' => $e->getMessage()]);
        }

        return $log?->fresh();
    }

    /**
     * Send one template to every notifiable contact of the client.
     * $componentsFor and $previewFor each receive the recipient name.
     */
    private function fanOut(
        ?\App\Models\ServiceRequest $sr,
        $client,
        string $event,
        string $template,
        callable $componentsFor,
        callable $previewFor,
        ?string $refOverride = null
    ): void {
        if (!$client) {
            Log::warning('WhatsApp skipped — no client', ['sr_id' => $sr?->id]);
            return;
        }

        $recipients = $this->recipients($client);

        if (!$recipients) {
            Log::warning('WhatsApp skipped — no usable phone', [
                'sr_id'     => $sr?->id,
                'client_id' => $client->id ?? null,
            ]);
            return;
        }

        foreach ($recipients as $r) {
            $this->sendLogged(
                $sr, $client, $r['phone'], $event, $template,
                $componentsFor($r['name']),
                $previewFor($r['name']),
                'en_US',
                $refOverride
            );
        }
    }

    /* =========================================================
       SR lifecycle templates
       ========================================================= */

    /**
     * SR received → service_request_received
     * {{1}} name, {{2}} SR ref, {{3}} project, {{4}} location
     */
    public function notifyServiceRequestReceived(
        \App\Models\ServiceRequest $sr,
        string $event = 'SR Received'
    ): void {
        $c = $this->srContext($sr);

        $this->fanOut($sr, $sr->client, $event, 'service_request_received',
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['ref']),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                ],
            ]],
            fn ($name) =>
                "Hi {$name}, thank you for contacting Matter Mind Decor & General Maintenance LLC. "
                . "We have successfully received your maintenance request. "
                . "Service Request No: {$c['ref']} | Project: {$c['project']} | Location: {$c['location']}."
        );
    }

    /**
     * Generic status update → status_change
     * {{1}} name, {{2}} SR ref, {{3}} status
     */
    public function notifyServiceStatus(
        \App\Models\ServiceRequest $sr,
        string $status,
        string $event = 'SR Status Update'
    ): void {
        $ref    = $this->buildRef($sr);
        $status = $this->cleanParam($status);

        $this->fanOut($sr, $sr->client, $event, 'status_change',
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($ref),
                    $this->txt($status),
                ],
            ]],
            fn ($name) => "Hi {$name}, your request {$ref} status: {$status}."
        );
    }

    /**
 * Internal status-change alert → internal_status_change
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} customer,
 * {{5}} status, {{6}} updated by, {{7}} date & time
 */
public function notifyInternalStatusChange(
    \App\Models\ServiceRequest $sr,
    string $status,
    ?string $updatedBy = null,
    string $event = 'Internal - Status Update'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal status-change alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $sr->loadMissing('client');
    $c = $this->srContext($sr);

    $customer  = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $statusLbl = $this->cleanParam($status) ?: 'Updated';
    $by        = $this->cleanParam($updatedBy) ?: 'System';
    $when      = now()->format('d M Y, h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($customer),
            $this->txt($statusLbl),
            $this->txt($by),
            $this->txt($when),
        ],
    ]];

    $preview = "Status update — {$c['ref']} | {$customer} | {$statusLbl} | by {$by}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'internal_status_change', $components, $preview
        );
    }
}
    /**
     * Shared builder for the 7-var warranty-scope templates.
     * {{1}} name, {{2}} project, {{3}} location, {{4}} handover,
     * {{5}} warranty expiry, {{6}} SR ref, {{7}} issue
     */
    private function sendWarrantyScopeMessage(
        \App\Models\ServiceRequest $sr,
        string $template,
        string $event,
        callable $previewFor
    ): void {
        $c = $this->srContext($sr);

        $this->fanOut($sr, $sr->client, $event, $template,
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['handover']),
                    $this->txt($c['expiry']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                ],
            ]],
            fn ($name) => $previewFor($name, $c)
        );
    }

    /**
     * In-warranty approval → template: warranty_approved
     * {{1}} name, {{2}} project, {{3}} location, {{4}} handover date,
     * {{5}} warranty expiry, {{6}} SR ref, {{7}} issue
     */
    public function notifyWarrantyApproved(
        \App\Models\ServiceRequest $sr,
        string $event = 'Warranty Approved'
    ): void {
        $c = $this->srContext($sr);

        $this->fanOut($sr, $sr->client, $event, 'warranty_approved',
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['handover']),
                    $this->txt($c['expiry']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                ],
            ]],
            fn ($name) =>
                "Hi {$name}, we have reviewed your maintenance request and it has been approved under the project warranty. "
                . "Project: {$c['project']} | Location: {$c['location']} | Handover: {$c['handover']} | "
                . "Warranty Expiry: {$c['expiry']} | Request: {$c['ref']} | Issue: {$c['issue']}. "
                . "Our service team will schedule the maintenance visit shortly. "
                . "No charges will apply for the approved work."
        );
    }

    /** Out-of-warranty → outside_warranty_quotation */
    public function notifyOutsideWarranty(
        \App\Models\ServiceRequest $sr,
        string $event = 'Outside Warranty — Quotation'
    ): void {
        $this->sendWarrantyScopeMessage($sr, 'outside_warranty_quotation', $event,
            fn ($name, $c) =>
                "Hi {$name}, request {$c['ref']} falls outside the project warranty period. "
                . "Project: {$c['project']} | Location: {$c['location']} | Handover: {$c['handover']} | "
                . "Warranty Expiry: {$c['expiry']} | Issue: {$c['issue']}. "
                . "Forwarded to our Estimation & Finance Team for quotation."
        );
    }

    /** In-warranty project, out-of-scope issue → outside_warranty_scope */
public function notifyOutsideScope(
    \App\Models\ServiceRequest $sr,
    string $event = 'Outside Warranty Scope'
): void {
    $this->sendWarrantyScopeMessage($sr, 'outside_warranty_scope', $event,
        fn ($name, $c) =>
            "Hi {$name}, request {$c['ref']} is within the warranty period but falls outside the agreed warranty scope. "
            . "Project: {$c['project']} | Location: {$c['location']} | Handover: {$c['handover']} | "
            . "Warranty Expiry: {$c['expiry']} | Issue: {$c['issue']}. "
            . "Forwarded to our Estimation & Finance Team for quotation."
    );
}
    /**
     * Quotation approved by client → quotation_approved
     * Header: document (the approved quote PDF)
     * {{1}} name, {{2}} project, {{3}} location, {{4}} handover, {{5}} SR ref, {{6}} issue
     */
    public function notifyQuotationApproved(
        \App\Models\ServiceRequest $sr,
        string $event = 'Quotation Approved'
    ): void {
        if (!$sr->quote_path) {
            Log::warning('Quotation-approved skipped — no quote_path, falling back', ['sr_id' => $sr->id]);
            $this->notifyServiceStatus($sr, 'Pending Invoice');
            return;
        }

        $c       = $this->srContext($sr);
        $docLink = asset('storage/' . $sr->quote_path);

        $this->fanOut($sr, $sr->client, $event, 'quotation_approved',
            fn ($name) => [
                [
                    "type" => "header",
                    "parameters" => [[
                        "type"     => "document",
                        "document" => [
                            "link"     => $docLink,
                            "filename" => 'Quotation-' . $c['ref'] . '.pdf',
                        ],
                    ]],
                ],
                [
                    "type" => "body",
                    "parameters" => [
                        $this->txt($this->cleanParam($name) ?: 'Customer'),
                        $this->txt($c['project']),
                        $this->txt($c['location']),
                        $this->txt($c['handover']),
                        $this->txt($c['ref']),
                        $this->txt($c['issue']),
                    ],
                ],
            ],
            fn ($name) =>
                "Hi {$name}, thank you for approving the quotation for request {$c['ref']}. "
                . "Project: {$c['project']} | Location: {$c['location']} | Issue: {$c['issue']}. "
                . "Our Service Coordination Team will contact you shortly to schedule the visit."
        );
    }

    /**
     * Technician assigned → technician_assigned
     * {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref, {{5}} issue,
     * {{6}} technician, {{7}} tech phone, {{8}} visit date, {{9}} visit time
     */
    public function notifyTechnicianAssigned(
        \App\Models\ServiceRequest $sr,
        string $event = 'Technician Assigned'
    ): void {
        $c    = $this->srContext($sr);
        $tech = $this->techContext($sr);

        $eta       = $sr->eta_at ? Carbon::parse($sr->eta_at) : null;
        $visitDate = $eta ? $eta->format('d M Y') : 'To be confirmed';
        $visitTime = $eta ? $eta->format('h:i A') : 'To be confirmed';

        $this->fanOut($sr, $sr->client, $event, 'technician_assigned',
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                    $this->txt($tech['name']),
                    $this->txt($tech['phone']),
                    $this->txt($visitDate),
                    $this->txt($visitTime),
                ],
            ]],
            fn ($name) =>
                "Hi {$name}, a technician has been assigned to request {$c['ref']}. "
                . "Project: {$c['project']} | Location: {$c['location']} | Issue: {$c['issue']}. "
                . "Technician: {$tech['name']} ({$tech['phone']}) — visiting {$visitDate} at {$visitTime}."
        );
    }

    /**
     * Work started on site → maintenance_started
     * {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref, {{5}} issue,
     * {{6}} technician, {{7}} tech phone, {{8}} arrival time
     */
    public function notifyMaintenanceStarted(
        \App\Models\ServiceRequest $sr,
        ?\App\Models\Punch $punch = null,
        string $event = 'Maintenance Started'
    ): void {
        $punch ??= $sr->punches()->whereNotNull('punch_in_at')->latest('punch_in_at')->first();

        $arrival = $punch?->punch_in_at
            ? Carbon::parse($punch->punch_in_at)->format('d M Y, h:i A')
            : now()->format('d M Y, h:i A');

        $c    = $this->srContext($sr);
        $tech = $this->techContext($sr);

        $this->fanOut($sr, $sr->client, $event, 'maintenance_started',
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                    $this->txt($tech['name']),
                    $this->txt($tech['phone']),
                    $this->txt($arrival),
                ],
            ]],
            fn ($name) =>
                "Hi {$name}, our technician has arrived and started work on request {$c['ref']}. "
                . "Technician: {$tech['name']} ({$tech['phone']}) — arrived {$arrival}."
        );
    }

    /**
     * Visit incomplete → maintenance_on_hold
     * {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref, {{5}} issue,
     * {{6}} technician, {{7}} status, {{8}} reason
     */
    public function notifyMaintenanceOnHold(
        \App\Models\ServiceRequest $sr,
        string $status,
        string $reason,
        string $event = 'Maintenance On Hold'
    ): void {
        $c    = $this->srContext($sr);
        $tech = $this->techContext($sr);

        $statusLabel = $this->cleanParam($status) ?: 'On Hold';
        $reasonText  = Str::limit($this->cleanParam($reason), 400) ?: 'N/A';

        $this->fanOut($sr, $sr->client, $event, 'maintenance_on_hold',
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                    $this->txt($tech['name']),
                    $this->txt($statusLabel),
                    $this->txt($reasonText),
                ],
            ]],
            fn ($name) =>
                "Hi {$name}, our technician attended request {$c['ref']} but could not complete the work. "
                . "Status: {$statusLabel} | Reason: {$reasonText}."
        );
    }

    /**
 * Work completed & QC passed → maintenance_completed
 * {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref, {{5}} issue,
 * {{6}} technician, {{7}} completion date, {{8}} completion time
 * Button (index 0, url): the template's registered URL is a static base
 * (https://maintenance.mattermind.ae/portal/project/) plus {{1}}. Meta appends
 * whatever we send here directly onto that base, so we must send the FULL
 * remainder — code, query string, signature — not just the trailing segment,
 * or the signed portal link loses its signature and 404s / fails validation.
 */
public function notifyMaintenanceCompleted(
    \App\Models\ServiceRequest $sr,
    ?\App\Models\Punch $punch = null,
    ?string $photosLink = null,
    string $event = 'Maintenance Completed'
): void {
    $punch ??= $sr->punches()->whereNotNull('punch_out_at')->latest('punch_out_at')->first();

    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $out  = $punch?->punch_out_at ? Carbon::parse($punch->punch_out_at) : now();
    $link = $photosLink ?: url("/sr/{$sr->id}/photos");

    // in notifyMaintenanceCompleted()
    $buttonValue = $this->buttonSuffix($link, config('app.url') . '/portal/project/');
    
    $this->fanOut($sr, $sr->client, $event, 'maintenance_completed',
        fn ($name) => [
            [
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                    $this->txt($tech['name']),
                    $this->txt($out->format('d M Y')),
                    $this->txt($out->format('h:i A')),
                ],
            ],
            [
                "type"     => "button",
                "sub_type" => "url",
                "index"    => "0",
                "parameters" => [
                    ["type" => "text", "text" => $buttonValue],
                ],
            ],
        ],
        fn ($name) =>
            "Hi {$name}, your maintenance request {$c['ref']} has been successfully completed. "
            . "Technician: {$tech['name']} — completed {$out->format('d M Y')} at {$out->format('h:i A')}. "
            . "Photos: {$link}"
    );
}

    /**
     * Strip a template's static base URL from a full link, leaving exactly
     * what Meta needs for a dynamic {{1}} button parameter — code, query
     * string, signature all preserved. Falls back to the full link if the
     * prefix doesn't match, so a differently-shaped fallback URL still sends
     * something usable rather than nothing.
     */
    private function buttonSuffix(string $link, string $prefix): string
    {
        return str_starts_with($link, $prefix)
            ? substr($link, strlen($prefix))
            : $link;
    }

    /**
 * Post-completion survey → satisfaction_survey
 * {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref, {{5}} completion date
 * Button (index 0, url): dynamic suffix appended to the template's static
 * base (https://portal.mattermind.ae/client_feedback/) — send ONLY the
 * feedback id, not the full link, or the button URL doubles up and 404s.
 */
    public function notifySatisfactionSurvey(
    \App\Models\ServiceRequest $sr,
    ?string $surveyLink = null,
    string $event = 'Satisfaction Survey'
): void {
    $c = $this->srContext($sr);

    $punch = $sr->punches()->whereNotNull('punch_out_at')->latest('punch_out_at')->first();
    $completed = $this->fmtDate($punch?->punch_out_at ?? $sr->qc_reviewed_at);

    $link = $surveyLink ?: \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'clients.feedback.show',
        now()->addDays(30),
        ['id' => $sr->id]
    );

    // Template's registered base: https://maintenance.mattermind.ae/client_feedback/
    $buttonValue = $this->buttonSuffix($link, 'https://maintenance.mattermind.ae/client_feedback/');

    $this->fanOut($sr, $sr->client, $event, 'satisfaction_survey',
        fn ($name) => [
            [
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['ref']),
                    $this->txt($completed),
                ],
            ],
            [
                "type"     => "button",
                "sub_type" => "url",
                "index"    => "0",
                "parameters" => [
                    ["type" => "text", "text" => $buttonValue],
                ],
            ],
        ],
        fn ($name) =>
            "Hi {$name}, we hope everything is working well after the maintenance visit. "
            . "Request: {$c['ref']} | Completed: {$completed}. "
            . "Please rate your experience: {$link}"
    );
}

    /* =========================================================
       Client / project templates (no ServiceRequest)
       ========================================================= */

    /**
     * Shared builder for client_welcome and project_added.
     * {{1}} name, {{2}} project, {{3}} location, {{4}} handover, {{5}} warranty expiry
     */
    private function sendProjectMessage(
        \App\Models\Client $client,
        \App\Models\Project $project,
        string $template,
        string $event,
        callable $previewFor,
        string $refPrefix
    ): void {
        $c = [
            'project'  => $this->cleanParam($project->project_name) ?: 'N/A',
            'location' => $this->cleanParam($project->site_name) ?: 'N/A',
            'handover' => $this->fmtDate($project->completion_date, 'To be confirmed'),
            'expiry'   => $this->fmtDate($project->warranty_end_date, 'To be confirmed'),
        ];

        $this->fanOut(null, $client, $event, $template,
            fn ($name) => [[
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($name) ?: 'Customer'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['handover']),
                    $this->txt($c['expiry']),
                ],
            ]],
            fn ($name) => $previewFor($name, $c),
            $refPrefix . '-' . $project->id
        );
    }

    /** First project onboarded → client_welcome */
    public function notifyClientWelcome(
        \App\Models\Client $client,
        \App\Models\Project $project,
        string $event = 'Client Welcome'
    ): void {
        $this->sendProjectMessage($client, $project, 'client_welcome', $event,
            fn ($name, $c) =>
                "Hi {$name}, welcome to Matter Mind's Post-Handover Maintenance Portal. "
                . "Project: {$c['project']} | Location: {$c['location']} | "
                . "Handover: {$c['handover']} | Warranty Expiry: {$c['expiry']}.",
            'WELCOME'
        );
    }

    /** Additional project on an existing account → project_added */
    public function notifyProjectAdded(
        \App\Models\Client $client,
        \App\Models\Project $project,
        string $event = 'Project Added'
    ): void {
        $this->sendProjectMessage($client, $project, 'project_added', $event,
            fn ($name, $c) =>
                "Hi {$name}, your new project has been added to the Matter Mind Post-Handover Maintenance Portal. "
                . "Project: {$c['project']} | Location: {$c['location']} | "
                . "Handover: {$c['handover']} | Warranty Expiry: {$c['expiry']}.",
            'PROJECT'
        );
    }

    /* =========================================================
       Retry
       ========================================================= */

    public function retryLog(WhatsappLog $log): WhatsappLog
    {
        try {
            $result = $this->sendTemplate(
                $log->recipient,
                $log->template,
                'en_US',
                $log->payload ?? []
            );

            $wamid = $result['messages'][0]['id'] ?? null;

            $log->update([
                'status'      => $wamid ? WhatsappLog::STATUS_SENT : WhatsappLog::STATUS_FAILED,
                'wamid'       => $wamid,
                'response'    => $result,
                'error'       => $result['error']['message'] ?? null,
                'sent_at'     => $wamid ? now() : null,
                'retry_count' => $log->retry_count + 1,
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'status'      => WhatsappLog::STATUS_FAILED,
                'error'       => $e->getMessage(),
                'retry_count' => $log->retry_count + 1,
            ]);
        }

        return $log->fresh();
    }

 /* =========================================================
       internal messages
       ========================================================= */
    /**
 * Internal recipients for an SR: the creator, all Super Admins and
 * Heads of Projects, plus the project engineer stored on the project.
 * Deduped by phone so nobody gets it twice.
 */
private function internalRecipients(\App\Models\ServiceRequest $sr): array
{
    $sr->loadMissing(['creator', 'project', 'assignedSe']);
    $out  = [];
    $seen = [];

    $add = function ($name, $country, $mobile) use (&$out, &$seen) {
        $phone = $this->formatWhatsAppNumber($country, $mobile);
        if (!$phone || in_array($phone, $seen, true)) {
            return;
        }
        $seen[] = $phone;
        $out[]  = ['name' => $this->cleanParam($name) ?: 'Team', 'phone' => $phone];
    };

    // 1. Whoever raised the SR
    if ($sr->creator) {
        $add($sr->creator->name, $sr->creator->country_code, $sr->creator->phone);
    }

    // 2 + 3. Super Admins and Heads of Projects
    $codes = config('services.whatsapp.internal_role_codes', ['SA', 'HP']);

    $staff = \App\Models\User::whereHas('role', fn ($q) => $q->whereIn('code', $codes))
        ->get(['id', 'name', 'country_code', 'phone']);

    foreach ($staff as $u) {
        $add($u->name, $u->country_code, $u->phone);
    }
    // 4. Project engineer — stored on the project row, not a user account
    if ($sr->project?->engineer_contact) {
        $add(
            $sr->project->project_engineer ?: 'Project Engineer',
            $sr->project->engineer_country,
            $sr->project->engineer_contact
        );
    }

    // 5. Service Engineer allocated at approval
    if ($sr->assignedSe) {
        $add($sr->assignedSe->name, $sr->assignedSe->country_code, $sr->assignedSe->phone);
    }

    return $out;
}

/**
 * Accounts / quotation-handling recipients — distinct from internalRecipients
 * (SA/HP/PE/SE), since quote prep is a separate role, not a general FYI list.
 */
private function accountsRecipients(): array
{
    $out  = [];
    $seen = [];

    $codes = config('services.whatsapp.accounts_role_codes', ['AC']);

    $staff = \App\Models\User::whereHas('role', fn ($q) => $q->whereIn('code', $codes))
        ->get(['id', 'name', 'country_code', 'phone']);

    foreach ($staff as $u) {
        $phone = $this->formatWhatsAppNumber($u->country_code, $u->phone);
        if (!$phone || in_array($phone, $seen, true)) {
            continue;
        }
        $seen[] = $phone;
        $out[]  = ['name' => $this->cleanParam($u->name) ?: 'Accounts', 'phone' => $phone];
    }

    return $out;
}

 /**
 * Shared builder for the 8-var internal SR alerts.
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} customer,
 * {{5}} created by, {{6}} issue, {{7}} priority, {{8}} date & time
 */
private function sendInternalSrAlert(
    \App\Models\ServiceRequest $sr,
    string $template,
    string $event,
    string $headline
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal alert skipped — no recipients resolved', [
            'sr_id'    => $sr->id,
            'template' => $template,
        ]);
        return;
    }

    $sr->loadMissing('client');
    $c = $this->srContext($sr);

    $customer  = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $createdBy = $this->cleanParam(optional($sr->creator)->name ?: $sr->reported_by) ?: 'N/A';
    $priority  = $this->cleanParam($sr->priority_level) ?: 'Normal';
    $when      = ($sr->created_at ?? now())->format('d M Y, h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($customer),
            $this->txt($createdBy),
            $this->txt($c['issue']),
            $this->txt($priority),
            $this->txt($when),
        ],
    ]];

    $preview = "{$headline} {$c['ref']} — {$customer} | {$c['project']} | Priority: {$priority} | {$when}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            $template, $components, $preview
        );
    }
}

/** New SR raised → internal_new_service_request */
public function notifyInternalNewRequest(
    \App\Models\ServiceRequest $sr,
    string $event = 'Internal - New SR'
): void {
    $this->sendInternalSrAlert($sr, 'internal_new_service_request', $event, 'New SR');
}

/** SR accepted by management → internal_sr_accepted */
public function notifyInternalSrAccepted(
    \App\Models\ServiceRequest $sr,
    string $event = 'Internal - SR Accepted'
): void {
    $this->sendInternalSrAlert($sr, 'internal_sr_accepted', $event, 'Accepted');
}

/** SR falls outside warranty → internal_outside_warranty */
public function notifyInternalOutsideWarranty(
    \App\Models\ServiceRequest $sr,
    string $event = 'Internal - Outside Warranty'
): void {
    $this->sendInternalSrAlert($sr, 'internal_outside_warranty', $event, 'Outside warranty');
}

/**
 * Technician assigned → internal_technician_assigned
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} customer,
 * {{5}} created by, {{6}} issue, {{7}} priority, {{8}} date & time,
 * {{9}} technician, {{10}} technician phone
 */
public function notifyInternalTechnicianAssigned(
    \App\Models\ServiceRequest $sr,
    string $event = 'Internal - Technician Assigned'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal tech-assigned alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $sr->loadMissing('client');
    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $customer  = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $createdBy = $this->cleanParam(optional($sr->creator)->name ?: $sr->reported_by) ?: 'N/A';
    $priority  = $this->cleanParam($sr->priority_level) ?: 'Normal';
    $when      = ($sr->created_at ?? now())->format('d M Y, h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($customer),
            $this->txt($createdBy),
            $this->txt($c['issue']),
            $this->txt($priority),
            $this->txt($when),
            $this->txt($tech['name']),
            $this->txt($tech['phone']),
        ],
    ]];

    $preview = "Technician assigned — {$c['ref']} | {$customer} | "
        . "{$tech['name']} ({$tech['phone']})";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'internal_technician_assigned', $components, $preview
        );
    }
}

/**
 * Technician punched in → internal_maintenance_started
 * {{1}} project, {{2}} location, {{3}} SR ref,
 * {{4}} technician, {{5}} arrival time
 */
public function notifyInternalMaintenanceStarted(
    \App\Models\ServiceRequest $sr,
    ?\App\Models\Punch $punch = null,
    string $event = 'Internal - Maintenance Started'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal started alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $punch ??= $sr->punches()->whereNotNull('punch_in_at')->latest('punch_in_at')->first();

    $arrival = $punch?->punch_in_at
        ? Carbon::parse($punch->punch_in_at)->format('d M Y, h:i A')
        : now()->format('d M Y, h:i A');

    $sr->loadMissing('client');
    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($tech['name']),
            $this->txt($arrival),
        ],
    ]];

    $preview = "Work started — {$c['ref']} | {$tech['name']} | arrived {$arrival}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'internal_maintenance_started', $components, $preview
        );
    }
}

/**
 * Visit incomplete → internal_maintenance_on_hold
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} status,
 * {{5}} reason, {{6}} next visit date, {{7}} next visit time
 */
public function notifyInternalMaintenanceOnHold(
    \App\Models\ServiceRequest $sr,
    string $status,
    string $reason,
    string $event = 'Internal - Maintenance On Hold'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal on-hold alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $sr->loadMissing('client');
    $c = $this->srContext($sr);

    $statusLabel = $this->cleanParam($status) ?: 'On Hold';
    $reasonText  = Str::limit($this->cleanParam($reason), 400) ?: 'N/A';

    $eta       = $sr->eta_at ? Carbon::parse($sr->eta_at) : null;
    $visitDate = $eta ? $eta->format('d M Y') : 'To be confirmed';
    $visitTime = $eta ? $eta->format('h:i A') : 'To be confirmed';

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($statusLabel),
            $this->txt($reasonText),
            $this->txt($visitDate),
            $this->txt($visitTime),
        ],
    ]];

    $preview = "On hold — {$c['ref']} | {$statusLabel} | {$reasonText} | next: {$visitDate} {$visitTime}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'internal_maintenance_on_hold', $components, $preview
        );
    }
}

/**
 * QC passed → sent to internal staff using the customer-facing
 * maintenance_completed template (no internal-specific template exists).
 * {{1}} recipient name, {{2}} project, {{3}} location, {{4}} SR ref,
 * {{5}} issue, {{6}} technician, {{7}} completed date, {{8}} completed time
 * Button (index 0, url): dynamic suffix only — same rule as above.
 */
public function notifyInternalMaintenanceCompleted(
    \App\Models\ServiceRequest $sr,
    ?\App\Models\Punch $punch = null,
    ?string $photosLink = null,
    string $event = 'Internal - Maintenance Completed'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal completed alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $punch ??= $sr->punches()->whereNotNull('punch_out_at')->latest('punch_out_at')->first();

    $sr->loadMissing('client');
    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $out  = Carbon::parse($punch?->punch_out_at ?? now());
    $link = $photosLink ?: \Illuminate\Support\Facades\URL::signedRoute(
        'sr.photos', ['serviceRequest' => $sr->id]
    );
    
    $buttonValue = $this->buttonSuffix($link, config('app.url') . '/portal/project/');
    // (removed the stray one-arg buttonSuffix() call that was here — it would
    // have thrown a TypeError, since buttonSuffix() now requires a prefix)

    foreach ($recipients as $r) {
        $components = [
            [
                "type" => "body",
                "parameters" => [
                    $this->txt($this->cleanParam($r['name']) ?: 'Team'),
                    $this->txt($c['project']),
                    $this->txt($c['location']),
                    $this->txt($c['ref']),
                    $this->txt($c['issue']),
                    $this->txt($tech['name']),
                    $this->txt($out->format('d M Y')),
                    $this->txt($out->format('h:i A')),
                ],
            ],
            [
                "type"     => "button",
                "sub_type" => "url",
                "index"    => "0",
                "parameters" => [
                    ["type" => "text", "text" => $buttonValue],
                ],
            ],
        ];

        $preview = "Completed — {$c['ref']} | {$tech['name']} | {$out->format('d M Y, h:i A')}";

        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'maintenance_completed', $components, $preview
        );
    }
}


/**
 * Job assigned to the Maintenance Lead → ml_job_assigned
 * Dedicated ML-facing template — separate from internal_technician_assigned
 * (which goes to SA/HP/PE/SE staff). Sent to the ML alone via sendToOne().
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} customer, {{5}} issue,
 * {{6}} priority, {{7}} assigned on, {{8}} technician name, {{9}} technician phone
 */
public function notifyMlJobAssigned(
    \App\Models\ServiceRequest $sr,
    string $event = 'ML - Job Assigned'
): void {
    $sr->loadMissing(['client', 'creator', 'assignedUser']);

    $ml = $sr->assignedUser;

    if (!$ml) {
        Log::warning('ML job alert skipped — no technician assigned', ['sr_id' => $sr->id]);
        return;
    }

    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $customer = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $priority = $this->cleanParam($sr->priority_level) ?: 'Normal';
    $when     = ($sr->created_at ?? now())->format('d M Y, h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($customer),
            $this->txt($c['issue']),
            $this->txt($priority),
            $this->txt($when),
            $this->txt($tech['name']),
            $this->txt($tech['phone']),
        ],
    ]];

    $preview = "Job assigned — {$c['ref']} | {$customer} | {$c['location']} | "
        . "Priority: {$priority}";

    $this->sendToOne(
        $sr, 'wa.ml_job',
        $ml->name, $ml->country_code, $ml->phone,
        $event, 'ml_job_assigned', $components, $preview
    );
}

/**
 * Survey dispatched to customer → internal_survey_sent
 * {{1}} created by, {{2}} customer, {{3}} project, {{4}} location,
 * {{5}} handover, {{6}} warranty expiry, {{7}} SR ref, {{8}} issue,
 * {{9}} priority, {{10}} sent date, {{11}} sent time
 * No survey link in this template — it's purely an internal FYI, not a
 * click-through. (Previously sent a 12th param for the link with nowhere
 * for Meta to put it — that's what caused every send to fail.)
 */
public function notifyInternalSurveySent(
    \App\Models\ServiceRequest $sr,
    ?string $surveyLink = null,
    string $event = 'Internal - Survey Sent'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Internal survey-sent alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $sr->loadMissing('client');
    $c = $this->srContext($sr);

    $createdBy = $this->cleanParam(optional($sr->creator)->name ?: $sr->reported_by) ?: 'N/A';
    $customer  = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $priority  = $this->cleanParam($sr->priority_level) ?: 'Normal';

    $link = $surveyLink ?: \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'clients.feedback.show',
        now()->addDays(30),
        ['id' => $sr->id]
    );

    $now = now();

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($createdBy),
            $this->txt($customer),
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['handover']),
            $this->txt($c['expiry']),
            $this->txt($c['ref']),
            $this->txt($c['issue']),
            $this->txt($priority),
            $this->txt($now->format('d M Y')),
            $this->txt($now->format('h:i A')),
        ],
    ]];

    // $link is kept only for the preview text (visible in the WhatsApp
    // Notification Log), not sent to Meta — the approved template has no
    // slot for it.
    $preview = "Survey sent — {$c['ref']} | {$customer} | {$now->format('d M Y, h:i A')} | Link: {$link}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'internal_survey_sent', $components, $preview
        );
    }
}

public function notifyServiceCompleted(ServiceRequest $sr): void
{
    $sr->loadMissing(['client', 'project', 'category']);
 
    $to = PortalLink::customerNumber($sr);
 
    if (! $to) {
        \Log::warning('Completion WhatsApp skipped — no contact number', ['sr_id' => $sr->id]);
        return;
    }
 
    $ref  = 'SR-' . $sr->created_at->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    $link = PortalLink::project($sr->project, $sr);
 
    $body = implode("\n", [
        "Hello {$sr->client->contact_name},",
        '',
        "Work on {$ref} at {$sr->project->site_name} is complete.",
        '',
        'You can view the full record — before and after photos, what the '
            . 'technician did, and the progress log — and download it as a PDF here:',
        $link,
        '',
        'The link is private to you. Please keep it if you need the record later.',
    ]);
 
    // ── Swap this line for your existing sender ──
    $this->send($to, $body);
 
    // If you send approved templates instead of free text, the link is the
    // only dynamic part that matters — pass $link as the button URL suffix or
    // as a body variable, depending on how the template is registered.
}

/**
 * Technician accepted the job and set their visit ETA → internal_visit_scheduled
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} technician, {{5}} date, {{6}} time
 */
public function notifyInternalVisitScheduled(
    \App\Models\ServiceRequest $sr,
    string $event = 'Internal - Visit Scheduled'
): void {
    $recipients = $this->internalRecipients($sr);

    if (!$recipients) {
        Log::warning('Visit-scheduled alert skipped — no recipients', ['sr_id' => $sr->id]);
        return;
    }

    $sr->loadMissing('client');
    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $eta       = $sr->eta_at ? Carbon::parse($sr->eta_at) : now();
    $visitDate = $eta->format('d M Y');
    $visitTime = $eta->format('h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($tech['name']),
            $this->txt($visitDate),
            $this->txt($visitTime),
        ],
    ]];

    $preview = "Visit scheduled — {$c['ref']} | {$tech['name']} | {$visitDate} {$visitTime}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'internal_visit_scheduled', $components, $preview
        );
    }
}

/**
 * SR routed to Accounts for quotation → quote_pending_accounts
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} customer,
 * {{5}} SR created by, {{6}} issue, {{7}} priority, {{8}} date & time,
 * {{9}} warranty scope, {{10}} approved by, {{11}} routed on
 */
public function notifyQuotePendingAccounts(
    \App\Models\ServiceRequest $sr,
    ?string $approvedBy = null,
    string $event = 'Quote Pending - Accounts'
): void {
    $recipients = $this->accountsRecipients();

    if (!$recipients) {
        Log::warning('Quote-pending alert skipped — no Accounts recipients resolved', [
            'sr_id' => $sr->id,
        ]);
        return;
    }

    $sr->loadMissing('client', 'creator');
    $c = $this->srContext($sr);

    $customer  = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $createdBy = $this->cleanParam(optional($sr->creator)->name ?: $sr->reported_by) ?: 'N/A';
    $priority  = $this->cleanParam($sr->priority_level) ?: 'Normal';
    $when      = ($sr->created_at ?? now())->format('d M Y, h:i A');
    $scope     = $this->warrantyScopeLabel($sr);
    $approver  = $this->cleanParam($approvedBy) ?: 'N/A';
    $routedOn  = now()->format('d M Y, h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($customer),
            $this->txt($createdBy),
            $this->txt($c['issue']),
            $this->txt($priority),
            $this->txt($when),
            $this->txt($scope),
            $this->txt($approver),
            $this->txt($routedOn),
        ],
    ]];

    $preview = "Quote pending — {$c['ref']} | {$customer} | {$c['project']} | Scope: {$scope}";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'quote_pending_accounts', $components, $preview
        );
    }
}

/**
 * QC-approved SR routed to Accounts for invoicing → invoice_required_accounts
 * {{1}} project, {{2}} location, {{3}} SR ref, {{4}} customer,
 * {{5}} SR created by, {{6}} issue, {{7}} priority, {{8}} date & time,
 * {{9}} ERP quote ref, {{10}} quote value, {{11}} QC approved by,
 * {{12}} QC approved on, {{13}} technician
 */
public function notifyInvoiceRequiredAccounts(
    \App\Models\ServiceRequest $sr,
    ?string $quoteReference = null,
    ?string $qcApprovedBy = null,
    ?\Carbon\Carbon $qcApprovedAt = null,
    string $event = 'Invoice Required - Accounts'
): void {
    $recipients = $this->accountsRecipients();

    if (!$recipients) {
        Log::warning('Invoice-required alert skipped — no Accounts recipients resolved', [
            'sr_id' => $sr->id,
        ]);
        return;
    }

    $sr->loadMissing('client', 'creator');
    $c    = $this->srContext($sr);
    $tech = $this->techContext($sr);

    $customer   = $this->cleanParam(optional($sr->client)->company_name) ?: 'N/A';
    $createdBy  = $this->cleanParam(optional($sr->creator)->name ?: $sr->reported_by) ?: 'N/A';
    $priority   = $this->cleanParam($sr->priority_level) ?: 'Normal';
    $when       = ($sr->created_at ?? now())->format('d M Y, h:i A');

    $quoteRef   = $this->cleanParam($quoteReference ?? $sr->quote_reference) ?: 'N/A';
    $quoteText  = $quoteVal !== null ? 'AED ' . number_format((float) $quoteVal, 2) : 'N/A';

    $qcBy       = $this->cleanParam($qcApprovedBy ?? optional($sr->qcApprovedBy)->name) ?: 'N/A';
    $qcAt       = ($qcApprovedAt ?? ($sr->qc_reviewed_at ? Carbon::parse($sr->qc_reviewed_at) : now()))
                    ->format('d M Y, h:i A');

    $components = [[
        "type" => "body",
        "parameters" => [
            $this->txt($c['project']),
            $this->txt($c['location']),
            $this->txt($c['ref']),
            $this->txt($customer),
            $this->txt($createdBy),
            $this->txt($c['issue']),
            $this->txt($priority),
            $this->txt($when),
            $this->txt($quoteRef),
            $this->txt($quoteText),
            $this->txt($qcBy),
            $this->txt($qcAt),
            $this->txt($tech['name']),
        ],
    ]];

    $preview = "Invoice required — {$c['ref']} | {$customer} | Quote: {$quoteRef} ({$quoteText})";

    foreach ($recipients as $r) {
        $this->sendLogged(
            $sr, $sr->client, $r['phone'], $event,
            'invoice_required_accounts', $components, $preview
        );
    }
}
/**
     * Send one template to a single named person, bypassing the client fan-out
     * and the internal staff list. Use for recipients who need their own
     * wording — a technician getting a job, not a manager getting an alert.
     */
    private function sendToOne(
        ?\App\Models\ServiceRequest $sr,
        string $label,
        ?string $name,
        ?string $country,
        ?string $mobile,
        string $event,
        string $template,
        array $components,
        string $preview
    ): void {
        $phone = $this->formatWhatsAppNumber($country, $mobile);

        if (!$phone) {
            Log::warning("WhatsApp skipped — no usable number [{$label}]", [
                'sr_id'  => $sr?->id,
                'name'   => $name,
                'mobile' => $mobile,
            ]);
            return;
        }

        $this->sendLogged(
            $sr,
            $sr?->client,
            $phone,
            $event,
            $template,
            $components,
            $preview
        );
    }
}