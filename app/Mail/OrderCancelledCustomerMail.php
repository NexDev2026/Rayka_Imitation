<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelledCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $reason,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order #'.$this->order->order_number.' Cancellation Confirmation — Rayka',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_cancelled_confirm',
            with: [
                'order' => $this->order,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
