<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceRequest $sr,
        public string $reference,
        public string $portalUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Maintenance Successfully Completed – {$this->reference}",
        );
    }

    public function content(): Content
    {
        $this->sr->loadMissing(['client', 'project', 'assignedUser', 'punches']);

        $punch = $this->sr->punches
            ->whereNotNull('punch_out_at')
            ->sortByDesc('punch_out_at')
            ->first();

        $out = $punch?->punch_out_at;

        return new Content(
            view: 'emails.maintenance_completed',
            with: [
                'customerName'    => $this->sr->client?->company_name ?? 'Customer',
                'projectName'     => $this->sr->project?->project_name ?? '-',
                'location'        => $this->sr->project?->site_name
                                     ?? $punch?->site_location
                                     ?? $this->sr->project_site
                                     ?? '-',
                'issue'           => $this->sr->issue_description ?? '-',
                'technician'      => $this->sr->assignedUser?->name ?? 'Our service team',
                'completionDate'  => $out?->format('d M Y') ?? now()->format('d M Y'),
                'completionTime'  => $out?->format('h:i A') ?? now()->format('h:i A'),
            ],
        );
    }
}