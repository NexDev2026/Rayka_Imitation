<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCodBalanceSettledCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?string $adminNote = null,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'COD Delivery Balance Settled — Order #'.$this->order->order_number.' — Rayka Imitation Jewellery',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_cod_balance_settled',
            with: [
                'order' => $this->order,
                'adminNote' => $this->adminNote,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (! empty($this->order->delivery_payment_screenshot)) {
            $absPath = public_path(ltrim($this->order->delivery_payment_screenshot, '/'));
            if (file_exists($absPath) && is_file($absPath)) {
                $attachments[] = Attachment::fromPath($absPath)
                    ->as('Delivery_Balance_Receipt_'.preg_replace('/[^A-Za-z0-9_-]/', '', $this->order->order_number).'.'.pathinfo($absPath, PATHINFO_EXTENSION));
            }
        }

        return $attachments;
    }
}
