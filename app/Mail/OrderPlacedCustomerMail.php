<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        $paymentStatus = strtolower((string) ($this->order->payment?->status ?? 'pending'));
        $paymentMethod = strtolower((string) ($this->order->payment?->payment_method ?? ''));
        $isPaid = in_array($paymentStatus, ['confirmed', 'success', 'paid']);
        $isCod = str_contains($paymentMethod, 'cod') || str_contains($paymentMethod, 'cash');

        if ($isPaid) {
            $subject = 'Payment successful for Rayka Imitation Jewellery — Order #'.$this->order->order_number;
        } elseif ($isCod) {
            $subject = 'Order Booked (Cash on Delivery) — Order #'.$this->order->order_number.' — Rayka';
        } else {
            $subject = 'Order Received (Payment Verification in Progress) — Order #'.$this->order->order_number.' — Rayka';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_placed',
            with: [
                'order' => $this->order,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
