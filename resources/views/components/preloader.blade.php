{{-- Rayka Luxury Jewellery Preloader --}}
<style id="rayka-preloader-gate">
    #rayka-preloader { display: none !important; opacity: 0 !important; pointer-events: none !important; }
</style>
<script>
    (function() {
        try {
            if (!sessionStorage.getItem('rayka_seen_intro')) {
                var gate = document.getElementById('rayka-preloader-gate');
                if (gate) gate.remove();
            }
        } catch (e) {}
    })();
</script>
<div id="rayka-preloader" aria-hidden="false" role="status" aria-label="Loading Rayka Imitation Jewellery">
    <style>
        #rayka-preloader {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 48%, #3A1D11 0%, #251209 55%, #140703 100%);
            overflow: hidden;
            user-select: none;
            transition: opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.7s cubic-bezier(0.4, 0, 0.2, 1),
                        visibility 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        #rayka-preloader.rayka-preloader-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: scale(1.03);
        }

        /* Ambient golden particles background glow */
        .rayka-ambient-glow {
            position: absolute;
            width: 650px;
            height: 650px;
            max-width: 95vw;
            max-height: 95vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 106, 0.22) 0%, rgba(153, 110, 46, 0.1) 45%, transparent 70%);
            filter: blur(25px);
            animation: raykaGlowPulse 4s ease-in-out infinite alternate;
            pointer-events: none;
        }

        /* Floating royal sparkles */
        .rayka-sparkle {
            position: absolute;
            color: #E7C77B;
            pointer-events: none;
            opacity: 0;
            animation: raykaSparkleFloat 3.5s ease-in-out infinite;
        }
        .rayka-sparkle-1 { top: 20%; left: 24%; font-size: 18px; animation-delay: 0.2s; }
        .rayka-sparkle-2 { top: 25%; right: 22%; font-size: 24px; animation-delay: 1.1s; }
        .rayka-sparkle-3 { bottom: 22%; left: 26%; font-size: 20px; animation-delay: 1.8s; }
        .rayka-sparkle-4 { bottom: 20%; right: 24%; font-size: 16px; animation-delay: 2.5s; }

        /* Outer Halo Rings */
        .rayka-halo-wrap {
            position: relative;
            width: 180px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem auto;
        }

        @media (min-width: 640px) {
            .rayka-halo-wrap {
                width: 220px;
                height: 220px;
            }
        }

        @media (min-width: 1024px) {
            .rayka-halo-wrap {
                width: 250px;
                height: 250px;
            }
        }

        /* Outer dashed gold halo */
        .rayka-ring-outer {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px dashed rgba(212, 175, 106, 0.65);
            animation: raykaSpinClockwise 18s linear infinite;
        }

        /* Cardinal gem pips on outer ring */
        .rayka-gem-pip {
            position: absolute;
            width: 9px;
            height: 9px;
            background: #FFF2D1;
            border: 1.5px solid #D4AF6A;
            border-radius: 50%;
            box-shadow: 0 0 10px #E7C77B;
            transform: translate(-50%, -50%);
        }

        @media (min-width: 640px) {
            .rayka-gem-pip {
                width: 11px;
                height: 11px;
                box-shadow: 0 0 14px #FFE5A3;
            }
        }

        .rayka-gem-pip-top    { top: 0%; left: 50%; }
        .rayka-gem-pip-right  { top: 50%; left: 100%; }
        .rayka-gem-pip-bottom { top: 100%; left: 50%; }
        .rayka-gem-pip-left   { top: 50%; left: 0%; }

        /* Middle delicate solid counter-rotating ring */
        .rayka-ring-mid {
            position: absolute;
            inset: 10px;
            border-radius: 50%;
            border: 1.5px solid rgba(231, 199, 123, 0.4);
            animation: raykaSpinCounter 12s linear infinite;
        }

        @media (min-width: 640px) {
            .rayka-ring-mid {
                inset: 14px;
            }
        }

        /* Inner Medallion holding Logo */
        .rayka-medallion {
            position: relative;
            width: 136px;
            height: 136px;
            border-radius: 50%;
            background: radial-gradient(circle, #432214 0%, #251208 100%);
            border: 2.5px solid #D4AF6A;
            box-shadow: 0 0 40px rgba(212, 175, 106, 0.45),
                        inset 0 0 25px rgba(212, 175, 106, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            animation: raykaMedallionPulse 3s ease-in-out infinite;
        }

        @media (min-width: 640px) {
            .rayka-medallion {
                width: 168px;
                height: 168px;
                border-width: 3px;
                box-shadow: 0 0 50px rgba(212, 175, 106, 0.5),
                            inset 0 0 30px rgba(212, 175, 106, 0.35);
            }
        }

        @media (min-width: 1024px) {
            .rayka-medallion {
                width: 192px;
                height: 192px;
            }
        }

        /* The Logo Image */
        .rayka-loader-logo {
            width: 96px;
            height: 96px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
            animation: raykaLogoBreathe 3s ease-in-out infinite;
        }

        @media (min-width: 640px) {
            .rayka-loader-logo {
                width: 122px;
                height: 122px;
            }
        }

        @media (min-width: 1024px) {
            .rayka-loader-logo {
                width: 142px;
                height: 142px;
            }
        }

        /* Diamond Glint effect on logo medallion */
        .rayka-glint {
            position: absolute;
            top: 14px;
            right: 18px;
            width: 18px;
            height: 18px;
            pointer-events: none;
            opacity: 0;
            animation: raykaDiamondGlint 3s ease-in-out infinite;
        }

        @media (min-width: 640px) {
            .rayka-glint {
                width: 24px;
                height: 24px;
                top: 18px;
                right: 22px;
            }
        }

        /* Brand Typography */
        .rayka-loader-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.85rem;
            font-weight: 800;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            margin-right: -0.28em;
            text-align: center;
            background: linear-gradient(135deg, #FFF6DD 0%, #E7C77B 25%, #FFFDF7 50%, #D4AF6A 75%, #A8782E 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: raykaGoldShimmer 3s ease-in-out infinite;
            filter: drop-shadow(0 3px 14px rgba(212, 175, 106, 0.35));
            line-height: 1.08;
        }

        @media (min-width: 640px) {
            .rayka-loader-title {
                font-size: 3.8rem;
            }
        }

        @media (min-width: 1024px) {
            .rayka-loader-title {
                font-size: 4.6rem;
            }
        }

        .rayka-loader-subtitle {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.42em;
            text-transform: uppercase;
            margin-right: -0.42em;
            color: #E7C77B;
            text-align: center;
            margin-top: 0.75rem;
            opacity: 0.95;
            text-shadow: 0 0 10px rgba(231, 199, 123, 0.4);
        }

        @media (min-width: 640px) {
            .rayka-loader-subtitle {
                font-size: 1.15rem;
                margin-top: 0.85rem;
            }
        }

        @media (min-width: 1024px) {
            .rayka-loader-subtitle {
                font-size: 1.35rem;
            }
        }

        /* Royal Jewellery Divider */
        .rayka-loader-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            width: 260px;
            margin: 1.5rem auto 1.1rem auto;
        }

        @media (min-width: 640px) {
            .rayka-loader-divider {
                width: 360px;
                margin: 1.75rem auto 1.25rem auto;
            }
        }

        @media (min-width: 1024px) {
            .rayka-loader-divider {
                width: 440px;
            }
        }

        .rayka-divider-line {
            flex: 1;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 106, 0.8), transparent);
        }

        .rayka-divider-diamond {
            color: #E7C77B;
            font-size: 14px;
            text-shadow: 0 0 10px rgba(231, 199, 123, 0.9);
            animation: raykaDiamondPulse 2s ease-in-out infinite;
        }

        /* Slender Gold Progress Line */
        .rayka-progress-track {
            position: relative;
            width: 200px;
            height: 3px;
            background: rgba(212, 175, 106, 0.25);
            border-radius: 999px;
            margin: 0.85rem auto 0 auto;
            overflow: hidden;
        }

        @media (min-width: 640px) {
            .rayka-progress-track {
                width: 280px;
                height: 3.5px;
            }
        }

        @media (min-width: 1024px) {
            .rayka-progress-track {
                width: 340px;
            }
        }

        .rayka-progress-bar {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50%;
            background: linear-gradient(90deg, transparent, #E7C77B, #FFF6D8, #D4AF6A, transparent);
            border-radius: 999px;
            animation: raykaProgressSweep 1.8s ease-in-out infinite;
        }

        .rayka-loader-tagline {
            font-size: 11px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #CBA55B;
            margin-top: 1rem;
            text-align: center;
            margin-right: -0.28em;
            font-weight: 500;
        }

        @media (min-width: 640px) {
            .rayka-loader-tagline {
                font-size: 13px;
                margin-top: 1.15rem;
            }
        }

        @media (min-width: 1024px) {
            .rayka-loader-tagline {
                font-size: 14.5px;
            }
        }

        /* Keyframes */
        @keyframes raykaSpinClockwise {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes raykaSpinCounter {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }

        @keyframes raykaGlowPulse {
            0% { transform: scale(0.92); opacity: 0.7; }
            100% { transform: scale(1.08); opacity: 1; }
        }

        @keyframes raykaMedallionPulse {
            0%, 100% {
                box-shadow: 0 0 25px rgba(212, 175, 106, 0.35), inset 0 0 15px rgba(212, 175, 106, 0.2);
                border-color: #D4AF6A;
            }
            50% {
                box-shadow: 0 0 45px rgba(231, 199, 123, 0.6), inset 0 0 22px rgba(231, 199, 123, 0.35);
                border-color: #F8DE9D;
            }
        }

        @keyframes raykaLogoBreathe {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes raykaDiamondGlint {
            0%, 65%, 100% { opacity: 0; transform: scale(0.3) rotate(0deg); }
            75% { opacity: 1; transform: scale(1.2) rotate(45deg); filter: drop-shadow(0 0 6px #FFF2D1); }
            85% { opacity: 0; transform: scale(0.6) rotate(90deg); }
        }

        @keyframes raykaGoldShimmer {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes raykaDiamondPulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.35); opacity: 1; text-shadow: 0 0 12px #FFF3D1; }
        }

        @keyframes raykaProgressSweep {
            0% { left: -50%; }
            50% { left: 40%; }
            100% { left: 100%; }
        }

        @keyframes raykaSparkleFloat {
            0% { opacity: 0; transform: translateY(6px) scale(0.6); }
            50% { opacity: 0.9; transform: translateY(-4px) scale(1.1); filter: drop-shadow(0 0 6px #E7C77B); }
            100% { opacity: 0; transform: translateY(-12px) scale(0.5); }
        }
    </style>

    <!-- Center Ambient Glow -->
    <div class="rayka-ambient-glow"></div>

    <!-- Floating Jewellery Sparkles -->
    <span class="rayka-sparkle rayka-sparkle-1"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A11.89 11.89 0 0112 15a11.89 11.89 0 01-4.416-.85A12.974 12.974 0 011.5 21.75a.75.75 0 01-1.16-.886 14.473 14.473 0 005.06-5.06C2.518 12.062 0 7.378 0 2.25a.75.75 0 01.75-.75c5.128 0 9.812 2.518 12.936 6.584zm4.195 5.324a10.373 10.373 0 00-2.493-2.493c-1.84 1.341-4.103 2.135-6.527 2.135 1.25.755 2.381 1.69 3.327 2.766-1.503 1.1-3.23 1.83-5.06 2.135a12.974 12.974 0 013.344-1.78A10.373 10.373 0 0013.51 12.91z" clip-rule="evenodd" /></svg></span>
    <span class="rayka-sparkle rayka-sparkle-2"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A11.89 11.89 0 0112 15a11.89 11.89 0 01-4.416-.85A12.974 12.974 0 011.5 21.75a.75.75 0 01-1.16-.886 14.473 14.473 0 005.06-5.06C2.518 12.062 0 7.378 0 2.25a.75.75 0 01.75-.75c5.128 0 9.812 2.518 12.936 6.584zm4.195 5.324a10.373 10.373 0 00-2.493-2.493c-1.84 1.341-4.103 2.135-6.527 2.135 1.25.755 2.381 1.69 3.327 2.766-1.503 1.1-3.23 1.83-5.06 2.135a12.974 12.974 0 013.344-1.78A10.373 10.373 0 0013.51 12.91z" clip-rule="evenodd" /></svg></span>
    <span class="rayka-sparkle rayka-sparkle-3"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A11.89 11.89 0 0112 15a11.89 11.89 0 01-4.416-.85A12.974 12.974 0 011.5 21.75a.75.75 0 01-1.16-.886 14.473 14.473 0 005.06-5.06C2.518 12.062 0 7.378 0 2.25a.75.75 0 01.75-.75c5.128 0 9.812 2.518 12.936 6.584zm4.195 5.324a10.373 10.373 0 00-2.493-2.493c-1.84 1.341-4.103 2.135-6.527 2.135 1.25.755 2.381 1.69 3.327 2.766-1.503 1.1-3.23 1.83-5.06 2.135a12.974 12.974 0 013.344-1.78A10.373 10.373 0 0013.51 12.91z" clip-rule="evenodd" /></svg></span>
    <span class="rayka-sparkle rayka-sparkle-4"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A11.89 11.89 0 0112 15a11.89 11.89 0 01-4.416-.85A12.974 12.974 0 011.5 21.75a.75.75 0 01-1.16-.886 14.473 14.473 0 005.06-5.06C2.518 12.062 0 7.378 0 2.25a.75.75 0 01.75-.75c5.128 0 9.812 2.518 12.936 6.584zm4.195 5.324a10.373 10.373 0 00-2.493-2.493c-1.84 1.341-4.103 2.135-6.527 2.135 1.25.755 2.381 1.69 3.327 2.766-1.503 1.1-3.23 1.83-5.06 2.135a12.974 12.974 0 013.344-1.78A10.373 10.373 0 0013.51 12.91z" clip-rule="evenodd" /></svg></span>

    <!-- Preloader Center Stage -->
    <div style="position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; max-width: 90vw; padding: 1.5rem;">
        
        <!-- Royal Jewellery Halo with Brand Logo -->
        <div class="rayka-halo-wrap">
            <!-- Outer rotating halo with 4 cardinal gems -->
            <div class="rayka-ring-outer">
                <span class="rayka-gem-pip rayka-gem-pip-top"></span>
                <span class="rayka-gem-pip rayka-gem-pip-right"></span>
                <span class="rayka-gem-pip rayka-gem-pip-bottom"></span>
                <span class="rayka-gem-pip rayka-gem-pip-left"></span>
            </div>

            <!-- Middle counter-rotating ring -->
            <div class="rayka-ring-mid"></div>

            <!-- Inner Medallion holding Logo -->
            <div class="rayka-medallion">
                <img src="{{ asset('images/rayka-logo.png') }}" 
                     alt="Rayka" 
                     class="rayka-loader-logo"
                     loading="eager"
                     decoding="sync">

                <!-- Sparkle glint on top-right -->
                <svg class="rayka-glint" viewBox="0 0 24 24" fill="#FFF2D1">
                    <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
                </svg>
            </div>
        </div>

        <!-- Brand Name & Identity -->
        <h1 class="rayka-loader-title">RAYKA</h1>
        <p class="rayka-loader-subtitle">Imitation Jewellery</p>

        <!-- Royal Divider -->
        <div class="rayka-loader-divider">
            <div class="rayka-divider-line"></div>
            <span class="rayka-divider-diamond"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A11.89 11.89 0 0112 15a11.89 11.89 0 01-4.416-.85A12.974 12.974 0 011.5 21.75a.75.75 0 01-1.16-.886 14.473 14.473 0 005.06-5.06C2.518 12.062 0 7.378 0 2.25a.75.75 0 01.75-.75c5.128 0 9.812 2.518 12.936 6.584zm4.195 5.324a10.373 10.373 0 00-2.493-2.493c-1.84 1.341-4.103 2.135-6.527 2.135 1.25.755 2.381 1.69 3.327 2.766-1.503 1.1-3.23 1.83-5.06 2.135a12.974 12.974 0 013.344-1.78A10.373 10.373 0 0013.51 12.91z" clip-rule="evenodd" /></svg></span>
            <div class="rayka-divider-line"></div>
        </div>

        <!-- Sleek Animated Golden Progress Line -->
        <div class="rayka-progress-track">
            <div class="rayka-progress-bar"></div>
        </div>

        <p class="rayka-loader-tagline">Royal Heritage & 1 Gram Gold</p>
    </div>

    <script>
        (function() {
            var preloader = document.getElementById('rayka-preloader');
            if (!preloader) return;

            // If user has already seen the intro animation in this session, skip immediately
            try {
                if (sessionStorage.getItem('rayka_seen_intro')) {
                    preloader.style.display = 'none';
                    if (preloader.parentNode) {
                        preloader.parentNode.removeChild(preloader);
                    }
                    return;
                }
                sessionStorage.setItem('rayka_seen_intro', '1');
            } catch (e) {
                // In case of private browsing storage restrictions
            }

            var startTime = performance.now();
            var minDisplayTime = 600; // Brief presentation on first visit
            var maxSafetyTime = 1800; // Fail-safe timeout

            function dismissLoader() {
                if (!preloader || preloader.classList.contains('rayka-preloader-hidden')) return;

                var elapsed = performance.now() - startTime;
                var remaining = Math.max(0, minDisplayTime - elapsed);

                setTimeout(function() {
                    preloader.classList.add('rayka-preloader-hidden');
                    setTimeout(function() {
                        if (preloader && preloader.parentNode) {
                            preloader.parentNode.removeChild(preloader);
                        }
                    }, 500);
                }, remaining);
            }

            if (document.readyState === 'complete') {
                dismissLoader();
            } else {
                window.addEventListener('load', dismissLoader);
            }

            // Safety fallback timeout
            setTimeout(dismissLoader, maxSafetyTime);
        })();
    </script>
</div>
