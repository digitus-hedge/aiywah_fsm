<?php

namespace App\Jobs;

use App\Mail\ServiceRequestReceivedMail;
use App\Models\ServiceRequest;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSrCreatedNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 3;
    public $timeout = 60;

    public function __construct(public int $srId, public string $ref) {}

    public function handle(WhatsAppService $wa): void
    {
        $sr = ServiceRequest::with(['client', 'project', 'creator'])->find($this->srId);
        if (! $sr) return;

        try {
            $wa->notifyServiceRequestReceived($sr);
            $wa->notifyInternalNewRequest($sr);
        } catch (\Throwable $e) {
            Log::error('SR created WhatsApp failed', ['sr_id' => $this->srId, 'error' => $e->getMessage()]);
        }

        if ($to = optional($sr->client)->email) {
            try {
                Mail::to($to)->send(new ServiceRequestReceivedMail($sr, $this->ref));
            } catch (\Throwable $e) {
                Log::error('SR created mail failed', ['sr_id' => $this->srId, 'error' => $e->getMessage()]);
            }
        } else {
            Log::warning('SR created but client has no email', ['sr_id' => $this->srId]);
        }
    }
}