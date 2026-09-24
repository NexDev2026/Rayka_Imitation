<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByAdminCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $actionType, // 'Cancelled' or 'Rejected'
        public string $reasonNote,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        $subject = $this->actionType === 'Rejected'
            ? 'Notice: Payment Verification Unsuccessful — Order #'.$this->order->order_number.' (Rayka)'
            : 'Important Notice: Order #'.$this->order->order_number.' Cancelled (Rayka)';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_order_cancelled_by_admin',
            with: [
                'order' => $this->order,
                'actionType' => $this->actionType,
                'reasonNote' => $this->reasonNote,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
