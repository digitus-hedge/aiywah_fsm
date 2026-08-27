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

/**
 * All service-request notifications, keyed by event.
 *
 * Replaces SendSrCreatedNotifications — the 'created' event behaves exactly as
 * that class did, so the only change at the call site is the class name.
 *
 * Engineer recipients are NOT resolved here. WhatsAppService::internalRecipients()
 * already assembles the creator, SA/HP staff, the project engineer and the
 * allocated Service Engineer, and every notifyInternal* call fans out to that
 * list. Duplicating it here is what produced the undefined-method error.
 *
 * Each channel is wrapped individually: a dead WhatsApp API must never stop the
 * email, and a failed internal alert must never stop the customer message.
 */
class SendSrNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const CREATED      = 'created';        // SR logged
    public const APPROVED     = 'approved';       // in-warranty approval
    public const OOW_RELEASED = 'oow_released';   // quote approved, engineer allocated
    public const FORWARDED    = 'forwarded';      // sent to Quotation Desk
    public const ADDITIONAL   = 'additional';     // accepted as additional work
    public const REJECTED     = 'rejected';       // request declined
    public const DISPATCHED   = 'dispatched';  
    public const COMPLETED    = 'completed';
    public const VISIT_SCHEDULED = 'visit_scheduled';
    public const QC_SUBMITTED = 'qc_submitted'; 
    public const MAINTENANCE_STARTED = 'maintenance_started';   
    public const QUOTE_PENDING_ACCOUNTS    = 'quote_pending_accounts';
    public const INVOICE_REQUIRED_ACCOUNTS = 'invoice_required_accounts';
    public const QUOTE_CLIENT_APPROVED     = 'quote_client_approved';
    public const INVOICE_SUBMITTED         = 'invoice_submitted'; 
    public $tries   = 3;
    public $backoff = [10, 60, 180];   // don't hammer a provider that's down
    public $timeout = 120;

    public function __construct(
        public int $srId,
        public string $event = self::CREATED,
        public ?string $ref = null,
        public ?string $actor = null,
    ) {}

    public function handle(WhatsAppService $wa): void
    {
        $sr = ServiceRequest::with([
            'client',
            'project',
            'creator',
            'assignedUser',
            'assignedSe',
        ])->find($this->srId);

        if (! $sr) return;   // deleted between dispatch and run

        $ref = $this->ref ?: $this->buildRef($sr);

        match ($this->event) {
            self::CREATED      => $this->created($wa, $sr, $ref),
            self::APPROVED     => $this->approved($wa, $sr),
            self::OOW_RELEASED => $this->oowReleased($wa, $sr),
            self::FORWARDED    => $this->forwarded($wa, $sr),
            self::ADDITIONAL   => $this->additional($wa, $sr),
            self::REJECTED     => $this->rejected($wa, $sr),
            self::DISPATCHED   => $this->dispatched($wa, $sr, $ref),
            self::COMPLETED    => $this->completed($wa, $sr, $ref),  
            self::VISIT_SCHEDULED   => $this->visitScheduled($wa, $sr),
            self::QC_SUBMITTED      => $this->qcSubmitted($wa, $sr),
            self::MAINTENANCE_STARTED => $this->maintenanceStarted($wa, $sr),
            self::QUOTE_PENDING_ACCOUNTS   => $this->quotePendingAccounts($wa, $sr),
            self::INVOICE_REQUIRED_ACCOUNTS => $this->invoiceRequiredAccounts($wa, $sr),
            self::QUOTE_CLIENT_APPROVED     => $this->quoteClientApproved($wa, $sr),
            self::INVOICE_SUBMITTED     => $this->invoiceSubmitted($wa, $sr),
            default => Log::warning('SR notification: unknown event type', [
                'sr_id' => $this->srId,
                'event' => $this->event,
            ]),
        };
    }

    /* ════════════════════════════════════════════
       EVENTS
    ════════════════════════════════════════════ */

    private function created(WhatsAppService $wa, ServiceRequest $sr, string $ref): void
    {
        $this->safely('wa.created', function () use ($wa, $sr) {
            $wa->notifyServiceRequestReceived($sr);
            $wa->notifyInternalNewRequest($sr);
        });

        if ($to = optional($sr->client)->email) {
            $this->safely('mail.created', fn() =>
                Mail::to($to)->send(new ServiceRequestReceivedMail($sr, $ref))
            );
        } else {
            Log::warning('SR created but client has no email', ['sr_id' => $this->srId]);
        }
    }

    private function approved(WhatsAppService $wa, ServiceRequest $sr): void
    {
        $end = optional($sr->project)->warranty_end_date;
        $inWarranty = $end && \Carbon\Carbon::parse($end)->endOfDay()->isFuture();

        $this->safely('wa.approved', function () use ($wa, $sr, $inWarranty) {
            $inWarranty
                ? $wa->notifyWarrantyApproved($sr)
                : $wa->notifyServiceStatus($sr, 'Approved');

            // Reaches the creator, SA/HP staff, the project engineer and the
            // Service Engineer allocated at approval.
            $wa->notifyInternalSrAccepted($sr);
        });
    }

    private function oowReleased(WhatsAppService $wa, ServiceRequest $sr): void
    {
        $this->safely('wa.oow_released', function () use ($wa, $sr) {
            $wa->notifyServiceStatus($sr, 'Approved');
            $wa->notifyInternalSrAccepted($sr);
        });
    }

    private function forwarded(WhatsAppService $wa, ServiceRequest $sr): void
    {
        $this->safely('wa.forwarded', function () use ($wa, $sr) {
            $wa->notifyOutsideWarranty($sr);
            $wa->notifyInternalOutsideWarranty($sr);
        });
    }

    private function additional(WhatsAppService $wa, ServiceRequest $sr): void
    {
        $this->safely('wa.additional', fn() => $wa->notifyOutsideScope($sr));
    }

    // SendSrNotifications::rejected()
    private function rejected(WhatsAppService $wa, ServiceRequest $sr): void
    {
        $this->safely('wa.rejected', fn() => $wa->notifyServiceStatus($sr, 'Rejected'));
        $this->safely('wa.rejected.internal', fn() => $wa->notifyInternalStatusChange($sr, 'Rejected'));
    }

    /* ════════════════════════════════════════════
       HELPERS
    ════════════════════════════════════════════ */

    /** One channel failing must never take the others down with it. */
    private function safely(string $label, \Closure $fn): void
    {
        try {
            $fn();
        } catch (\Throwable $e) {
            Log::error("SR notification failed [{$label}]", [
                'sr_id' => $this->srId,
                'event' => $this->event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }

    private function dispatched(WhatsAppService $wa, ServiceRequest $sr, string $ref): void
{
    // Customer
    if ($to = $sr->client?->email) {
        $this->safely('mail.dispatched.client', fn() =>
            Mail::to($to)->send(new \App\Mail\TechnicianAssignedMail($sr, $ref))
        );
    } else {
        Log::warning('Tech-assigned mail skipped — client has no email', ['sr_id' => $this->srId]);
    }

    // The Maintenance Lead doing the work
    if ($techEmail = $sr->assignedUser?->email) {
        $this->safely('mail.dispatched.ml', fn() =>
            Mail::to($techEmail)->send(new \App\Mail\TechnicianAssignedMail($sr, $ref, true))
        );
    } else {
        Log::warning('Tech-assigned mail skipped — ML has no email', ['sr_id' => $this->srId]);
    }

    $this->safely('wa.dispatched', function () use ($wa, $sr) {
        $wa->notifyServiceStatus($sr, 'Technician Assigned — awaiting confirmation'); // customer  → status_change
        $wa->notifyMlJobAssigned($sr);               // ML        → ml_job_assigned
        $wa->notifyInternalTechnicianAssigned($sr);  // SA/HP/SE/PE → internal_technician_assigned
    });
}
    private function completed(WhatsAppService $wa, ServiceRequest $sr, string $ref): void
{
    $customerLink = $sr->project
        ? \App\Support\PortalLink::project($sr->project, $sr)
        : \Illuminate\Support\Facades\URL::signedRoute(
            'sr.photos',
            ['serviceRequest' => $sr->id]
        );

    if ($to = $sr->client?->email) {
        $this->safely('mail.completed', fn() =>
            Mail::to($to)->send(new \App\Mail\MaintenanceCompletedMail($sr, $ref, $customerLink))
        );
    } else {
        Log::warning('Completion mail skipped — client has no email', ['sr_id' => $this->srId]);
    }

    $this->safely('wa.completed.customer', fn() =>
        $wa->notifyMaintenanceCompleted($sr, null, $customerLink)
    );

    $this->safely('wa.completed.internal', fn() =>
        $wa->notifyInternalMaintenanceCompleted($sr, null, $customerLink)
    );

    \App\Jobs\SendSatisfactionSurvey::dispatch($sr)
        ->delay(now()->addMinutes(2));   // ← restore addDay() before go-live
}

private function visitScheduled(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.visit_scheduled', function () use ($wa, $sr) {
        $wa->notifyTechnicianAssigned($sr);        // customer  → technician_assigned
        $wa->notifyInternalVisitScheduled($sr);    // SA/HP/SE/PE → internal_visit_scheduled
    });
}
private function qcSubmitted(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.qc_submitted', function () use ($wa, $sr) {
        $wa->notifyServiceStatus($sr, 'QC Review');                                   // customer
        $wa->notifyInternalStatusChange($sr, 'QC Review', optional($sr->assignedUser)->name); // internal
    });
}
private function maintenanceStarted(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.maintenance_started', function () use ($wa, $sr) {
        $wa->notifyMaintenanceStarted($sr);           // customer
        $wa->notifyInternalMaintenanceStarted($sr);    // internal
    });
}
private function quotePendingAccounts(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.quote_pending_accounts', fn() =>
        $wa->notifyQuotePendingAccounts($sr, $this->actor)
    );
}

private function invoiceRequiredAccounts(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.invoice_required_accounts.customer', fn() =>
        $wa->notifyServiceStatus($sr, 'Pending Invoice')
    );

    $this->safely('wa.invoice_required_accounts.internal', fn() =>
        $wa->notifyInternalStatusChange($sr, 'Pending Invoice', $this->actor)
    );

    $this->safely('wa.invoice_required_accounts.accounts', fn() =>
        $wa->notifyInvoiceRequiredAccounts(
            $sr,
            $sr->erp_quote_ref,
            $this->actor,
            $sr->qc_reviewed_at ? \Carbon\Carbon::parse($sr->qc_reviewed_at) : null
        )
    );
}
private function quoteClientApproved(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.quote_client_approved', function () use ($wa, $sr) {
        $wa->notifyServiceStatus($sr, 'Quote Approved');                          // customer
        $wa->notifyInternalStatusChange($sr, 'Quote Approved', $this->actor);     // internal
    });
}
private function invoiceSubmitted(WhatsAppService $wa, ServiceRequest $sr): void
{
    $this->safely('wa.invoice_submitted', function () use ($wa, $sr) {
        $wa->notifyServiceStatus($sr, 'Invoice Submitted');                          // customer
        $wa->notifyInternalStatusChange($sr, 'Invoice Submitted', $this->actor);     // internal
    });
}
}