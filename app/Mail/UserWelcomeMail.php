<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public string $roleName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Matter Mind Account Has Been Created',
        );
    }

    public function content(): Content
    {
        // Maintenance leads log in through the worker portal, everyone else
        // through the staff login.
        $isWorker = optional($this->user->role)->code === 'ML';

        return new Content(
            view: 'emails.user_welcome',
            with: [
                'loginUrl' => $isWorker
                    ? route('worker.login')
                    : route('login'),
            ],
        );
    }
}