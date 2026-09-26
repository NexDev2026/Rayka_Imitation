@extends('layouts.storefront')

@section('title', 'Frequently Asked Questions (FAQ) — Rayka Imitation Jewellery')
@section('meta_description', 'Find answers to common questions about Rayka Imitation Jewellery: 1 Gram Micro Gold plating, care, express shipping across India, payments, and our 7-day replacement guarantee.')

@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "What is 1 Gram Micro Gold Plating and how long does it last?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "1 Gram Micro Gold Plating is an advanced electroplating process where authentic 24-karat gold of 1 to 2 micron thickness is bonded over high-grade jeweller's copper and brass alloys. With proper care (keeping it away from water, perfumes, and harsh chemicals), the royal shine and lustre last between 1 to 3 years."
      }
    },
    {
      "@@type": "Question",
      "name": "Is Rayka Imitation Jewellery safe for sensitive skin?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "Yes, absolutely. All Rayka jewellery pieces are 100% lead-free, nickel-free, and crafted with skin-safe, hypoallergenic base alloys to prevent irritation, rashes, or skin discoloration even during long wedding wear."
      }
    },
    {
      "@@type": "Question",
      "name": "How long does shipping take across India?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "Orders are processed and dispatched within 24 to 48 hours from our atelier in Ahmedabad, Gujarat. Delivery takes 2 to 4 business days for major metro cities (Mumbai, Delhi, Bangalore, Hyderabad, etc.) and 4 to 6 business days for the rest of India. All orders above ₹999 qualify for Free Express Shipping."
      }
    },
    {
      "@@type": "Question",
      "name": "How does payment via Static QR / Direct UPI work?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "During checkout, you will see Rayka's official verified Static UPI QR Code. Scan it using Google Pay, PhonePe, Paytm, or BHIM UPI, complete the payment, and upload the transaction screenshot or UTR number. Our concierge team validates the transaction and confirms your order with tracking details."
      }
    },
    {
      "@@type": "Question",
      "name": "What is your 7-Day Replacement Guarantee?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "If your order arrives damaged, defective, or incorrect, you are covered by our 7-Day Replacement Guarantee. Simply record a brief unboxing video upon delivery and share it with our WhatsApp concierge{{ !empty($storeSettings['store_whatsapp']) ? ' (' . $storeSettings['store_whatsapp'] . ')' : '' }} within 7 days. We will dispatch a brand-new replacement at zero additional shipping cost."
      }
    },
    {
      "@@type": "Question",
      "name": "Can I visit your physical showroom in Ahmedabad?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "Yes! You are most welcome to visit our physical showroom located at Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad - 380026, Gujarat. Our store is open Monday to Saturday from 10:00 AM to 8:30 PM."
      }
    }
  ]
}
</script>
@endpush

@section('content')
<div class="bg-[#FAF7F0] min-h-screen py-12 sm:py-16" x-data="{ activeTab: 'all', activeAccordion: 1 }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center mb-10 sm:mb-14">
            <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Help & Guidance</span>
            <h1 class="font-serif-royal text-3xl sm:text-4xl md:text-5xl text-[#4A2C1D] font-bold mt-1 mb-4">
                Frequently Asked Questions
            </h1>
            <div class="h-1 w-24 bg-gradient-to-r from-[#D4AF6A] to-[#996E2E] mx-auto rounded-full mb-6"></div>
            <p class="text-stone-600 text-xs sm:text-sm max-w-2xl mx-auto leading-relaxed">
                Everything you need to know about our royal 1-gram gold collections, plating durability, express dispatch, safe UPI payments, and replacement guarantees.
            </p>

            <!-- Category Filter Tabs -->
            <div class="flex items-center justify-center flex-wrap gap-2 mt-8">
                <button type="button" @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'bg-[#4A2C1D] text-[#E7C77B] shadow-md border-[#4A2C1D]' : 'bg-white text-stone-600 hover:text-[#4A2C1D] border-stone-200'"
                        class="px-4 py-2 rounded-full text-xs font-semibold border transition cursor-pointer">
                    All Questions
                </button>
                <button type="button" @click="activeTab = 'quality'" 
                        :class="activeTab === 'quality' ? 'bg-[#4A2C1D] text-[#E7C77B] shadow-md border-[#4A2C1D]' : 'bg-white text-stone-600 hover:text-[#4A2C1D] border-stone-200'"
                        class="px-4 py-2 rounded-full text-xs font-semibold border transition cursor-pointer">
                    1 Gram Gold & Quality
                </button>
                <button type="button" @click="activeTab = 'shipping'" 
                        :class="activeTab === 'shipping' ? 'bg-[#4A2C1D] text-[#E7C77B] shadow-md border-[#4A2C1D]' : 'bg-white text-stone-600 hover:text-[#4A2C1D] border-stone-200'"
                        class="px-4 py-2 rounded-full text-xs font-semibold border transition cursor-pointer">
                    Shipping & Delivery
                </button>
                <button type="button" @click="activeTab = 'payments'" 
                        :class="activeTab === 'payments' ? 'bg-[#4A2C1D] text-[#E7C77B] shadow-md border-[#4A2C1D]' : 'bg-white text-stone-600 hover:text-[#4A2C1D] border-stone-200'"
                        class="px-4 py-2 rounded-full text-xs font-semibold border transition cursor-pointer">
                    Payments & Security
                </button>
                <button type="button" @click="activeTab = 'returns'" 
                        :class="activeTab === 'returns' ? 'bg-[#4A2C1D] text-[#E7C77B] shadow-md border-[#4A2C1D]' : 'bg-white text-stone-600 hover:text-[#4A2C1D] border-stone-200'"
                        class="px-4 py-2 rounded-full text-xs font-semibold border transition cursor-pointer">
                    Replacements & Refunds
                </button>
            </div>
        </div>

        <!-- Accordion Q&A Container -->
        <div class="space-y-4">

            <!-- 1. Gold Plating -->
            <div x-show="activeTab === 'all' || activeTab === 'quality'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 1 ? null : 1" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        What is 1 Gram Micro Gold Plating and how long does it last?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 1 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 1" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <p class="mb-2">
                        1 Gram Micro Gold Plating is an electro-chemical plating technique where pure 24-karat gold layer (1 to 2 micron thickness) is bonded onto pure brass or copper alloy jewellery. This creates the exact hue, mirror-gloss, and royal weight of solid hallmarked gold jewellery.
                    </p>
                    <p>
                        With regular Indian festive and occasion use, the plating retains its royal lustre for <strong>1 to 3 years</strong>. To maximize lifespan, store in an airtight box and keep away from liquid perfumes, sanitizer, and sweat.
                    </p>
                </div>
            </div>

            <!-- 2. Skin Safety -->
            <div x-show="activeTab === 'all' || activeTab === 'quality'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 2 ? null : 2" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        Is Rayka Imitation Jewellery safe for sensitive skin?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 2 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 2" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <p>
                        Yes, 100%. All Rayka jewellery pieces are crafted with <strong>anti-allergic, nickel-free, and lead-free</strong> metallurgy. Even patrons with sensitive skin can wear our chokers, chains, and kadas comfortably throughout long wedding ceremonies without itching, redness, or skin darkening.
                    </p>
                </div>
            </div>

            <!-- 3. Express Shipping & Delivery -->
            <div x-show="activeTab === 'all' || activeTab === 'shipping'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 3 ? null : 3" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        How long does express delivery take across India?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 3 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 3" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <ul class="list-disc pl-5 space-y-1.5 mb-2">
                        <li><strong>Metro Cities (Mumbai, Delhi, Bengaluru, Hyderabad, Jaipur, Pune):</strong> 2 to 4 business days.</li>
                        <li><strong>Gujarat State Deliveries:</strong> 1 to 2 business days.</li>
                        <li><strong>Rest of India:</strong> 4 to 6 business days.</li>
                    </ul>
                    <p>
                        Every parcel is packed in a royal craft jewellery box and sealed in a tamper-evident outer bubble courier bag. Orders above ₹999 receive <strong>FREE Express Shipping</strong>.
                    </p>
                </div>
            </div>

            <!-- 4. Order Tracking -->
            <div x-show="activeTab === 'all' || activeTab === 'shipping'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 4 ? null : 4" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        How can I track my parcel once dispatched?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 4 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 4" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <p>
                        As soon as your parcel is handed over to our courier partners (BlueDart, Delhivery, DTDC, or Express Cargo), an SMS & WhatsApp notification with the live tracking AWB is dispatched to your registered mobile. You can also visit our <a href="{{ route('order.track') }}" class="text-[#996E2E] font-bold underline">Live Order Tracking Portal</a> anytime by entering your Order Number.
                    </p>
                </div>
            </div>

            <!-- 5. Payment Security -->
            <div x-show="activeTab === 'all' || activeTab === 'payments'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 5 ? null : 5" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        How does payment via Static QR / UPI verification work?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 5 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 5" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <p class="mb-2">
                        To offer zero gateway surcharges and ensure direct security, we support instant verified UPI payments (Google Pay, PhonePe, Paytm, BHIM, CRED).
                    </p>
                    <ol class="list-decimal pl-5 space-y-1">
                        <li>During checkout, scan the verified Rayka Jewellery QR Code displayed on screen.</li>
                        <li>Pay the exact order amount and note your 12-digit UTR/Transaction ID.</li>
                        <li>Upload your payment screenshot on the checkout page.</li>
                        <li>Our automated portal matches the transaction and confirms your order immediately.</li>
                    </ol>
                </div>
            </div>

            <!-- 6. 7-Day Replacement Guarantee -->
            <div x-show="activeTab === 'all' || activeTab === 'returns'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 6 ? null : 6" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        What is your 7-Day Replacement Guarantee?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 6 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 6" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <p class="mb-2">
                        We offer a comprehensive <strong>7-Day Replacement Guarantee</strong> for any transit breakage, stone displacement, or manufacturing defect.
                    </p>
                    <p>
                        To claim a replacement, simply record a 30-second continuous parcel unboxing video when your package arrives and share it with our WhatsApp concierge{{ !empty($storeSettings['store_whatsapp']) ? ' (' . $storeSettings['store_whatsapp'] . ')' : '' }}. Once validated, a brand new replacement unit will be dispatched to your doorstep free of charge. For complete details, see our <a href="{{ route('policy', 'return-replacement-policy') }}" class="text-[#996E2E] font-bold underline">Replacement Policy</a>.
                    </p>
                </div>
            </div>

            <!-- 7. Showroom Visit -->
            <div x-show="activeTab === 'all' || activeTab === 'quality'" 
                 class="bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs transition-all duration-200">
                <button type="button" 
                        @click="activeAccordion = activeAccordion === 7 ? null : 7" 
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-stone-50/80 transition">
                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        Can I visit your physical showroom in Ahmedabad?
                    </span>
                    <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E] font-bold text-sm"
                          x-text="activeAccordion === 7 ? '−' : '+'"></span>
                </button>
                <div x-show="activeAccordion === 7" x-collapse class="px-6 pb-6 pt-1 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
                    <p class="mb-2">
                        Yes, we welcome all our patrons! You can visit our flagship atelier showroom in Ahmedabad:
                    </p>
                    <p class="font-semibold text-[#4A2C1D]">
                        Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad - 380026, Gujarat.
                    </p>
                    <p class="mt-2 text-stone-500">
                        Operating Hours: Monday – Saturday: 10:00 AM – 8:30 PM | Sunday: 11:00 AM – 5:00 PM.
                    </p>
                </div>
            </div>

        </div>

        <!-- Concierge Helpdesk Callout Box -->
        <div class="mt-12 bg-gradient-to-r from-[#4A2C1D] via-[#3A1C0E] to-[#2E180E] text-[#FAF7F0] rounded-3xl p-8 sm:p-10 border border-[#D4AF6A] shadow-xl text-center relative overflow-hidden">
            <div class="relative z-10 max-w-xl mx-auto space-y-4">
                <div class="w-12 h-12 rounded-full bg-[#FAF7F0]/10 border border-[#D4AF6A] flex items-center justify-center mx-auto text-[#E7C77B]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#E7C77B]">
                    Still Have Questions?
                </h3>
                <p class="text-xs sm:text-sm text-stone-200 leading-relaxed font-light">
                    Our dedicated royal jewellery concierge is available on WhatsApp for custom sizing, bridal curations, and real-time order inquiries.
                </p>
                <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                    @if(!empty($storeSettings['whatsapp_url']) || !empty($storeSettings['clean_whatsapp']))
                    <a href="{{ $storeSettings['whatsapp_url'] ?? ('https://wa.me/'.$storeSettings['clean_whatsapp']) }}?text=Hello%20Rayka%20Jewellery%2C%20I%20have%20a%20question%20regarding%20my%20order." 
                       target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center space-x-2 bg-[#25D366] hover:bg-[#20ba5a] text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition shadow-md hover:scale-105 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Chat on WhatsApp</span>
                    </a>
                    @endif
                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 border border-[#D4AF6A] text-[#FAF7F0] px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition">
                        <span>Contact Page</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
