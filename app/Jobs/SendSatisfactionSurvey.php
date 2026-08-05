<?php

namespace App\Jobs;

use App\Mail\SatisfactionSurveyMail;
use App\Models\ServiceRequest;
use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendSatisfactionSurvey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;

    public function __construct(public ServiceRequest $serviceRequest) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $sr = $this->serviceRequest->fresh(['client', 'project']);

        if (!$sr) {
            return;   // deleted in the meantime
        }

        // The SR may have gone back to rework/hold since the job was queued.
        if (!in_array($sr->status, ['Completed', 'Pending Invoice', 'Invoice Submitted'], true)) {
            Log::info('Survey skipped — SR no longer completed', [
                'sr_id'  => $sr->id,
                'status' => $sr->status,
            ]);
            return;
        }

        // One link for every channel, so internal staff can test what the customer got.
        $link = URL::temporarySignedRoute(
            'clients.feedback.show',
            now()->addDays(30),
            ['id' => $sr->id]
        );

        $ref = 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);

        // ---- Email ----
        try {
            if ($to = $sr->client?->email) {
                Mail::to($to)->send(new SatisfactionSurveyMail($sr, $ref, $link));
                Log::info('Survey mail sent', ['sr_id' => $sr->id, 'to' => $to]);
            } else {
                Log::warning('Survey mail skipped — client has no email', ['sr_id' => $sr->id]);
            }
        } catch (\Throwable $e) {
            Log::error('Survey mail failed', [
                'sr_id' => $sr->id,
                'error' => $e->getMessage(),
            ]);
        }

        // ---- WhatsApp ----
        $already = WhatsappLog::where('service_request_id', $sr->id)
            ->where('event', 'Satisfaction Survey')
            ->where('status', WhatsappLog::STATUS_SENT)
            ->exists();

        if ($already) {
            Log::info('Survey WhatsApp skipped — already sent', ['sr_id' => $sr->id]);
            return;
        }

        try {
            $whatsapp->notifySatisfactionSurvey($sr, $link);
            $whatsapp->notifyInternalSurveySent($sr, $link);
        } catch (\Throwable $e) {
            Log::error('Survey WhatsApp failed', [
                'sr_id' => $sr->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}