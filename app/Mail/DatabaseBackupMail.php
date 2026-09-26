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
        if (! empty($this->backupData['filepath']) && file_exists($this->backupData['filepath'])) {
            $size = @filesize($this->backupData['filepath']);
            // Attach backup snapshot directly to email if under 15MB
            if ($size > 0 && $size < 15 * 1024 * 1024) {
                return [
                    \Illuminate\Mail\Mailables\Attachment::fromPath($this->backupData['filepath'])
                        ->as($this->backupData['filename'])
                        ->withMime('application/gzip'),
                ];
            }
        }

        return [];
    }
}
