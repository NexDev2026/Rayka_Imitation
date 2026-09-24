<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $backupData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Database Backup: {$this->backupData['filename']} ({$this->backupData['size_readable']}) — Rayka",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.backup_completed',
            with: [
                'data' => $this->backupData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
