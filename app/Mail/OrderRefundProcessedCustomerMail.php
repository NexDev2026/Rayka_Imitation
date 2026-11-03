<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefundProcessedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Refund Completed — Order #'.$this->order->order_number.' — Rayka Imitation Jewellery',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_refund_processed',
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (! empty($this->order->refund_screenshot)) {
            $absPath = public_path(ltrim($this->order->refund_screenshot, '/'));
            if (file_exists($absPath) && is_file($absPath)) {
                $attachments[] = Attachment::fromPath($absPath)
                    ->as('Refund_Receipt_'.preg_replace('/[^A-Za-z0-9_-]/', '', $this->order->order_number).'.'.pathinfo($absPath, PATHINFO_EXTENSION));
            }
        }

        return $attachments;
    }
}

