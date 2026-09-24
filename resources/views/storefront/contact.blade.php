@extends('layouts.storefront')

@section('title', 'Contact Us & Concierge — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10 sm:space-y-12">

    <div class="text-center max-w-xl mx-auto">
        <span class="text-[11px] sm:text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Boutique Concierge</span>
        <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D] mt-1">
            Contact Rayka Jewellery
        </h1>
        <div class="rangoli-divider">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
        </div>
        <p class="text-xs text-stone-600">
            Have questions about bridal customization, sizing, or 1 gram micro plating warranty? Our jewelry specialists are at your service.
        </p>
    </div>

    @if(session('success'))
        <div class="max-w-2xl mx-auto p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
        
        <!-- Left: Contact Details & Boutique Info (5 cols) -->
        <div class="lg:col-span-5 bg-[#FAF7F0] rounded-2xl border border-[#D4AF6A]/40 p-5 sm:p-8 space-y-6 shadow-xs">
            <div class="border-b border-[#D4AF6A]/30 pb-4">
                <span class="text-[10px] uppercase font-bold tracking-widest text-[#996E2E]">Flagship Atelier</span>
                <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D] mt-0.5">
                    The Heritage Atelier
                </h3>
            </div>

            <div class="space-y-4 text-xs">
                <!-- Boutique Address -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <strong class="block text-[#4A2C1D] font-semibold">Boutique Address:</strong>
                        <p class="text-stone-600 leading-relaxed mt-0.5">{{ $storeAddress }}</p>
                        @if(!empty($storeGoogleMapUrl))
                            <a href="{{ $storeGoogleMapUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-white border border-[#D4AF6A] text-[#996E2E] rounded-md font-semibold text-[11px] hover:bg-[#FAF7F0] transition shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                <span>Get Directions</span>
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Instagram -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-pink-600 shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                    <div>
                        <strong class="block text-[#4A2C1D] font-semibold">Instagram Profile:</strong>
                        <a href="{{ $storeInstagramUrl ?: 'https://www.instagram.com/rayka_imitation_amdavad/?hl=en' }}" target="_blank" rel="noopener noreferrer" class="text-[#D4AF6A] font-semibold hover:underline flex items-center gap-1 mt-0.5">
                            <span class="text-pink-600 font-bold">@rayka_imitation_amdavad</span>
                            <span class="text-[10px] text-stone-500">(Latest designs & reels)</span>
                        </a>
                    </div>
                </div>

                <!-- Helpline -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div>
                        <strong class="block text-[#4A2C1D] font-semibold">Telephone & Helpline:</strong>
                        <div class="space-y-0.5 mt-0.5">
                            <a href="tel:{{ $storePhone }}" class="text-[#996E2E] hover:underline font-medium block">{{ $storePhone }}</a>
                            @if(!empty($storeAltPhone))
                                <a href="tel:{{ $storeAltPhone }}" class="text-stone-600 hover:text-[#996E2E] hover:underline block text-[11px]">Alt: {{ $storeAltPhone }}</a>
                            @endif
                        </div>
                        <p class="text-[10px] text-stone-400 mt-1">Monday to Saturday (10:00 AM – 8:00 PM IST)</p>
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-emerald-600 shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <strong class="block text-[#4A2C1D] font-semibold">WhatsApp Concierge & Orders:</strong>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWhatsapp ?: '918128498531') }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-700 font-semibold hover:underline mt-0.5">
                            <span>Chat on WhatsApp ({{ $storeWhatsapp }})</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                        </a>
                    </div>
                </div>

                <!-- Email -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <strong class="block text-[#4A2C1D] font-semibold">Customer Support Email:</strong>
                        <a href="mailto:{{ $storeEmail }}" class="text-[#996E2E] hover:underline block mt-0.5">{{ $storeEmail ?: 'care@raykajewellery.com' }}</a>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-[#D4AF6A]/30">
                <p class="text-[11px] text-stone-500 italic">
                    "Visit our boutique at Vastral, Ahmedabad or order online across India with 100% guarantee."
                </p>
            </div>
        </div>

        <!-- Right: Inquiry Form (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-[#D4AF6A]/40 p-5 sm:p-8 shadow-xs space-y-6">
            <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D] pb-3 border-b border-[#D4AF6A]/30">
                Send a Message to Our Concierge
            </h3>

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Your Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Ananya Sharma" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="e.g. ananya@example.com" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Mobile Number</label>
                        <input type="tel" name="mobile" placeholder="e.g. 9876543210" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Subject</label>
                        <input type="text" name="subject" placeholder="e.g. Bridal Jewellery Booking" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Your Message / Inquiry *</label>
                    <textarea name="message" rows="5" required placeholder="Tell us how we may assist you..." class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden"></textarea>
                </div>

                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-full font-bold text-xs uppercase tracking-wider hover:shadow-lg transition border border-[#D4AF6A]">
                    <span class="flex items-center gap-1.5">Submit Message <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                </button>
            </form>
        </div>

    </div>

    <!-- Interactive Showroom Location Google Map Section -->
    <div class="bg-white rounded-2xl border-2 border-[#D4AF6A]/60 overflow-hidden shadow-md">
        <div class="p-4 sm:p-6 bg-gradient-to-r from-[#FAF7F0] via-white to-[#FAF7F0] border-b border-[#D4AF6A]/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-[#996E2E]">Showroom Boutique Location</span>
                </div>
                <h3 class="font-serif-royal text-lg sm:text-xl font-bold text-[#4A2C1D] mt-0.5">
                    Rayka Imitation Jewellery — Vastral, Ahmedabad
                </h3>
                <p class="text-xs text-stone-500 mt-0.5">Open Monday – Saturday, 10:00 AM – 8:00 PM IST</p>
            </div>
            <a href="https://www.google.com/maps/place/Rayka+imitation/@23.0040348,72.6434239,15z" target="_blank" rel="noopener noreferrer" 
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#4A2C1D] text-[#E7C77B] font-bold text-xs hover:bg-[#2E180E] transition shadow-xs border border-[#D4AF6A] shrink-0">
                <svg class="w-4 h-4 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Open in Google Maps</span>
            </a>
        </div>

        <div class="relative w-full h-80 sm:h-96 bg-stone-100">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3672.5373373214575!2d72.6434239!3d23.0040348!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e87002777fe9f%3A0x806339db8988a306!2sRayka%20imitation!5e0!3m2!1sen!2sin!4v1789625982624!5m2!1sen!2sin"
                class="w-full h-full border-0"
                loading="lazy"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"
                title="Rayka Boutique Location Map">
            </iframe>
        </div>
    </div>

</div>
@endsection

