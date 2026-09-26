<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $backupData) {}

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
        if (empty($this->backupData['skip_attachment']) && ! empty($this->backupData['filepath']) && file_exists($this->backupData['filepath'])) {
            $size = @filesize($this->backupData['filepath']);
            // Attach backup snapshot directly to email if under 8MB (safe within Brevo 10MB payload limit)
            if ($size > 0 && $size <= 8 * 1024 * 1024) {
                $ext = strtolower(pathinfo((string) $this->backupData['filename'], PATHINFO_EXTENSION));
                $mime = ($ext === 'zip') ? 'application/zip' : 'application/octet-stream';

                return [
                    Attachment::fromPath($this->backupData['filepath'])
                        ->as($this->backupData['filename'])
                        ->withMime($mime),
                ];
            }
        }

        return [];
    }
}
