<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Royal Heritage — Rayka Imitation Jewellery',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.welcome_user',
            with: ['user' => $this->user],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
