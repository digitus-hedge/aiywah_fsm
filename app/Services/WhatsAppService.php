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
     */
    public function notifyServiceStatus(
        \App\Models\ServiceRequest $sr,
        string $status,
        string $event = 'SR Status Update'
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

        $this->sendLogged(
            $sr,
            $client,
            $phone,
            $event,
            'status_change',
            [[
                "type"       => "body",
                "parameters" => [
                    ["type" => "text", "text" => (string) $client->contact_name],
                    ["type" => "text", "text" => (string) $ref],
                    ["type" => "text", "text" => (string) $status],
                ],
            ]],
            "Hi {$client->contact_name}, your request {$ref} status: {$status}"
        );

        // Secondary contacts with notify = 1
        foreach ($this->notifiableSecondaryContacts($client) as $c) {
            $this->sendLogged(
                $sr,
                $client,
                $c['phone'],
                $event,
                'status_change',
                [[
                    "type"       => "body",
                    "parameters" => [
                        ["type" => "text", "text" => (string) $c['name']],
                        ["type" => "text", "text" => (string) $ref],
                        ["type" => "text", "text" => (string) $status],
                    ],
                ]],
                "Hi {$c['name']}, request {$ref} status: {$status}"
            );
        }
    }

    /**
     * SR creation → template: sr_creation
     * Body vars: {{1}} name, {{2}} SR ref   (status is fixed text in the template)
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

        $ref = $this->buildRef($sr);

        $this->sendLogged(
            $sr,
            $client,
            $phone,
            $event,
            'sr_creation',
            [[
                "type"       => "body",
                "parameters" => [
                    ["type" => "text", "text" => (string) $client->contact_name],
                    ["type" => "text", "text" => (string) $ref],
                ],
            ]],
            "Hi {$client->contact_name}, your request {$ref} was created"
        );

        // Secondary contacts with notify = 1
        foreach ($this->notifiableSecondaryContacts($client) as $c) {
            $this->sendLogged(
                $sr,
                $client,
                $c['phone'],
                $event,
                'sr_creation',
                [[
                    "type"       => "body",
                    "parameters" => [
                        ["type" => "text", "text" => (string) $c['name']],
                        ["type" => "text", "text" => (string) $ref],
                    ],
                ]],
                "Hi {$c['name']}, request {$ref} was created"
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
     * Body vars: {{1}} name, {{2}} SR ref/code   (status is fixed text)
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
}