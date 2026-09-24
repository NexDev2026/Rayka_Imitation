<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $ip = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Patron Registered — '.$this->user->name.' (Rayka)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new_registration',
            with: [
                'user' => $this->user,
                'ip' => $this->ip ?: request()->ip(),
                'timestamp' => now()->format('d M, Y \a\t h:i A').' IST',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
