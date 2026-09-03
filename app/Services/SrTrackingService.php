<?php
namespace App\Services;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\ServiceRequest;

class SrTrackingService
{
    public function statusCatalogue(): array
    {
        return [
            'Pending'           => ['label' => 'Pending',            'chip' => 'chip-pending', 'color' => '#7c3aed', 'icon' => 'bi-hourglass-split'],
            'Approved'          => ['label' => 'Approved',           'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-check-circle'],
            'Forwarded'         => ['label' => 'Forwarded',          'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-send'],
            'Rejected'          => ['label' => 'Rejected',           'chip' => 'chip-bad',     'color' => '#dc2626', 'icon' => 'bi-x-octagon'],
            'Assigned'          => ['label' => 'Assigned',           'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-person-check'],
            'Quoted'            => ['label' => 'Quoted',             'chip' => 'chip-warn',    'color' => '#b45309', 'icon' => 'bi-receipt'],
            'In Progress'       => ['label' => 'In Progress',        'chip' => 'chip-inprog',  'color' => '#0891b2', 'icon' => 'bi-wrench-adjustable-circle'],
            'Quote Rejected'    => ['label' => 'Quote Rejected',     'chip' => 'chip-bad',     'color' => '#dc2626', 'icon' => 'bi-x-circle'],
            'Qc Review'         => ['label' => 'Qc Review',          'chip' => 'chip-warn',    'color' => '#b45309', 'icon' => 'bi-clipboard2-check'],
            'Rework'            => ['label' => 'Rework',             'chip' => 'chip-warn',    'color' => '#ea580c', 'icon' => 'bi-arrow-repeat'],
            'Reschedule'        => ['label' => 'Reschedule',         'chip' => 'chip-warn',    'color' => '#ea580c', 'icon' => 'bi-calendar-event'],
            'Accepted'          => ['label' => 'Accepted',           'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-hand-thumbs-up'],
            'Pending Invoice'   => ['label' => 'Pending Invoice',    'chip' => 'chip-warn',    'color' => '#b45309', 'icon' => 'bi-file-earmark-text'],
            'Invoice Submitted' => ['label' => 'Invoice Submitted',  'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-file-earmark-check'],
            'Completed'         => ['label' => 'Completed',          'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-patch-check'],
            'On Hold'           => ['label' => 'On Hold',            'chip' => 'chip-pending', 'color' => '#64748b', 'icon' => 'bi-pause-circle'],
            'Additional'        => ['label' => 'Additional Work',    'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-plus-circle'],
            'Quote Approved'    => ['label' => 'Quote Approved',     'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-check2-circle'],
        ];
    }

    public function statusMeta(?string $s): array
    {
        return $this->statusCatalogue()[$s]
            ?? ['label' => $s ?: '-', 'chip' => 'chip-pending', 'color' => '#6b7280', 'icon' => 'bi-circle'];
    }
    /**
     * Assignment + ownership summary for the popup header.
     */
    public function ownershipInfo(ServiceRequest $sr): array
    {
        return [
            'assigned_to' => $sr->assignedUser?->name ?? $sr->assignedSe?->name ?? null,
            'owner'       => $this->currentOwner($sr),
        ];
    }
    private function flowFor(ServiceRequest $sr): array
{
    $isOow = $sr->warranty_scope === 'oow';

    $quote   = $isOow ? ['Quoted', 'Quote Approved'] : [];
    // In-warranty work closes straight from QC - no invoicing stage at all.
    $invoice = $isOow ? ['Pending Invoice', 'Invoice Submitted'] : [];

    return array_merge(
        ['Pending', 'Approved'],
        $quote,
        ['Assigned', 'Accepted', 'In Progress', 'Punched Out', 'Qc Review'],
        $invoice,
        ['Completed']
    );
}

/** Extra display metadata for synthetic milestone-only keys that aren't real SR statuses. */
private function milestoneOnlyMeta(): array
{
    return [
        'Punched Out' => ['label' => 'Punched Out', 'icon' => 'bi-box-arrow-right', 'color' => '#0891b2'],
    ];
}
/** "Aysha (SE)" - falls back to just the name if no role code is present. */
private function withRole(?\App\Models\User $user): ?string
{
    if (!$user) {
        return null;
    }

    $code = $user->role?->code;

    return $code ? "{$user->name} ({$code})" : $user->name;
}
private function stampFor(ServiceRequest $sr, string $key)
{
    return [
        'Pending'           => $sr->created_at,
        'Approved'          => $sr->approved_at,
        'Forwarded'         => $sr->updated_at,
        'Rejected'          => $sr->updated_at,
        'Assigned'          => $sr->dispatched_at,
        'Accepted'          => $sr->accepted_at,
        'In Progress'       => optional($sr->punch)->punch_in_at,
        'Punched Out'       => optional($sr->punch)->punch_out_at,
        'Quoted'            => $sr->quote_submitted_at,
        'Quote Approved'    => $sr->client_approved_at,
        'Quote Rejected'    => $sr->updated_at,
        'Qc Review'         => $sr->qc_reviewed_at,
        'Rework'            => $sr->qc_reviewed_at,
        'Pending Invoice'   => optional($sr->punch)->punch_out_at,
        'Invoice Submitted' => $sr->invoice_submitted_at
            ?? (in_array($sr->status, ['Invoice Submitted', 'Completed'], true) ? $sr->updated_at : null),
        'Completed'         => $sr->status === 'Completed' ? ($sr->hop_approved_at ?? $sr->updated_at) : null,
        'On Hold'           => $sr->updated_at,
    ][$key] ?? null;
}

public function buildMilestones(ServiceRequest $sr): array
{
    $terminal = ['Rejected', 'Quote Rejected'];
    $flow = in_array($sr->status, $terminal, true)
        ? ['Pending', $sr->status]
        : $this->flowFor($sr);

    $cat    = array_merge($this->statusCatalogue(), $this->milestoneOnlyMeta());
    $cur    = array_search($sr->status, $flow, true);
    $actors = $this->actorsByStatus($sr);

    $out = [];

    foreach ($flow as $i => $key) {
        $m = $cat[$key] ?? $this->statusMeta($key);

        $out[] = [
            'key'   => $key,
            'label' => $m['label'],
            'icon'  => $m['icon'],
            'state' => $cur === false ? 'pending' : ($i < $cur ? 'done' : ($i === $cur ? 'active' : 'pending')),
            'time'  => optional($this->stampFor($sr, $key))->format('d M · h:i A') ?? '',
            'desc'  => $this->descFor($sr, $key, $actors),
        ];
    }
    return $out;
}

private function descFor(ServiceRequest $sr, string $key, array $actors): string
{
    $tech = $this->withRole($sr->assignedUser);

    $creator      = $sr->creator ? $this->withRole($sr->creator) : ($sr->reported_by ?: null);
    $approvedBy   = $actors['Approved'] ?? null;
    $dispatchedBy = $actors['Assigned'] ?? null;
    $qcReviewer   = $sr->qc_reviewed_by ? $this->withRole($sr->qcReviewedBy) : null;

    // Who closed it out: HoP approval for OOW, the QC reviewer for IW
    // (qcPass() sends in-warranty work straight to Completed).
    $completedBy = $sr->warranty_scope === 'oow'
        ? ($sr->hop_approved_by ? $this->withRole($sr->hopApprovedBy) : null)
        : $qcReviewer;

    return match ($key) {
        'Pending' => $creator
            ? "SR Logged by {$creator}"
            : 'SR Logged',

        'Approved' => $approvedBy
            ? "SR Approved by {$approvedBy}"
            : trim(strtoupper($sr->warranty_scope ?: '') . ' path confirmed'),

        'Forwarded' => 'Forwarded to accounts',
        'Rejected'  => $sr->internal_remark ?: 'Request rejected',

        'Assigned' => $tech
            ? "SR Assigned to {$tech}" . ($dispatchedBy ? " by {$dispatchedBy}" : '')
            : 'Pending assignment',

        'Accepted'    => $tech ? "Job accepted by {$tech}" : 'Job accepted by technician',
        'In Progress' => $tech ? "{$tech} punched in and started work" : 'Technician on-site and working',
        'Punched Out' => $tech ? "{$tech} finished and submitted to QC" : 'Technician finished and submitted to QC',

        'Quoted'         => 'Quote ' . ($sr->erp_quote_ref ?: 'submitted') . ' sent to client',
        'Quote Approved' => 'Quote accepted, awaiting engineer allocation',
        'Quote Rejected' => 'Quote rejected by client',

        'Qc Review' => $qcReviewer
            ? "Quality check done by {$qcReviewer}"
            : 'Awaiting quality check',

        'Rework' => $sr->rework_notes ?: 'Rework requested by QC',

        'Pending Invoice'   => 'Awaiting invoice generation',
        'Invoice Submitted' => trim('Invoice ' . ($sr->invoice_code ?: '') . ' submitted'),

        'Completed' => $completedBy
            ? "Work completed and closed by {$completedBy}"
            : 'Work completed and closed',

        'On Hold' => 'Request on hold',
        default   => '',
    };
}
    public function buildHistory(ServiceRequest $sr): array
    {
        $cat  = $this->statusCatalogue();
        $rows = [];

        $push = function ($key, $event, $meta, $ts) use (&$rows, $cat) {
            if (!$ts) return;
            $rows[] = [
                'color' => $cat[$key]['color'] ?? '#6b7280',
                'event' => $event,
                'meta'  => $meta,
                'ts'    => $ts,
            ];
        };

        $tech = optional($sr->assignedUser)->name;
        $push('Pending', 'Service Request logged', 'Logged by ' . ($sr->reported_by ?: 'Front Desk'), $sr->created_at);
        $push('Approved', 'SR approved - ' . strtoupper($sr->warranty_scope ?: '') . ' path', 'Contract coverage verified', $sr->approved_at);
        $push('Forwarded', 'SR forwarded to service partner', 'Routed for dispatch', $sr->updated_at && $sr->status === 'Forwarded' ? $sr->updated_at : null);
        $push('Assigned', ($tech ?: 'Technician') . ' assigned', 'Dispatched by Head of Projects', $sr->dispatched_at);
        $push('Quoted', 'Quote submitted - ' . ($sr->erp_quote_ref ?: ''), 'Awaiting client approval', $sr->quote_submitted_at);
        $push('Quote Approved', 'Quote accepted by client', 'Approval received - work authorised', $sr->client_approved_at);
        $push('Quote Rejected', 'Quote rejected by client', 'Client declined the quotation', $sr->status === 'Quote Rejected' ? $sr->quote_rejected_at ?? $sr->updated_at : null);
        $push('In Progress', 'Technician ' . ($tech ?: '') . ' punched in on-site', optional($sr->category)->category_name, optional($sr->punch)->punch_in_at);
        $push('Pending Invoice', 'Technician punched out', optional($sr->punch)->completion_summary, optional($sr->punch)->punch_out_at);
        $push('Qc Review', 'QC review recorded', $sr->status !== 'Rework' ? 'Passed quality check' : null, $sr->status !== 'Rework' ? $sr->qc_reviewed_at : null);
        $push('Rework', 'Rework requested by QC', $sr->rework_notes ?: 'Returned to technician', $sr->status === 'Rework' ? $sr->qc_reviewed_at : null);
        $push('Invoice Submitted', 'Invoice ' . ($sr->invoice_code ?: '') . ' submitted', 'Total: ' . $sr->invoice_total, $sr->invoice_submitted_at);
        $push('Completed', 'Work completed and closed', 'SR closed', $sr->status === 'Completed' ? ($sr->hop_approved_at ?? $sr->updated_at) : null);
        $push('Rejected', 'SR rejected', $sr->internal_remark, $sr->status === 'Rejected' ? $sr->updated_at : null);

        usort($rows, fn($a, $b) => $b['ts'] <=> $a['ts']);

        return array_map(fn($r) => [
            'color' => $r['color'],
            'event' => $r['event'],
            'meta'  => $r['meta'] ?: '-',
            'day'   => $r['ts']->isToday() ? 'Today' : $r['ts']->format('d M'),
            'time'  => $r['ts']->format('h:i A'),
        ], $rows);
    }

   

    /**
     * Who caused each status transition, keyed by the to_status value.
     * NotificationLog rows are ordered ascending and keyed by to_status,
     * so the final write wins - matches the "last moved by" pattern used
     * elsewhere in the app (e.g. ticketSummary()).
     */
    private function actorsByStatus(ServiceRequest $sr): array
    {
        $causerIds = NotificationLog::where('service_request_id', $sr->id)
            ->whereNotNull('caused_by')
            ->orderBy('id')
            ->get(['to_status', 'caused_by'])
            ->groupBy('to_status')
            ->map(fn($rows) => $rows->last()->caused_by)
            ->all();

        if (empty($causerIds)) {
            return [];
        }

        $names = User::whereIn('id', array_unique(array_values($causerIds)))
            ->pluck('name', 'id');

        return array_map(fn($userId) => $names[$userId] ?? null, $causerIds);
    }
    /**
     * Who currently owns this SR's status - i.e. whose court the ball is in.
     * Mirrors the same role logic used across the controllers (assigned_se,
     * assigned_user_id, created_by) so this reads consistently with the rest
     * of the app.
     */
    private function currentOwner(ServiceRequest $sr): ?string
    {
        return match ($sr->status) {
            'Pending'                          => 'Front Desk',
            'Approved', 'Quote Approved'        => 'Dispatch Engine',
            'Forwarded', 'Additional', 'Quoted',
            'Quote Rejected'                    => 'Accounts',
            'Assigned', 'In Progress',
            'Accepted', 'Reschedule', 'On Hold' => $this->withRole($sr->assignedUser) ?? 'Technician',
            'Qc Review'                         => $this->withRole($sr->assignedSe) ?? 'Head of Projects',
            'Rework'                            => $this->withRole($sr->assignedUser) ?? 'Technician',
            'Pending Invoice'                   => 'Accounts',
            'Invoice Submitted'                 => 'Head of Projects',
            'Completed', 'Rejected'             => null, // closed - nobody owns it
            default                             => null,
        };
    }
}