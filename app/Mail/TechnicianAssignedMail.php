<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TechnicianAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceRequest $sr,
        public string $reference,
        public bool $forTechnician = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Technician Assigned – Maintenance Visit Scheduled ({$this->reference})",
        );
    }

    public function content(): Content
    {
        $this->sr->loadMissing(['client', 'project', 'assignedUser']);

        $eta  = $this->sr->eta_at;
        $tech = $this->sr->assignedUser;

        return new Content(
            view: 'emails.technician_assigned',
            with: [
                'customerName' => $this->sr->client?->company_name ?? 'Customer',
                'projectName'  => $this->sr->project?->project_name ?? '-',
                'location'     => $this->sr->project?->site_name
                                  ?? $this->sr->project_site
                                  ?? '-',
                'issue'        => $this->sr->issue_description ?? '-',
                'technician'   => $tech?->name ?? 'To be confirmed',
                'techPhone'    => $tech?->mobile ?? $tech?->phone ?? '-',
                'visitDate'    => $eta?->format('d M Y') ?? 'To be confirmed',
                'visitTime'    => $eta?->format('h:i A') ?? 'To be confirmed',
            ],
        );
    }
}