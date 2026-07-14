<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private function endpoint()
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
            "to" => $phone,
            "type" => "template",
            "template" => [
                "name" => $template,
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

    public function sendRegistration($phone, $contactName, $token, $lang = 'en_US')
    {
        return $this->sendTemplate($phone, 'client_registration', $lang, [
            [
                "type" => "body",
                "parameters" => [
                    ["type" => "text", "text" => $contactName],
                    ["type" => "text", "text" => $token],
                ],
            ],
        ]);
    }
    public function sendOrderTest($phone, $contactName, $uniqueCode, $delivery, $lang = 'en_US')
{
    return $this->sendTemplate($phone, 'jaspers_market_order_confirmation_v1', $lang, [
        [
            "type" => "body",
            "parameters" => [
                ["type" => "text", "text" => $contactName],  // {{1}}
                ["type" => "text", "text" => $uniqueCode],   // {{2}} order number
                ["type" => "text", "text" => $delivery],     // {{3}} delivery estimate
            ],
        ],
    ]);
}

public function sendServiceRequest($phone, $contactName, $srReference, $status, $lang = 'en_US')
{
    return $this->sendTemplate($phone, 'jaspers_market_order_confirmation_v1', $lang, [
        [
            "type" => "body",
            "parameters" => [
                ["type" => "text", "text" => $contactName],  // {{1}}
                ["type" => "text", "text" => $srReference],  // {{2}} SR reference
                ["type" => "text", "text" => $status],       // {{3}} status
            ],
        ],
    ]);
}

public function notifyServiceStatus(\App\Models\ServiceRequest $sr, string $status): void
{
    try {
        $client = $sr->client;
        if (!$client) {
            \Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
            return;
        }

        $phone = $this->formatWhatsAppNumber(
            $client->primary_country,
            $client->primary_mobile
        );
        if (!$phone) {
            \Log::warning('WhatsApp skipped — no phone', ['sr_id' => $sr->id]);
            return;
        }

        $ref = 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);

        $result = $this->sendServiceRequest($phone, $client->contact_name, $ref, $status);

        \Log::info('WhatsApp service status sent', [
            'sr_id'  => $sr->id,
            'status' => $status,
            'phone'  => $phone,
            'result' => $result,
        ]);
    } catch (\Throwable $e) {
        \Log::error('WhatsApp service status message failed', [
            'sr_id' => $sr->id,
            'error' => $e->getMessage(),
        ]);
    }
}
public function sendDocumentTemplate(
    $phone, $template, array $bodyParams, string $docLink,
    ?string $filename = null, $lang = 'en_US'
) {
    $components = [
        [
            "type" => "header",
            "parameters" => [[
                "type" => "document",
                "document" => array_filter([
                    "link"     => $docLink,
                    "filename" => $filename ?? 'quotation.pdf',
                ]),
            ]],
        ],
        [
            "type" => "body",
            "parameters" => array_map(
                fn ($t) => ["type" => "text", "text" => (string) $t],
                $bodyParams
            ),
        ],
    ];

    return $this->sendTemplate($phone, $template, $lang, $components);
}
public function sendQuotation(\App\Models\ServiceRequest $sr, string $docLink = ''): void
{
    try {
        $client = $sr->client;
        if (!$client) { \Log::warning('Quote WA skipped — no client', ['sr_id' => $sr->id]); return; }

        $phone = $this->formatWhatsAppNumber($client->primary_country, $client->primary_mobile);
        if (!$phone) { \Log::warning('Quote WA skipped — no phone', ['sr_id' => $sr->id]); return; }

        $ref = $sr->erp_quote_ref ?? ('SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT));

        // Reuses the working jaspers_market_order_confirmation_v1 template.
        // {{1}} name · {{2}} ref · {{3}} status
        $result = $this->sendServiceRequest(
            $phone,
            $client->contact_name,
            $ref,
            'Waiting for quotation approval'
        );

        \Log::info('Quotation WA sent', ['sr_id' => $sr->id, 'result' => $result]);
    } catch (\Throwable $e) {
        \Log::error('Quotation WA failed', ['sr_id' => $sr->id, 'error' => $e->getMessage()]);
    }
}
}