<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkerOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otp, public int $ttl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Matter Mind verification code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.worker_otp');
    }
}