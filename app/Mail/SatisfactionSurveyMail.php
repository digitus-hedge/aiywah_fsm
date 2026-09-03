<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SatisfactionSurveyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceRequest $sr,
        public string $reference,
        public string $surveyUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We'd Love Your Feedback – Maintenance Service Survey",
        );
    }

    public function content(): Content
    {
        $this->sr->loadMissing(['client', 'project']);

        $completedAt = $this->sr->qc_reviewed_at ?? $this->sr->updated_at;

        return new Content(
            view: 'emails.satisfaction_survey',
            with: [
                'customerName'   => $this->sr->client?->company_name ?? 'Customer',
                'projectName'    => $this->sr->project?->project_name ?? '-',
                'location'       => $this->sr->project?->site_name
                                    ?? $this->sr->project_site
                                    ?? '-',
                'completionDate' => $completedAt?->format('d M Y') ?? '-',
            ],
        );
    }
}