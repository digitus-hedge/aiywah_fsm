<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceRequestReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceRequest $sr,
        public string $reference,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Service Request Has Been Received – {$this->reference}",
        );
    }

    public function content(): Content
{
    return new Content(
        view: 'emails.service_request_received',
        with: [
            'customerName' => $this->sr->client?->company_name ?? 'Customer',
            'projectName'  => $this->sr->project?->project_name ?? '-',
            'location'     => $this->sr->project?->site_name
                              ?? $this->sr->project_site
                              ?? '-',
        ],
    );
}
}