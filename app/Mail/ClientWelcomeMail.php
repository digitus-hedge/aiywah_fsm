<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public ?Project $project = null,
        public ?string $portalUrl = null,
    ) {}

    public function build()
    {
        return $this->subject('Welcome to Matter Mind - Post-Handover Maintenance Portal')
            ->markdown('emails.client_welcome');
    }
}