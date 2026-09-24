<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByCustomerAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $reason,
        public ?string $comment = null,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order #'.$this->order->order_number.' Cancelled by Customer — Rayka',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.admin_order_cancelled',
            with: [
                'order' => $this->order,
                'reason' => $this->reason,
                'comment' => $this->comment,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
