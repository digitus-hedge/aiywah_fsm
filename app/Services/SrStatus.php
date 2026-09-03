<?php

namespace App\Support;

/**
 * Single source of truth for service-request status presentation.
 *
 * ClientController already had a private statusCatalogue(); point it here so the
 * staff screens and the customer portal never drift apart:
 *
 *   private function statusCatalogue(): array { return \App\Support\SrStatus::all(); }
 */
class SrStatus
{
    public static function all(): array
    {
        return [
            'Pending'           => ['label' => 'Pending',           'tone' => 'wait', 'color' => '#7c3aed', 'icon' => 'bi-hourglass-split'],
            'Approved'          => ['label' => 'Approved',          'tone' => 'ok',   'color' => '#15803d', 'icon' => 'bi-check-circle'],
            'Forwarded'         => ['label' => 'Forwarded',         'tone' => 'info', 'color' => '#2563eb', 'icon' => 'bi-send'],
            'Rejected'          => ['label' => 'Rejected',          'tone' => 'bad',  'color' => '#dc2626', 'icon' => 'bi-x-octagon'],
            'Assigned'          => ['label' => 'Technician assigned', 'tone' => 'info', 'color' => '#2563eb', 'icon' => 'bi-person-check'],
            'Quoted'            => ['label' => 'Quote sent',        'tone' => 'warn', 'color' => '#b45309', 'icon' => 'bi-receipt'],
            'In Progress'       => ['label' => 'Work in progress',  'tone' => 'live', 'color' => '#0891b2', 'icon' => 'bi-wrench-adjustable-circle'],
            'Quote Rejected'    => ['label' => 'Quote declined',    'tone' => 'bad',  'color' => '#dc2626', 'icon' => 'bi-x-circle'],
            'Qc Review'         => ['label' => 'Quality check',     'tone' => 'warn', 'color' => '#b45309', 'icon' => 'bi-clipboard2-check'],
            'Rework'            => ['label' => 'Rework',            'tone' => 'warn', 'color' => '#ea580c', 'icon' => 'bi-arrow-repeat'],
            'Reschedule'        => ['label' => 'Rescheduled',       'tone' => 'warn', 'color' => '#ea580c', 'icon' => 'bi-calendar-event'],
            'Accepted'          => ['label' => 'Quote accepted',    'tone' => 'ok',   'color' => '#15803d', 'icon' => 'bi-hand-thumbs-up'],
            'Pending Invoice'   => ['label' => 'Awaiting invoice',  'tone' => 'warn', 'color' => '#b45309', 'icon' => 'bi-file-earmark-text'],
            'Invoice Submitted' => ['label' => 'Invoice submitted', 'tone' => 'info', 'color' => '#2563eb', 'icon' => 'bi-file-earmark-check'],
            'Completed'         => ['label' => 'Completed',         'tone' => 'ok',   'color' => '#15803d', 'icon' => 'bi-patch-check'],
            'On Hold'           => ['label' => 'On hold',           'tone' => 'wait', 'color' => '#64748b', 'icon' => 'bi-pause-circle'],
        ];
    }

    public static function meta(?string $status): array
    {
        return self::all()[$status]
            ?? ['label' => $status ?: '-', 'tone' => 'wait', 'color' => '#6b7280', 'icon' => 'bi-circle'];
    }

    /** Statuses a customer should read as "still open". */
    public static function openStatuses(): array
    {
        return [
            'Pending', 'Approved', 'Forwarded', 'Assigned', 'Quoted', 'In Progress',
            'Qc Review', 'Rework', 'Reschedule', 'Accepted', 'Pending Invoice',
            'Invoice Submitted', 'On Hold',
        ];
    }
}