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
use Illuminate\Support\Facades\Log;

class OrderCodBalanceSettledCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $pdfData = null;

    public function __construct(
        public Order $order,
        public ?string $adminNote = null,
    ) {
        $this->order->loadMissing(['items.product', 'address', 'payment', 'user']);

        // Now that the COD remaining balance has been collected and settled,
        // the order is 100% fully paid. Generate and attach the official final GST Tax Invoice PDF.
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
            Log::warning("Failed to render invoice PDF for COD settled order #{$order->order_number}: ".$e->getMessage());
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Full Payment Completed (COD Balance Settled) — Order #'.$this->order->order_number.' — Rayka Imitation Jewellery',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_cod_balance_settled',
            with: [
                'order' => $this->order,
                'adminNote' => $this->adminNote,
                'hasInvoice' => ! empty($this->pdfData),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach the official GST Tax Invoice PDF
        if ($this->pdfData) {
            $attachments[] = Attachment::fromData(fn () => $this->pdfData, 'Tax-Invoice-'.$this->order->order_number.'.pdf')
                ->withMime('application/pdf');
        }

        // Attach delivery payment screenshot proof if uploaded
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

