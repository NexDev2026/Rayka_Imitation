{{-- Rayka Royal Jewellery Care & Longevity Guide Component --}}
<div class="rounded-2xl bg-[#FAF7F0] border border-[#D4AF6A]/50 p-5 sm:p-7 shadow-xs space-y-6" x-data="{ careLang: 'en' }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#D4AF6A]/30 gap-3">
        <div>
            <div class="flex items-center space-x-2">
                <div class="p-1.5 bg-[#FAF7F0] border border-[#D4AF6A] rounded-lg">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                </div>
                <h3 class="font-serif-royal text-lg sm:text-xl font-bold text-[#4A2C1D]">
                    Royal Jewellery Care & Safety Guide
                </h3>
            </div>
            <p class="text-xs text-stone-600 mt-1 max-w-2xl leading-relaxed">
                Follow these simple care rules to keep your gold plated jewellery shiny for longer. Plating life depends mainly on water, sweat, chemicals, and proper storage.
            </p>
        </div>

        <!-- Language Switcher Pills -->
        <div class="inline-flex items-center p-1 bg-white rounded-xl border border-[#D4AF6A]/40 shrink-0 self-start sm:self-center shadow-2xs">
            <button type="button" 
                    @click="careLang = 'en'" 
                    :class="{ 'bg-[#4A2C1D] text-[#E7C77B] font-bold shadow-xs': careLang === 'en', 'text-stone-600 hover:text-[#4A2C1D] font-medium': careLang !== 'en' }" 
                    class="px-3 py-1.5 rounded-lg text-xs transition flex items-center space-x-1.5">
                <span>EN</span>
                <span>English</span>
            </button>
            <button type="button" 
                    @click="careLang = 'hi'" 
                    :class="{ 'bg-[#4A2C1D] text-[#E7C77B] font-bold shadow-xs': careLang === 'hi', 'text-stone-600 hover:text-[#4A2C1D] font-medium': careLang !== 'hi' }" 
                    class="px-3 py-1.5 rounded-lg text-xs transition flex items-center space-x-1.5">
                <span>HI</span>
                <span>हिंदी</span>
            </button>
        </div>
    </div>

    <!-- 3 Quick Rule Highlight Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Card 1: Best Rule -->
        <div class="p-4 rounded-xl bg-white border border-[#D4AF6A]/40 shadow-2xs flex items-start space-x-3 hover:border-[#D4AF6A] transition">
            <div class="w-10 h-10 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-[#996E2E] block">
                    <span x-show="careLang === 'en'">Best Golden Rule</span>
                    <span x-show="careLang === 'hi'">सबसे जरूरी नियम</span>
                </span>
                <p class="text-xs sm:text-sm font-bold text-[#4A2C1D] mt-0.5">
                    <span x-show="careLang === 'en'">Wear last • Remove first</span>
                    <span x-show="careLang === 'hi'">लास्ट में पहनें • पहले उतारें</span>
                </p>
                <p class="text-[11px] text-stone-500 mt-1">
                    <span x-show="careLang === 'en'">Wear after makeup dries; remove before sleep</span>
                    <span x-show="careLang === 'hi'">मेकअप/परफ्यूम के बाद पहनें; सोने से पहले उतारें</span>
                </p>
            </div>
        </div>

        <!-- Card 2: Avoid -->
        <div class="p-4 rounded-xl bg-white border border-[#D4AF6A]/40 shadow-2xs flex items-start space-x-3 hover:border-[#D4AF6A] transition">
            <div class="w-10 h-10 rounded-full bg-rose-50 border border-rose-300 flex items-center justify-center shrink-0 text-rose-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-rose-800 block">
                    <span x-show="careLang === 'en'">Strictly Avoid</span>
                    <span x-show="careLang === 'hi'">दूर रखें (बचाएं)</span>
                </span>
                <p class="text-xs sm:text-sm font-bold text-[#4A2C1D] mt-0.5">
                    <span x-show="careLang === 'en'">Water • Sweat • Perfume</span>
                    <span x-show="careLang === 'hi'">पानी • पसीना • परफ्यूम</span>
                </p>
                <p class="text-[11px] text-stone-500 mt-1">
                    <span x-show="careLang === 'en'">Chemicals and moisture reduce plating shine</span>
                    <span x-show="careLang === 'hi'">केमिकल और नमी से प्लेटिंग की चमक कम होती है</span>
                </p>
            </div>
        </div>

        <!-- Card 3: Storage -->
        <div class="p-4 rounded-xl bg-white border border-[#D4AF6A]/40 shadow-2xs flex items-start space-x-3 hover:border-[#D4AF6A] transition">
            <div class="w-10 h-10 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center shrink-0 text-[#996E2E]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-[#996E2E] block">
                    <span x-show="careLang === 'en'">Safe Storage</span>
                    <span x-show="careLang === 'hi'">सुरक्षित स्टोरेज</span>
                </span>
                <p class="text-xs sm:text-sm font-bold text-[#4A2C1D] mt-0.5">
                    <span x-show="careLang === 'en'">Separate pouch / box</span>
                    <span x-show="careLang === 'hi'">अलग पाउच या बॉक्स</span>
                </p>
                <p class="text-[11px] text-stone-500 mt-1">
                    <span x-show="careLang === 'en'">Prevents tangles, scratches & rubbing</span>
                    <span x-show="careLang === 'hi'">स्क्रैच, रगड़ और उलझने से बचाएं</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Detailed Checklist: English (EN) -->
    <div x-show="careLang === 'en'" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-1" 
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-3">
        <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] flex items-center space-x-2">
            <svg class="w-4 h-4 text-[#996E2E]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <span>Care Tips (English)</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs text-stone-700">
            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#4A8EC2] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg></span>
                <p><strong>Keep away from water:</strong> Remove before bath, swimming, rain, or washing face/hands.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#8B5CF6] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg></span>
                <p><strong>Avoid chemicals:</strong> Perfume, deodorant, sanitizer, hairspray, and cleaning liquids can dull the shine.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#059669] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></span>
                <p><strong>Remove before workout:</strong> Body sweat reduces gold plating life faster.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#996E2E] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></span>
                <p><strong>Wear last, remove first:</strong> Wear after perfume/makeup dries; remove before sleeping.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#D4AF6A] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z"/></svg></span>
                <p><strong>Wipe after use:</strong> Gently clean with a soft, dry cotton cloth every time.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#4A2C1D] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg></span>
                <p><strong>Store separately:</strong> Keep in a pouch/box to avoid scratches, rubbing, and tangling.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-rose-600 mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg></span>
                <p><strong>No harsh cleaning:</strong> Don't use toothpaste, brushes, or strong chemical cleaners.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-amber-500 mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg></span>
                <p><strong>If it gets wet:</strong> Dry immediately with a soft cloth and store only when fully dry.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-orange-600 mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c0 6-6 7-6 13a6 6 0 0012 0c0-6-6-7-6-13z"/><path d="M9.5 20a3 3 0 005 0"/></svg></span>
                <p><strong>Avoid heat:</strong> Keep away from long direct sunlight exposure and high heat areas.</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#996E2E] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><path d="M12 6a6 6 0 100 12 6 6 0 000-12z"/><line x1="12" y1="18" x2="12" y2="22"/></svg></span>
                <p><strong>Quick tip:</strong> If you wear your jewellery daily, rotate with another piece to reduce plating wear.</p>
            </div>
        </div>
    </div>

    <!-- Detailed Checklist: Hindi (HI) -->
    <div x-show="careLang === 'hi'" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-1" 
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-3"
         style="display: none;">
        <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] flex items-center space-x-2">
            <svg class="w-4 h-4 text-[#996E2E]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <span>देखभाल के टिप्स (Hindi)</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs text-stone-700">
            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#4A8EC2] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg></span>
                <p><strong>पानी से दूर रखें:</strong> नहाने, स्विमिंग, बारिश या फेस/हैंड वॉश के समय उतार दें।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#8B5CF6] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg></span>
                <p><strong>केमिकल्स से बचाएं:</strong> परफ्यूम, डियो, सैनिटाइज़र, हेयर स्प्रे और क्लीनिंग लिक्विड से चमक कम हो सकती है।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#059669] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></span>
                <p><strong>वर्कआउट से पहले उतार दें:</strong> पसीने से प्लेटिंग जल्दी फीकी हो सकती है।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#996E2E] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></span>
                <p><strong>लास्ट में पहनें, पहले उतारें:</strong> परफ्यूम/मेकअप सूखने के बाद पहनें; सोने से पहले उतार दें।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#D4AF6A] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z"/></svg></span>
                <p><strong>यूज़ के बाद पोंछें:</strong> हर बार सॉफ्ट सूखे कपड़े से हल्का पोंछ लें।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#4A2C1D] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg></span>
                <p><strong>अलग स्टोर करें:</strong> स्क्रैच/रगड़ और उलझने से बचाने के लिए अलग पाउच/बॉक्स में रखें।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-rose-600 mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg></span>
                <p><strong>हार्श क्लीनिंग न करें:</strong> टूथपेस्ट, ब्रश या स्ट्रॉन्ग क्लीनर का उपयोग न करें।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-amber-500 mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg></span>
                <p><strong>गीली हो जाए तो:</strong> तुरंत सुखाकर ही स्टोर करें (पूरी तरह सूखने के बाद)।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-orange-600 mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c0 6-6 7-6 13a6 6 0 0012 0c0-6-6-7-6-13z"/><path d="M9.5 20a3 3 0 005 0"/></svg></span>
                <p><strong>गर्मी से बचाएं:</strong> ज्यादा धूप और हाई-हीट जगहों से दूर रखें।</p>
            </div>

            <div class="flex items-start space-x-2.5 p-3 rounded-lg bg-white border border-[#D4AF6A]/25">
                <span class="shrink-0 w-5 h-5 text-[#996E2E] mt-0.5"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><path d="M12 6a6 6 0 100 12 6 6 0 000-12z"/><line x1="12" y1="18" x2="12" y2="22"/></svg></span>
                <p><strong>टिप:</strong> रोज़ पहनते हैं तो दूसरे चेन के साथ रोटेशन रखें ताकि प्लेटिंग लाइफ बेहतर रहे।</p>
            </div>
        </div>
    </div>

    <!-- Bottom Golden Promise Banner -->
    <div class="p-3.5 sm:p-4 rounded-xl bg-gradient-to-r from-[#2E180E] via-[#4A2C1D] to-[#2E180E] border border-[#D4AF6A] text-[#FAF7F0] flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left shadow-xs">
        <div class="flex items-center space-x-2 text-[#E7C77B] font-bold text-xs sm:text-sm">
            <svg class="w-4 h-4 text-[#E7C77B]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <span>Small habits = longer shine</span>
        </div>
        <p class="text-[11px] sm:text-xs text-stone-200">
            Gold plated jewellery stays premium for longer when it's kept dry, cleaned gently, and stored safely.
        </p>
    </div>

</div>
