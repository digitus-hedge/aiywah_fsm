<?php

namespace App\Jobs;

use App\Models\ServiceRequest;
use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSatisfactionSurvey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;

    public function __construct(public ServiceRequest $serviceRequest) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $sr = $this->serviceRequest->fresh();

        if (!$sr) {
            return;   // deleted in the meantime
        }

        // The SR may have gone back to rework/hold in the last 24h.
        if (!in_array($sr->status, ['Completed', 'Pending Invoice', 'Invoice Submitted'], true)) {
            Log::info('Survey skipped — SR no longer completed', [
                'sr_id'  => $sr->id,
                'status' => $sr->status,
            ]);
            return;
        }

        // Idempotency guard — a retried or re-dispatched job must not double-send.
        $already = WhatsappLog::where('service_request_id', $sr->id)
            ->where('event', 'Satisfaction Survey')
            ->where('status', WhatsappLog::STATUS_SENT)
            ->exists();

        if ($already) {
            Log::info('Survey skipped — already sent', ['sr_id' => $sr->id]);
            return;
        }

        $whatsapp->notifySatisfactionSurvey($sr);
    }
}