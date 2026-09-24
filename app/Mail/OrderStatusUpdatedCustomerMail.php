<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\StoreSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $pdfData = null;

    public function __construct(
        public Order $order,
        public string $status,
        public ?string $customNote = null,
        bool $attachInvoice = false,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);

        // Generate and attach Tax Invoice PDF if payment is confirmed or explicitly requested
        if ($attachInvoice || $status === 'Confirmed') {
            try {
                $storeName = StoreSetting::get('store_name', 'Rayka Imitation Jewellery');
                $storeAddress = StoreSetting::get('store_address', 'Surat, Gujarat, India');
                $storePhone = StoreSetting::get('store_phone', '+91 98765 43210');
                $storeEmail = StoreSetting::get('store_email', 'support@rayka.in');
                $upiId = StoreSetting::get('upi_id', 'raykajewellers@okicici');

                $pdf = Pdf::loadView('pdf.invoice', [
                    'order' => $this->order,
                    'storeName' => $storeName,
                    'storeAddress' => $storeAddress,
                    'storePhone' => $storePhone,
                    'storeEmail' => $storeEmail,
                    'upiId' => $upiId,
                ]);

                $this->pdfData = $pdf->output();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to render invoice PDF for order #{$order->order_number}: ".$e->getMessage());
            }
        }
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'Confirmed' => 'Payment Confirmed & Tax Invoice — Order #'.$this->order->order_number.' (Rayka)',
            'Processing' => 'Creations in Crafting — Order #'.$this->order->order_number.' (Rayka)',
            'Shipped' => 'Your Ornaments Have Been Dispatched — Order #'.$this->order->order_number.' (Rayka)',
            'Delivered' => 'Order Delivered with Royal Compliments — #'.$this->order->order_number.' (Rayka)',
            default => "Order #{$this->order->order_number} Status Update — Rayka",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_status_updated',
            with: [
                'order' => $this->order,
                'status' => $this->status,
                'customNote' => $this->customNote,
                'hasInvoice' => ! empty($this->pdfData),
            ],
        );
    }

    public function attachments(): array
    {
        if ($this->pdfData) {
            return [
                Attachment::fromData(fn () => $this->pdfData, 'Tax-Invoice-'.$this->order->order_number.'.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
