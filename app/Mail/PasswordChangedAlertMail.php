<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $ip = null,
        public ?string $userAgent = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: Password Changed — Rayka',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.password_changed',
            with: [
                'user' => $this->user,
                'ip' => $this->ip ?: request()->ip(),
                'userAgent' => $this->userAgent ?: request()->userAgent(),
                'timestamp' => now()->format('d M, Y \a\t h:i A').' IST',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
