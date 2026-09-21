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
use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

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

                // If QC hasn't reviewed it yet (still sitting where punchOut() left it),
        // auto-complete it now - the SLA window for the customer to act has passed.
        if ($sr->status === 'Qc Review') {
            $sr->loadMissing('project');

            $warrantyEnd  = optional($sr->project)->warranty_end_date;
            $isInWarranty = $warrantyEnd
                && \Carbon\Carbon::parse($warrantyEnd)->endOfDay()->isFuture();

            $newStatus = $isInWarranty ? 'Completed' : 'Pending Invoice';
            $ref       = 'SR-' . ($sr->created_at?->year ?? now()->year)
                . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($sr, $newStatus, $isInWarranty, $ref) {
                $sr->update([
                    'status'         => $newStatus,
                    'qc_reviewed_at' => now(),
                    // qc_reviewed_by stays null - nobody reviewed it, the system auto-passed it.
                ]);

                $sr->punches()
                    ->whereIn('status', ['submitted', 'qc_review'])
                    ->update(['status' => 'qc_passed']);

                NotificationLog::create([
                    'service_request_id' => $sr->id,
                    'event'       => 'status_updated',
                    'title'       => 'Status Updated',
                    'message'     => "{$ref} auto-passed QC - no reviewer action within the SLA window - "
                        . ($isInWarranty ? 'marked Completed' : 'forwarded to invoicing'),
                    'from_status' => 'Qc Review',
                    'to_status'   => $newStatus,
                    'caused_by'   => null,
                ]);
            });

            // Out-of-warranty still needs Accounts to invoice it - same routing qcPass() uses.
            if (!$isInWarranty) {
                \App\Jobs\SendSrNotifications::dispatch(
                    $sr->id,
                    \App\Jobs\SendSrNotifications::INVOICE_REQUIRED_ACCOUNTS,
                    $ref
                );
            }

            $sr->refresh();
        }
        // Anything else that isn't a completed/invoicing state means QC already
        // moved it elsewhere (Rework, On Hold, etc.) - respect that and skip.
        elseif (!in_array($sr->status, ['Completed', 'Pending Invoice', 'Invoice Submitted'], true)) {
            Log::info('Survey skipped - SR no longer eligible', [
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
                Log::warning('Survey mail skipped - client has no email', ['sr_id' => $sr->id]);
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
            Log::info('Survey WhatsApp skipped - already sent', ['sr_id' => $sr->id]);
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