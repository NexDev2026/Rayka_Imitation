<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice — {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #2E180E;
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
        }
        .invoice-box {
            border: 2px solid #D4AF6A;
            padding: 24px;
            border-radius: 8px;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #D4AF6A;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }
        .company-title {
            font-size: 22px;
            font-weight: bold;
            color: #4A2C1D;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .company-tagline {
            font-size: 10px;
            font-weight: bold;
            color: #996E2E;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .company-contact {
            font-size: 9.5px;
            color: #666666;
            margin-top: 4px;
            line-height: 1.4;
        }
        .invoice-badge-wrap {
            text-align: right;
            vertical-align: top;
        }
        .invoice-badge {
            display: inline-block;
            background: #4A2C1D;
            color: #E7C77B;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 14px;
            border-radius: 4px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .invoice-meta {
            font-size: 10px;
            margin-top: 6px;
            color: #444444;
            line-height: 1.5;
        }
        .paid-stamp {
            display: inline-block;
            border: 2px solid #10B981;
            color: #10B981;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 18px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .card-inner {
            background: #FAF7F0;
            border: 1px solid #EFE8DD;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #996E2E;
            letter-spacing: 1px;
            border-bottom: 1px solid #D4AF6A;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 18px;
        }
        .items-table th {
            background-color: #4A2C1D;
            border: 1px solid #4A2C1D;
            padding: 7px 10px;
            text-align: left;
            font-size: 10px;
            color: #E7C77B;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .items-table td {
            border: 1px solid #EFE8DD;
            padding: 7px 10px;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background-color: #FAF7F0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .totals-table td {
            padding: 4px 8px;
            font-size: 10.5px;
        }
        .grand-total {
            font-size: 13px;
            font-weight: bold;
            color: #4A2C1D;
            background: #FAF7F0;
            border-top: 2px solid #D4AF6A;
            border-bottom: 2px solid #D4AF6A;
        }
        .clear {
            clear: both;
        }
        .footer {
            border-top: 1px solid #D4AF6A;
            padding-top: 12px;
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #777777;
            line-height: 1.45;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <div class="company-title">{{ $storeName ?? 'Rayka Imitation Jewellery' }}</div>
                <div class="company-tagline">{{ $storeSettings['tagline'] ?? '1 Gram Micro Gold Plating & Royal Heritage Creations' }}</div>
                <div class="company-contact">
                    {{ $storeAddress ?: ($storeSettings['store_address'] ?? 'Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad - 380026, Gujarat') }}<br>
                    Helpline: {{ $storePhone ?: ($storeSettings['store_phone'] ?? '+91 9638868024') }} | Email: {{ $storeEmail ?: ($storeSettings['store_email'] ?? 'raykaimitation@gmail.com') }}
                </div>
            </td>
            <td class="invoice-badge-wrap" style="width: 42%;">
                <div class="invoice-badge">TAX INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('d M, Y \a\t h:i A') }} IST<br>
                    <strong>Payment Method:</strong> {{ $order->payment?->payment_method ?: 'UPI QR Code' }}<br>
                    @if($order->payment?->transaction_reference)
                        <strong>UTR Ref:</strong> {{ $order->payment->transaction_reference }}<br>
                    @endif
                </div>
                <div class="paid-stamp">✓ VERIFIED & PAID</div>
            </td>
        </tr>
    </table>

    <!-- Billing & Dispatch Addresses -->
    <table class="info-table">
        <tr>
            <td class="info-col" style="padding-right: 8px;">
                <div class="card-inner">
                    <div class="section-title">Billed & Shipped To</div>
                    <strong>{{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }}</strong><br>
                    {{ $order->address?->formatted_address ?? 'Address on file' }}<br>
                    Phone: {{ $order->address?->mobile }}<br>
                    Email: {{ $order->address?->email ?? $order->user?->email }}
                </div>
            </td>
            <td class="info-col" style="padding-left: 8px;">
                <div class="card-inner">
                    <div class="section-title">Order & Logistics Status</div>
                    <strong>Order Status:</strong> {{ $order->status }}<br>
                    <strong>Logistics Carrier:</strong> {{ $order->tracking_carrier ?: 'Standard Royal Dispatch' }}<br>
                    <strong>Airway Bill (AWB):</strong> {{ $order->tracking_number ?: 'Pending Dispatch' }}<br>
                    <strong>GST Included:</strong> Yes (HSN Code: 7117 — Imitation Jewellery)
                </div>
            </td>
        </tr>
    </table>

    <!-- Ordered Items Table -->
    @php
        $taxableAmount = round(($order->subtotal - $order->coupon_discount) / 1.03, 2);
        $totalGst = round(($order->subtotal - $order->coupon_discount) - $taxableAmount, 2);
        $cgst = round($totalGst / 2, 2);
        $sgst = round($totalGst - $cgst, 2);
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 44%;">Jewellery Description</th>
                <th style="width: 11%;" class="text-center">HSN</th>
                <th style="width: 14%;">SKU</th>
                <th style="width: 8%;" class="text-center">Qty</th>
                <th style="width: 9%;" class="text-right">Rate</th>
                <th style="width: 9%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->variant_info)
                            <div style="font-size: 9px; color: #777;">Variant: {{ $item->variant_info }}</div>
                        @endif
                    </td>
                    <td class="text-center" style="font-family: monospace; font-size: 9px; color: #555;">7117</td>
                    <td style="font-family: monospace; font-size: 9px;">{{ $item->product_sku ?: 'RAY-'.str_pad($item->product_id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-center font-bold">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right font-bold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- GST Breakdown & Totals Table -->
    <table style="width: 100%; margin-top: 6px; margin-bottom: 20px;">
        <tr>
            <!-- Left: Tax Breakdown Note -->
            <td style="width: 50%; vertical-align: top; padding-right: 12px;">
                <div class="card-inner" style="font-size: 9.5px; color: #555555; line-height: 1.6;">
                    <div class="section-title">GST Tax Summary (HSN: 7117 — 3%)</div>
                    <table style="width: 100%; font-size: 9.5px;">
                        <tr>
                            <td>Taxable Value:</td>
                            <td class="text-right">Rs. {{ number_format($taxableAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>CGST (1.5%):</td>
                            <td class="text-right">Rs. {{ number_format($cgst, 2) }}</td>
                        </tr>
                        <tr>
                            <td>SGST (1.5%):</td>
                            <td class="text-right">Rs. {{ number_format($sgst, 2) }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #D4AF6A; font-weight: bold; color: #4A2C1D;">
                            <td>Total Tax (Included in Price):</td>
                            <td class="text-right">Rs. {{ number_format($totalGst, 2) }}</td>
                        </tr>
                    </table>
                    <div style="margin-top: 6px; font-size: 8.5px; color: #888;">
                        * Taxes are computed as per GST guidelines for Imitation Jewellery (HSN 7117).
                    </div>
                </div>
            </td>

            <!-- Right: Order Financials -->
            <td style="width: 50%; vertical-align: top; padding-left: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 3px 0; font-size: 10.5px;">Items Subtotal:</td>
                        <td class="text-right" style="padding: 3px 0; font-size: 10.5px; font-weight: bold;">Rs. {{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if($order->coupon_discount > 0)
                        <tr style="color: #10B981; font-weight: bold;">
                            <td style="padding: 3px 0; font-size: 10.5px;">Coupon Discount ({{ $order->coupon_code }}):</td>
                            <td class="text-right" style="padding: 3px 0; font-size: 10.5px;">− Rs. {{ number_format($order->coupon_discount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding: 3px 0; font-size: 10.5px;">Shipping Charges:</td>
                        <td class="text-right" style="padding: 3px 0; font-size: 10.5px;">{{ $order->shipping_fee == 0 ? 'FREE' : 'Rs. ' . number_format($order->shipping_fee, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td style="padding: 8px 6px; font-size: 13px; font-weight: bold; color: #4A2C1D; background: #FAF7F0; border-top: 2px solid #D4AF6A; border-bottom: 2px solid #D4AF6A;">
                            Grand Total (Paid):
                        </td>
                        <td class="text-right" style="padding: 8px 6px; font-size: 14px; font-weight: bold; color: #4A2C1D; background: #FAF7F0; border-top: 2px solid #D4AF6A; border-bottom: 2px solid #D4AF6A;">
                            Rs. {{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="clear"></div>

    <!-- Footer -->
    <div class="footer">
        <p>
            This is an authentic computer-generated Tax Invoice issued by <strong>{{ $storeName ?? 'Rayka Imitation Jewellery' }}</strong> and requires no physical signature.<br>
            All creations feature Rayka's 100% Genuine 1 Gram Micro Gold Craftsmanship Assurance.<br>
            For assistance or concierge services, contact <strong>{{ $storeEmail ?: ($storeSettings['store_email'] ?? 'raykaimitation@gmail.com') }}</strong> or call <strong>{{ $storePhone ?: ($storeSettings['store_phone'] ?? '+91 9638868024') }}</strong>.
        </p>
    </div>
</div>

</body>
</html>
