@extends('layouts.storefront')

@section('title', 'Refund & Cancellation Policy — Rayka Imitation Jewellery')
@section('meta_description', 'Read the Payment Refund and Cancellation Policy for Rayka Imitation Jewellery. Understand how we process refunds, cancellations, and secure your transactions.')

@section('content')
<div class="bg-[#FAF7F0] min-h-screen py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="font-serif-royal text-3xl sm:text-4xl md:text-5xl text-[#4A2C1D] font-bold mb-4">Refund & Cancellation Policy</h1>
            <div class="h-1 w-24 bg-[#D4AF6A] mx-auto rounded-full mb-6"></div>
            <p class="text-stone-600 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
                We are committed to providing a transparent, secure, and hassle-free payment experience. Please read our policy regarding order cancellations and payment refunds.
            </p>
        </div>

        <!-- Policy Content -->
        <div class="bg-white rounded-2xl shadow-xl border border-[#D4AF6A]/30 p-8 md:p-12 space-y-10 text-stone-700">
            
            <section class="space-y-4">
                <h2 class="font-serif-royal text-xl md:text-2xl text-[#4A2C1D] font-semibold border-b border-[#D4AF6A]/20 pb-2 flex items-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    1. Order Cancellations
                </h2>
                <div class="pl-9 space-y-3">
                    <p><strong>Before Dispatch:</strong> You can cancel your order any time before it is dispatched from our warehouse. To request a cancellation, please contact us immediately via WhatsApp or email with your Order ID.</p>
                    <p><strong>After Dispatch:</strong> Once an order has been handed over to our courier partners and an AWB tracking number is generated, it cannot be cancelled. However, you may refuse delivery, and the refund (minus reverse shipping charges, if applicable) will be processed once the item returns to our facility.</p>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="font-serif-royal text-xl md:text-2xl text-[#4A2C1D] font-semibold border-b border-[#D4AF6A]/20 pb-2 flex items-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    2. Refund Processing
                </h2>
                <div class="pl-9 space-y-3">
                    <p>Refunds are initiated only under the following conditions:</p>
                    <ul class="list-disc pl-5 space-y-2 text-stone-600">
                        <li>The order was successfully cancelled before dispatch.</li>
                        <li>A prepaid order was refused at the time of delivery and returned to origin safely.</li>
                        <li>An approved return request for a damaged or defective item where replacement is not possible or out of stock.</li>
                    </ul>
                    <p><strong>Processing Time:</strong> Once your cancellation or return is approved, we will initiate the refund within <strong>2 to 4 business days</strong>.</p>
                    <p><strong>Crediting Time:</strong> Depending on your bank, UPI provider, or credit card issuer, it may take an additional <strong>3 to 7 business days</strong> for the credited amount to reflect in your original payment source.</p>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="font-serif-royal text-xl md:text-2xl text-[#4A2C1D] font-semibold border-b border-[#D4AF6A]/20 pb-2 flex items-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    3. Payment Failures & Double Debits
                </h2>
                <div class="pl-9 space-y-3">
                    <p><strong>Failed Transactions:</strong> If your payment fails during checkout but the amount is debited from your account, the payment gateway typically auto-reverses the transaction within 72 hours. Your order will remain unconfirmed until payment succeeds.</p>
                    <p><strong>Double Debits:</strong> In the rare event that your account is charged twice for a single order, please contact our support team immediately with the transaction proofs. We will manually verify and initiate a refund for the duplicate charge within 48 hours.</p>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="font-serif-royal text-xl md:text-2xl text-[#4A2C1D] font-semibold border-b border-[#D4AF6A]/20 pb-2 flex items-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    4. Get Support
                </h2>
                <div class="pl-9 bg-[#FAF7F0] p-6 rounded-xl border border-[#D4AF6A]/30 flex flex-col md:flex-row md:items-center justify-between gap-4 mt-6">
                    <div>
                        <p class="text-[#4A2C1D] font-medium mb-1">Need help with a refund or cancellation?</p>
                        <p class="text-xs text-stone-500">Have your Order ID ready when contacting our team.</p>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        @if(!empty($storeSettings['whatsapp_url']) || !empty($storeSettings['clean_whatsapp']))
                        <a href="{{ $storeSettings['whatsapp_url'] ?? ('https://wa.me/'.$storeSettings['clean_whatsapp']) }}" class="bg-[#2E180E] text-[#E7C77B] px-4 py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 hover:bg-[#3A1C0E] transition shadow-sm border border-[#D4AF6A]/30">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            Message Support
                        </a>
                        @endif
                        <a href="mailto:{{ $storeSettings['store_email'] ?? 'raykaimitation@gmail.com' }}" class="text-center text-xs text-stone-600 hover:text-[#996E2E] font-medium underline">
                            {{ $storeSettings['store_email'] ?? 'raykaimitation@gmail.com' }}
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection

