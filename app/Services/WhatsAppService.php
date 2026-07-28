<?php

namespace App\Services;

use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /* =========================================================
       Low-level transport
       ========================================================= */

    private function endpoint(): string
    {
        return "https://graph.facebook.com/" .
            config('services.whatsapp.version') . "/" .
            config('services.whatsapp.phone_number_id') .
            "/messages";
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

        $response = Http::withToken(config('services.whatsapp.token'))
            ->post($this->endpoint(), $payload);

        return $response->json();
    }

    public function formatWhatsAppNumber(?string $country, ?string $mobile): ?string
    {
        if (!$mobile) {
            return null;
        }

        $country = preg_replace('/\D/', '', (string) $country);
        $mobile  = preg_replace('/\D/', '', $mobile);

        return $country . $mobile;
    }

    public function buildRef(\App\Models\ServiceRequest $sr): string
    {
        return $sr->erp_quote_ref
            ?: 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Return [name => ..., phone => ...] for every secondary contact
     * of the client that has notify = 1 and a usable mobile number.
     * The client's primary contact is handled separately by the caller.
     */
    private function notifiableSecondaryContacts($client): array
    {
        if (!$client || !method_exists($client, 'mobiles')) {
            return [];
        }

        $out = [];

        foreach ($client->mobiles()->where('notify', 1)->get() as $m) {
            $phone = $this->formatWhatsAppNumber($m->country, $m->mobile);
            if (!$phone) {
                continue;
            }
            $out[] = [
                'name'  => $m->name ?: ($client->contact_name ?? ''),
                'phone' => $phone,
            ];
        }

        return $out;
    }

    /**
     * Collapse whitespace — WhatsApp rejects body params containing
     * newlines, tabs or runs of more than one space.
     */
    private function cleanParam($value): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    /**
     * Shared field set for the project/request detail templates
     * (warranty_approved, outside_warranty_quotation, quotation_approved,
     *  sr_creation).
     */
    private function srTemplateContext(\App\Models\ServiceRequest $sr): array
    {
        $sr->loadMissing('project');
        $project = $sr->project;

        $date = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : 'N/A';

        return [
            'ref'      => $this->buildRef($sr),
            'project'  => $this->cleanParam(optional($project)->project_name) ?: 'N/A',
            'location' => $this->cleanParam($sr->project_site ?: optional($project)->site_name) ?: 'N/A',
            'handover' => $date(optional($project)->handover_date),
            'expiry'   => $date(optional($project)->warranty_end_date),
            'issue'    => \Illuminate\Support\Str::limit($this->cleanParam($sr->issue_description), 400) ?: 'N/A',
        ];
    }

    /** Technician name + best-available phone for the assigned user. */
    private function technicianDetails(\App\Models\ServiceRequest $sr): array
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

    /* =========================================================
       Core logged sender — ONE definition only.
       $refOverride lets callers supply an SR reference string
       when they don't have the ServiceRequest model itself.
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

            $wamid = $result['messages'][0]['id'] ?? null;

            $log?->update([
                'status'   => $wamid ? WhatsappLog::STATUS_SENT : WhatsappLog::STATUS_FAILED,
                'wamid'    => $wamid,
                'response' => $result,
                'error'    => $result['error']['message'] ?? null,
                'sent_at'  => $wamid ? now() : null,
            ]);

            Log::info('WhatsApp sent', [
                'log_id' => $log?->id,
                'event'  => $event,
                'phone'  => $phone,
                'wamid'  => $wamid,
            ]);

            // Surface Meta's rejection reason directly when the send didn't return a wamid.
            if (!$wamid) {
                Log::warning('WhatsApp NOT accepted by Meta', [
                    'log_id'   => $log?->id,
                    'phone'    => $phone,
                    'template' => $template,
                    'error'    => $result['error'] ?? null,
                    'response' => $result,
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

    /* =========================================================
       Public senders — prefer these; they populate the log fully.
       ========================================================= */

    /**
     * SR status update → template: status_change
     * Body vars: {{1}} name, {{2}} SR ref, {{3}} status
     * Optional {{4}} link when the template variant carries one.
     */
    public function notifyServiceStatus(
        \App\Models\ServiceRequest $sr,
        string $status,
        string $event = 'SR Status Update',
        ?string $link = null
    ): void {
        $client = $sr->client;
        if (!$client) {
            Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
            return;
        }

        $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
        if (!$phone) {
            Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
            return;
        }

        $ref = $this->buildRef($sr);

        $buildParams = function (string $name) use ($ref, $status, $link) {
            $params = [
                ["type" => "text", "text" => (string) $name],
                ["type" => "text", "text" => (string) $ref],
                ["type" => "text", "text" => (string) $status],
            ];
            if ($link) {
                $params[] = ["type" => "text", "text" => (string) $link];
            }
            return [["type" => "body", "parameters" => $params]];
        };

        $suffix = $link ? " Share your feedback: {$link}" : '';

        $this->sendLogged(
            $sr,
            $client,
            $phone,
            $event,
            'status_change',
            $buildParams($client->contact_name),
            "Hi {$client->contact_name}, your request {$ref} status: {$status}.{$suffix}"
        );

        foreach ($this->notifiableSecondaryContacts($client) as $c) {
            $this->sendLogged(
                $sr,
                $client,
                $c['phone'],
                $event,
                'status_change',
                $buildParams($c['name']),
                "Hi {$c['name']}, request {$ref} status: {$status}.{$suffix}"
            );
        }
    }

    /**
     * SR creation → template: sr_creation
     * Body vars: {{1}} name, {{2}} SR ref, {{3}} project, {{4}} location
     */
    public function notifyServiceCreated(
        \App\Models\ServiceRequest $sr,
        string $status = 'Pending',
        string $event = 'SR Created'
    ): void {
        $client = $sr->client;
        if (!$client) {
            Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
            return;
        }

        $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
        if (!$phone) {
            Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
            return;
        }

        $ctx = $this->srTemplateContext($sr);

        $params = fn (string $name) => [[
            "type"       => "body",
            "parameters" => [
                ["type" => "text", "text" => $this->cleanParam($name)],
                ["type" => "text", "text" => $ctx['ref']],
                ["type" => "text", "text" => $ctx['project']],
                ["type" => "text", "text" => $ctx['location']],
            ],
        ]];

        $preview = fn (string $name) =>
            "Hi {$name}, thank you for contacting Matter Mind Decor & General Maintenance LLC. "
            . "We have successfully received your maintenance request. "
            . "Service Request No: {$ctx['ref']} | Project: {$ctx['project']} | Location: {$ctx['location']}. "
            . "Our service team is reviewing your request and will update you shortly.";

        $primaryName = $client->contact_name ?: 'Customer';

        $this->sendLogged(
            $sr,
            $client,
            $phone,
            $event,
            'sr_creation',
            $params($primaryName),
            $preview($primaryName)
        );

        foreach ($this->notifiableSecondaryContacts($client) as $c) {
            $name = $c['name'] ?: 'Customer';
            $this->sendLogged(
                $sr,
                $client,
                $c['phone'],
                $event,
                'sr_creation',
                $params($name),
                $preview($name)
            );
        }
    }

    /**
     * Shared builder for the 7-var warranty templates.
     * Body vars: {{1}} name, {{2}} project, {{3}} location,
     *            {{4}} handover date, {{5}} warranty expiry,
     *            {{6}} SR ref, {{7}} issue description
     */
    private function sendWarrantyScopeMessage(
        \App\Models\ServiceRequest $sr,
        string $template,
        string $event,
        callable $previewFor
    ): void {
        $client = $sr->client;
        if (!$client) {
            Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
            return;
        }

        $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
        if (!$phone) {
            Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
            return;
        }

        $ctx = $this->srTemplateContext($sr);

        $params = fn (string $name) => [[
            "type"       => "body",
            "parameters" => [
                ["type" => "text", "text" => $this->cleanParam($name)],
                ["type" => "text", "text" => $ctx['project']],
                ["type" => "text", "text" => $ctx['location']],
                ["type" => "text", "text" => $ctx['handover']],
                ["type" => "text", "text" => $ctx['expiry']],
                ["type" => "text", "text" => $ctx['ref']],
                ["type" => "text", "text" => $ctx['issue']],
            ],
        ]];

        $primaryName = $client->contact_name ?: 'Customer';

        $this->sendLogged(
            $sr, $client, $phone, $event, $template,
            $params($primaryName),
            $previewFor($primaryName, $ctx)
        );

        foreach ($this->notifiableSecondaryContacts($client) as $c) {
            $name = $c['name'] ?: 'Customer';
            $this->sendLogged(
                $sr, $client, $c['phone'], $event, $template,
                $params($name),
                $previewFor($name, $ctx)
            );
        }
    }

    /** In-warranty approval → template: warranty_approved */
    public function notifyWarrantyApproved(
        \App\Models\ServiceRequest $sr,
        string $event = 'Warranty Approved'
    ): void {
        $this->sendWarrantyScopeMessage(
            $sr,
            'warranty_approved',
            $event,
            fn (string $name, array $c) =>
                "Hi {$name}, your maintenance request {$c['ref']} has been approved under the project warranty. "
                . "Project: {$c['project']} | Location: {$c['location']} | Handover: {$c['handover']} | "
                . "Warranty Expiry: {$c['expiry']} | Issue: {$c['issue']}. "
                . "No charges will apply for the approved work."
        );
    }

    /** Out-of-warranty → template: outside_warranty_quotation */
    public function notifyOutsideWarranty(
        \App\Models\ServiceRequest $sr,
        string $event = 'Outside Warranty — Quotation'
    ): void {
        $this->sendWarrantyScopeMessage(
            $sr,
            'outside_warranty_quotation',
            $event,
            fn (string $name, array $c) =>
                "Hi {$name}, request {$c['ref']} falls outside the project warranty period. "
                . "Project: {$c['project']} | Location: {$c['location']} | Handover: {$c['handover']} | "
                . "Warranty Expiry: {$c['expiry']} | Issue: {$c['issue']}. "
                . "Forwarded to our Estimation & Finance Team for quotation."
        );
    }

    /**
     * Quotation approved by client → template: quotation_approved
     * Header: document (the approved quote PDF)
     * Body vars: {{1}} name, {{2}} project, {{3}} location,
     *            {{4}} handover date, {{5}} SR ref, {{6}} issue
     */
    public function notifyQuotationApproved(
        \App\Models\ServiceRequest $sr,
        string $event = 'Quotation Approved'
    ): void {
        $client = $sr->client;
        if (!$client) {
            Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
            return;
        }

        $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
        if (!$phone) {
            Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
            return;
        }

        // The template carries a document header — without a PDF Meta rejects the send.
        if (!$sr->quote_path) {
            Log::warning('Quotation-approved WhatsApp skipped — no quote_path, falling back', [
                'sr_id' => $sr->id,
            ]);
            $this->notifyServiceStatus($sr, 'Pending Invoice');
            return;
        }

        $ctx     = $this->srTemplateContext($sr);
        $docLink = asset('storage/' . $sr->quote_path);

        $components = fn (string $name) => [
            [
                "type"       => "header",
                "parameters" => [[
                    "type"     => "document",
                    "document" => [
                        "link"     => $docLink,
                        "filename" => 'Quotation-' . $ctx['ref'] . '.pdf',
                    ],
                ]],
            ],
            [
                "type"       => "body",
                "parameters" => [
                    ["type" => "text", "text" => $this->cleanParam($name)],
                    ["type" => "text", "text" => $ctx['project']],
                    ["type" => "text", "text" => $ctx['location']],
                    ["type" => "text", "text" => $ctx['handover']],
                    ["type" => "text", "text" => $ctx['ref']],
                    ["type" => "text", "text" => $ctx['issue']],
                ],
            ],
        ];

        $preview = fn (string $name) =>
            "Hi {$name}, thank you for approving the quotation for request {$ctx['ref']}. "
            . "Project: {$ctx['project']} | Location: {$ctx['location']} | Handover: {$ctx['handover']} | "
            . "Issue: {$ctx['issue']}. Accepted for maintenance — our Service Coordination Team "
            . "will contact you shortly to schedule the visit.";

        $primaryName = $client->contact_name ?: 'Customer';

        $this->sendLogged(
            $sr, $client, $phone, $event, 'quotation_approved',
            $components($primaryName),
            $preview($primaryName)
        );

        foreach ($this->notifiableSecondaryContacts($client) as $c) {
            $name = $c['name'] ?: 'Customer';
            $this->sendLogged(
                $sr, $client, $c['phone'], $event, 'quotation_approved',
                $components($name),
                $preview($name)
            );
        }
    }

    /** Alias kept for existing call sites. */
    public function notifyServiceStatusLogged(
        \App\Models\ServiceRequest $sr,
        string $status,
        string $event = 'SR Status Update'
    ): void {
        $this->notifyServiceStatus($sr, $status, $event);
    }

    public function sendQuotation(\App\Models\ServiceRequest $sr, string $docLink = ''): void
    {
        $this->notifyServiceStatus($sr, 'Waiting for quotation approval', 'Invoice Finalized');
    }

    /**
     * Registration → template: client_registration
     * Body vars: {{1}} name   (only one variable)
     */
    public function sendRegistration(
        $phone,
        $contactName,
        $token,
        $lang = 'en_US',
        ?\App\Models\ServiceRequest $sr = null,
        $client = null,
        string $status = 'Registered'
    ) {
        $log = $this->sendLogged(
            $sr,
            $client ?? (object) ['contact_name' => $contactName],
            $phone,
            'Client Registration',
            'client_registration',
            [[
                "type"       => "body",
                "parameters" => [
                    ["type" => "text", "text" => (string) $contactName],
                ],
            ]],
            "Registration confirmed for {$contactName}",
            $lang
        );

        // Secondary contacts with notify = 1 (only when a real client model is passed)
        if ($client instanceof \App\Models\Client) {
            foreach ($this->notifiableSecondaryContacts($client) as $c) {
                $this->sendLogged(
                    $sr,
                    $client,
                    $c['phone'],
                    'Client Registration',
                    'client_registration',
                    [[
                        "type"       => "body",
                        "parameters" => [
                            ["type" => "text", "text" => (string) $c['name']],
                        ],
                    ]],
                    "Registration confirmed for {$c['name']}",
                    $lang
                );
            }
        }

        return $log;
    }

    /**
     * Order confirmation test → template: sr_creation
     *
     * NOTE: sr_creation now has FOUR body variables. This method still sends
     * two, so Meta will reject it with a parameter-count mismatch. Point it at
     * its own template or extend the parameter list before using it again.
     */
    public function sendOrderTest(
        $phone,
        $contactName,
        $uniqueCode,
        $delivery,
        $lang = 'en_US',
        ?\App\Models\ServiceRequest $sr = null,
        $client = null
    ) {
        return $this->sendLogged(
            $sr,
            $client ?? (object) ['contact_name' => $contactName],
            $phone,
            'Order Confirmation',
            'sr_creation',
            [[
                "type"       => "body",
                "parameters" => [
                    ["type" => "text", "text" => (string) $contactName],
                    ["type" => "text", "text" => (string) $uniqueCode],
                ],
            ]],
            "Hi {$contactName}, order {$uniqueCode} — {$delivery}",
            $lang,
            $uniqueCode
        );
    }

    /**
     * Legacy signature → template: status_change
     * Body vars: {{1}} name, {{2}} SR ref, {{3}} status
     * Pass $sr and $client from the call site, otherwise
     * service_request_id / client_id will be NULL.
     */
    public function sendServiceRequest(
        $phone,
        $contactName,
        $srReference,
        $status,
        $lang = 'en_US',
        ?\App\Models\ServiceRequest $sr = null,
        $client = null
    ) {
        $log = $this->sendLogged(
            $sr,
            $client ?? (object) ['contact_name' => $contactName],
            $phone,
            'SR Status Update',
            'status_change',
            [[
                "type"       => "body",
                "parameters" => [
                    ["type" => "text", "text" => (string) $contactName],
                    ["type" => "text", "text" => (string) $srReference],
                    ["type" => "text", "text" => (string) $status],
                ],
            ]],
            "Hi {$contactName}, your request {$srReference} status: {$status}",
            $lang,
            $srReference
        );

        // Secondary contacts with notify = 1 (only when a real client model is passed)
        if ($client instanceof \App\Models\Client) {
            foreach ($this->notifiableSecondaryContacts($client) as $c) {
                $this->sendLogged(
                    $sr,
                    $client,
                    $c['phone'],
                    'SR Status Update',
                    'status_change',
                    [[
                        "type"       => "body",
                        "parameters" => [
                            ["type" => "text", "text" => (string) $c['name']],
                            ["type" => "text", "text" => (string) $srReference],
                            ["type" => "text", "text" => (string) $status],
                        ],
                    ]],
                    "Hi {$c['name']}, request {$srReference} status: {$status}",
                    $lang,
                    $srReference
                );
            }
        }

        return $log;
    }

    /** Raw (unlogged) template send with a document header. */
    public function sendDocumentTemplate(
        $phone,
        $template,
        array $bodyParams,
        string $docLink,
        ?string $filename = null,
        $lang = 'en_US'
    ) {
        $components = [
            [
                "type"       => "header",
                "parameters" => [[
                    "type"     => "document",
                    "document" => array_filter([
                        "link"     => $docLink,
                        "filename" => $filename ?? 'quotation.pdf',
                    ]),
                ]],
            ],
            [
                "type"       => "body",
                "parameters" => array_map(
                    fn ($t) => ["type" => "text", "text" => (string) $t],
                    $bodyParams
                ),
            ],
        ];

        return $this->sendTemplate($phone, $template, $lang, $components);
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

    /**
 * Technician assigned & visit scheduled → template: technician_assigned
 * Body vars: {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref,
 *            {{5}} issue, {{6}} technician, {{7}} tech phone,
 *            {{8}} visit date, {{9}} visit time
 */
public function notifyTechnicianAssigned(
    \App\Models\ServiceRequest $sr,
    string $event = 'Technician Assigned'
): void {
    $client = $sr->client;
    if (!$client) {
        Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
        return;
    }

    $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
    if (!$phone) {
        Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
        return;
    }

    $tech = $this->technicianDetails($sr);

    // eta_at drives the schedule; fall back gracefully if it wasn't captured.
    $eta = $sr->eta_at ? \Carbon\Carbon::parse($sr->eta_at) : null;
    $visitDate = $eta ? $eta->format('d M Y') : 'To be confirmed';
    $visitTime = $eta ? $eta->format('h:i A') : 'To be confirmed';

    $params = fn (string $name) => [[
        "type"       => "body",
        "parameters" => [
            ["type" => "text", "text" => $this->cleanParam($name)],
            ["type" => "text", "text" => $ctx['project']],
            ["type" => "text", "text" => $ctx['location']],
            ["type" => "text", "text" => $ctx['ref']],
            ["type" => "text", "text" => $ctx['issue']],
            ["type" => "text", "text" => $techName],
            ["type" => "text", "text" => $techPhone],
            ["type" => "text", "text" => $visitDate],
            ["type" => "text", "text" => $visitTime],
        ],
    ]];

    $preview = fn (string $name) =>
        "Hi {$name}, a technician has been assigned to request {$ctx['ref']}. "
        . "Project: {$ctx['project']} | Location: {$ctx['location']} | Issue: {$ctx['issue']}. "
        . "Technician: {$techName} ({$techPhone}) — visiting {$visitDate} at {$visitTime}.";

    $primaryName = $client->contact_name ?: 'Customer';

    $this->sendLogged(
        $sr, $client, $phone, $event, 'technician_assigned',
        $params($primaryName),
        $preview($primaryName)
    );

    foreach ($this->notifiableSecondaryContacts($client) as $c) {
        $name = $c['name'] ?: 'Customer';
        $this->sendLogged(
            $sr, $client, $c['phone'], $event, 'technician_assigned',
            $params($name),
            $preview($name)
        );
    }
}

/**
 * Technician on site, work started → template: maintenance_started
 * Body vars: {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref,
 *            {{5}} issue, {{6}} technician, {{7}} tech phone, {{8}} arrival time
 */
public function notifyMaintenanceStarted(
    \App\Models\ServiceRequest $sr,
    ?\App\Models\Punch $punch = null,
    string $event = 'Maintenance Started'
): void {
    $client = $sr->client;
    if (!$client) {
        Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
        return;
    }

    $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
    if (!$phone) {
        Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
        return;
    }

    // Prefer the punch handed in by the caller; otherwise take the latest open one.
    $punch ??= $sr->punches()
        ->whereNotNull('punch_in_at')
        ->latest('punch_in_at')
        ->first();

    $arrival = $punch?->punch_in_at
        ? \Carbon\Carbon::parse($punch->punch_in_at)->format('d M Y, h:i A')
        : now()->format('d M Y, h:i A');

    $ctx  = $this->srTemplateContext($sr);
    $tech = $this->technicianDetails($sr);

    $params = fn (string $name) => [[
        "type"       => "body",
        "parameters" => [
            ["type" => "text", "text" => $this->cleanParam($name)],
            ["type" => "text", "text" => $ctx['project']],
            ["type" => "text", "text" => $ctx['location']],
            ["type" => "text", "text" => $ctx['ref']],
            ["type" => "text", "text" => $ctx['issue']],
            ["type" => "text", "text" => $tech['name']],
            ["type" => "text", "text" => $tech['phone']],
            ["type" => "text", "text" => $arrival],
        ],
    ]];

    $preview = fn (string $name) =>
        "Hi {$name}, our technician has arrived and started work on request {$ctx['ref']}. "
        . "Project: {$ctx['project']} | Location: {$ctx['location']} | Issue: {$ctx['issue']}. "
        . "Technician: {$tech['name']} ({$tech['phone']}) — arrived {$arrival}.";

    $primaryName = $client->contact_name ?: 'Customer';

    $this->sendLogged(
        $sr, $client, $phone, $event, 'maintenance_started',
        $params($primaryName),
        $preview($primaryName)
    );

    foreach ($this->notifiableSecondaryContacts($client) as $c) {
        $name = $c['name'] ?: 'Customer';
        $this->sendLogged(
            $sr, $client, $c['phone'], $event, 'maintenance_started',
            $params($name),
            $preview($name)
        );
    }
}

/**
 * Visit incomplete → template: maintenance_on_hold
 * Body vars: {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref,
 *            {{5}} issue, {{6}} technician, {{7}} status, {{8}} reason
 */
public function notifyMaintenanceOnHold(
    \App\Models\ServiceRequest $sr,
    string $status,
    string $reason,
    string $event = 'Maintenance On Hold'
): void {
    $client = $sr->client;
    if (!$client) {
        Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
        return;
    }

    $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
    if (!$phone) {
        Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
        return;
    }

    $ctx  = $this->srTemplateContext($sr);
    $tech = $this->technicianDetails($sr);

    $statusLabel = $this->cleanParam($status) ?: 'On Hold';
    $reasonText  = \Illuminate\Support\Str::limit($this->cleanParam($reason), 400) ?: 'N/A';

    $params = fn (string $name) => [[
        "type"       => "body",
        "parameters" => [
            ["type" => "text", "text" => $this->cleanParam($name)],
            ["type" => "text", "text" => $ctx['project']],
            ["type" => "text", "text" => $ctx['location']],
            ["type" => "text", "text" => $ctx['ref']],
            ["type" => "text", "text" => $ctx['issue']],
            ["type" => "text", "text" => $tech['name']],
            ["type" => "text", "text" => $statusLabel],
            ["type" => "text", "text" => $reasonText],
        ],
    ]];

    $preview = fn (string $name) =>
        "Hi {$name}, our technician attended request {$ctx['ref']} but could not complete the work. "
        . "Project: {$ctx['project']} | Location: {$ctx['location']} | Issue: {$ctx['issue']}. "
        . "Technician: {$tech['name']} | Status: {$statusLabel} | Reason: {$reasonText}. "
        . "Our Service Team is arranging the next steps.";

    $primaryName = $client->contact_name ?: 'Customer';

    $this->sendLogged(
        $sr, $client, $phone, $event, 'maintenance_on_hold',
        $params($primaryName),
        $preview($primaryName)
    );

    foreach ($this->notifiableSecondaryContacts($client) as $c) {
        $name = $c['name'] ?: 'Customer';
        $this->sendLogged(
            $sr, $client, $c['phone'], $event, 'maintenance_on_hold',
            $params($name),
            $preview($name)
        );
    }
}

/**
 * Work completed & QC passed → template: maintenance_completed
 * Header: document (signed Work Completion Report)
 * Body vars: {{1}} name, {{2}} project, {{3}} location, {{4}} SR ref,
 *            {{5}} issue, {{6}} technician, {{7}} completion date,
 *            {{8}} completion time, {{9}} photos link
 */
public function notifyMaintenanceCompleted(
    \App\Models\ServiceRequest $sr,
    ?\App\Models\Punch $punch = null,
    ?string $photosLink = null,
    string $event = 'Maintenance Completed'
): void {
    $client = $sr->client;
    if (!$client) {
        Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
        return;
    }

    $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
    if (!$phone) {
        Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
        return;
    }

    $punch ??= $sr->punches()
        ->whereNotNull('punch_out_at')
        ->latest('punch_out_at')
        ->first();

    // The template has a document header — no signed report means no valid send.
    if (!$punch?->customer_signature_path) {
        Log::warning('Completion WhatsApp skipped — no signed report, falling back', [
            'sr_id' => $sr->id,
        ]);
        $this->notifyServiceStatus($sr, 'Completed');
        return;
    }

    $ctx  = $this->srTemplateContext($sr);
    $tech = $this->technicianDetails($sr);

    $out  = \Carbon\Carbon::parse($punch->punch_out_at);
    $link = $photosLink ?: url("/sr/{$sr->id}/photos");

    $components = fn (string $name) => [
        [
            "type"       => "header",
            "parameters" => [[
                "type"     => "document",
                "document" => [
                    "link"     => asset('storage/' . $punch->customer_signature_path),
                    "filename" => 'Work-Completion-Report-' . $ctx['ref'] . '.pdf',
                ],
            ]],
        ],
        [
            "type"       => "body",
            "parameters" => [
                ["type" => "text", "text" => $this->cleanParam($name)],
                ["type" => "text", "text" => $ctx['project']],
                ["type" => "text", "text" => $ctx['location']],
                ["type" => "text", "text" => $ctx['ref']],
                ["type" => "text", "text" => $ctx['issue']],
                ["type" => "text", "text" => $tech['name']],
                ["type" => "text", "text" => $out->format('d M Y')],
                ["type" => "text", "text" => $out->format('h:i A')],
                ["type" => "text", "text" => $link],
            ],
        ],
    ];

    $preview = fn (string $name) =>
        "Hi {$name}, your maintenance request {$ctx['ref']} has been successfully completed. "
        . "Project: {$ctx['project']} | Location: {$ctx['location']} | Issue: {$ctx['issue']}. "
        . "Technician: {$tech['name']} — completed {$out->format('d M Y')} at {$out->format('h:i A')}. "
        . "Signed report attached; photos: {$link}";

    $primaryName = $client->contact_name ?: 'Customer';

    $this->sendLogged(
        $sr, $client, $phone, $event, 'maintenance_completed',
        $components($primaryName),
        $preview($primaryName)
    );

    foreach ($this->notifiableSecondaryContacts($client) as $c) {
        $name = $c['name'] ?: 'Customer';
        $this->sendLogged(
            $sr, $client, $c['phone'], $event, 'maintenance_completed',
            $components($name),
            $preview($name)
        );
    }
}

/**
 * Post-completion survey → template: satisfaction_survey
 * Body vars: {{1}} name, {{2}} project, {{3}} location,
 *            {{4}} SR ref, {{5}} completion date, {{6}} survey link
 */
public function notifySatisfactionSurvey(
    \App\Models\ServiceRequest $sr,
    ?string $surveyLink = null,
    string $event = 'Satisfaction Survey'
): void {
    $client = $sr->client;
    if (!$client) {
        Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
        return;
    }

    $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
    if (!$phone) {
        Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
        return;
    }

    $ctx = $this->srTemplateContext($sr);

    $punch = $sr->punches()
        ->whereNotNull('punch_out_at')
        ->latest('punch_out_at')
        ->first();

    $completedAt = $punch?->punch_out_at ?? $sr->qc_reviewed_at;
    $completed   = $completedAt
        ? \Carbon\Carbon::parse($completedAt)->format('d M Y')
        : 'N/A';

    // $link = $surveyLink ?: url("/client_feedback/{$sr->id}");
    $link = \Illuminate\Support\Facades\URL::temporarySignedRoute(
    'clients.feedback.show',
    now()->addDays(30),
    ['id' => $sr->id]
);

$whatsapp->notifySatisfactionSurvey($sr, $link);
    $params = fn (string $name) => [[
        "type"       => "body",
        "parameters" => [
            ["type" => "text", "text" => $this->cleanParam($name)],
            ["type" => "text", "text" => $ctx['project']],
            ["type" => "text", "text" => $ctx['location']],
            ["type" => "text", "text" => $ctx['ref']],
            ["type" => "text", "text" => $completed],
            ["type" => "text", "text" => $link],
        ],
    ]];

    $preview = fn (string $name) =>
        "Hi {$name}, we hope everything is working well after the maintenance visit. "
        . "Project: {$ctx['project']} | Location: {$ctx['location']} | "
        . "Request: {$ctx['ref']} | Completed: {$completed}. "
        . "Please rate your experience: {$link}";

    $primaryName = $client->contact_name ?: 'Customer';

    $this->sendLogged(
        $sr, $client, $phone, $event, 'satisfaction_survey',
        $params($primaryName),
        $preview($primaryName)
    );

    foreach ($this->notifiableSecondaryContacts($client) as $c) {
        $name = $c['name'] ?: 'Customer';
        $this->sendLogged(
            $sr, $client, $c['phone'], $event, 'satisfaction_survey',
            $params($name),
            $preview($name)
        );
    }
}
}