@extends('layouts.storefront')

@section('title', 'Register — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-12" x-data="registerPage()">

    <div class="bg-white rounded-2xl border-2 border-[#D4AF6A]/50 p-6 sm:p-8 shadow-xl text-center space-y-6">
        
        <!-- Royal Luxury Crest Icon -->
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#FAF7F0] to-[#F3ECE1] border-2 border-[#D4AF6A] flex items-center justify-center mx-auto shadow-sm text-[#996E2E]">
            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[#996E2E]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
            </svg>
        </div>

        <div>
            <span class="text-[10px] uppercase font-bold tracking-[0.25em] text-[#996E2E]">Welcome to Rayka</span>
            <h1 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-0.5">
                Create Your Royal Account
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Join our boutique for exclusive launch access and seamless orders.
            </p>
        </div>

        <!-- Dynamic Error Alert for Form / OTP -->
        <div x-show="errorMessage" x-cloak class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium text-left flex items-start gap-2.5">
            <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-text="errorMessage"></span>
        </div>

        @if(session('error'))
            <div x-show="!errorMessage" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium text-left flex items-start gap-2.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium text-left space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="text-rose-600 font-bold">•</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Success notification -->
        <div x-show="successMessage" x-cloak class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium text-left flex items-start gap-2.5">
            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="successMessage"></span>
        </div>

        <form @submit.prevent="submitRegistration()" class="space-y-4 text-left text-xs">
            @csrf

            <!-- STEP 1: Registration Details (Shown when !otpSent) -->
            <div x-show="!otpSent" class="space-y-4">
                <!-- Name Input -->
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Full Name *</label>
                    <div class="relative">
                        <input type="text" name="name" x-model="name" required placeholder="e.g. Maharani Gayatri Devi" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pl-9 focus:border-[#D4AF6A] focus:outline-hidden">
                        <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>

                <!-- Email Input -->
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Email Address *</label>
                    <div class="relative">
                        <input type="email" name="email" x-model="email" required autocomplete="off" placeholder="example@raykajewellery.com" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pl-9 focus:border-[#D4AF6A] focus:outline-hidden">
                        <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                </div>

                <!-- Password Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-stone-700 mb-1">Password *</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required autocomplete="new-password" placeholder="••••••••" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-9 focus:border-[#D4AF6A] focus:outline-hidden">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-2.5 text-stone-400 hover:text-stone-700 cursor-pointer">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                        <!-- Password Validation Requirements -->
                        <div class="mt-2 space-y-1 text-[10px]" x-show="password.length > 0" x-cloak>
                            <p class="flex items-center gap-1.5 transition-colors" :class="password.length >= 6 ? 'text-emerald-600' : 'text-stone-500'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="password.length >= 6 ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'"></path></svg>
                                At least 6 characters
                            </p>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-stone-700 mb-1">Confirm Password *</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" x-model="confirmPassword" required autocomplete="new-password" placeholder="••••••••" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-9 focus:border-[#D4AF6A] focus:outline-hidden">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-2.5 text-stone-400 hover:text-stone-700 cursor-pointer">
                                <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 1 Submit Button -->
                <div>
                    <button type="button" @click="sendRegisterCode()" :disabled="loading" class="w-full py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-full font-bold text-xs uppercase tracking-wider hover:shadow-md transition border border-[#D4AF6A] mt-2 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <span x-text="loading ? 'Sending OTP...' : 'Send Verification Code'"></span>
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 2: OTP Verification (Shown when otpSent) -->
            <div x-show="otpSent" x-cloak class="space-y-4">
                <div class="p-3.5 sm:p-4 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/40 text-left">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-stone-800 text-xs">Verify Your Email</p>
                        <button type="button" @click="editEmail()" class="text-[11px] font-bold text-[#996E2E] hover:underline cursor-pointer flex items-center gap-1 shrink-0 whitespace-nowrap">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Change Email</span>
                        </button>
                    </div>
                    <p class="text-stone-500 text-[11px] sm:text-xs mt-1">
                        An OTP has been sent to <strong class="text-stone-800 break-all" x-text="email"></strong>.
                    </p>
                </div>
                
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Verification OTP (6 Digits) *</label>
                    <input type="text" 
                           name="otp" 
                           x-ref="otpInput"
                           x-model="otp" 
                           placeholder="Enter 6-digit code" 
                           maxlength="6" 
                           autocomplete="one-time-code"
                           class="otp-input w-full border border-[#D4AF6A] rounded-xl py-3 px-3 text-stone-800 focus:border-[#4A2C1D] focus:ring-2 focus:ring-[#D4AF6A]/30 focus:outline-none bg-[#FAF7F0]/40 transition" 
                           :required="otpSent">
                </div>

                <!-- Resend OTP with Live Cooldown Timer -->
                <div class="flex items-center justify-between text-[11px] sm:text-xs pt-1 px-1 gap-2">
                    <span class="text-stone-500 whitespace-nowrap">Didn't receive code?</span>
                    <template x-if="cooldown > 0">
                        <span class="text-stone-400 font-medium whitespace-nowrap">
                            Resend in <strong class="text-stone-600" x-text="cooldown + 's'"></strong>
                        </span>
                    </template>
                    <template x-if="cooldown <= 0">
                        <button type="button" 
                                @click="resendOtp()" 
                                :disabled="resending"
                                class="font-bold text-[#996E2E] hover:text-[#4A2C1D] hover:underline cursor-pointer disabled:opacity-50 whitespace-nowrap">
                            <span x-text="resending ? 'Sending...' : 'Resend Code'"></span>
                        </button>
                    </template>
                </div>
                
                <!-- Complete Registration Button -->
                <div>
                    <button type="submit" :disabled="loading" class="w-full py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-full font-bold text-xs uppercase tracking-wider hover:shadow-md transition border border-[#D4AF6A] mt-2 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <span x-text="loading ? 'Verifying OTP...' : 'Complete Registration'"></span>
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>

        </form>

        <div class="pt-2 text-xs text-stone-500 border-t border-[#D4AF6A]/20 flex items-center justify-center gap-1">
            <span>Already have an account?</span>
            <a href="{{ route('login', array_filter(['redirect' => $redirect ?? request('redirect', '')])) }}" class="font-bold text-[#996E2E] hover:underline">Sign In Here</a>
        </div>

    </div>

</div>

<script>
function registerPage() {
    return {
        name: '{{ old("name") }}',
        email: '{{ old("email") }}',
        password: '',
        confirmPassword: '',
        otp: '',
        otpSent: {{ session('otp_step') ? 'true' : 'false' }},
        loading: false,
        resending: false,
        showPassword: false,
        showConfirmPassword: false,
        errorMessage: '',
        successMessage: '',
        cooldown: 0,
        cooldownTimer: null,

        startCooldown: function(seconds) {
            var self = this;
            this.cooldown = seconds || 60;
            if (this.cooldownTimer) clearInterval(this.cooldownTimer);
            this.cooldownTimer = setInterval(function() {
                self.cooldown--;
                if (self.cooldown <= 0) {
                    clearInterval(self.cooldownTimer);
                    self.cooldownTimer = null;
                }
            }, 1000);
        },

        editEmail: function() {
            this.otpSent = false;
            this.errorMessage = '';
            this.successMessage = '';
            this.otp = '';
        },

        sendRegisterCode: function() {
            this.errorMessage = '';
            this.successMessage = '';

            if (!this.name.trim()) {
                this.errorMessage = 'Please enter your full name.';
                return;
            }
            if (!this.email.trim()) {
                this.errorMessage = 'Please enter a valid email address.';
                return;
            }
            if (!this.password || this.password.length < 6) {
                this.errorMessage = 'Password must be at least 6 characters.';
                return;
            }
            if (this.password !== this.confirmPassword) {
                this.errorMessage = 'Passwords do not match.';
                return;
            }
            
            var self = this;
            this.loading = true;
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            fetch('{{ route("register.send_otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    name: self.name.trim(),
                    email: self.email.trim(),
                    password: self.password,
                    password_confirmation: self.confirmPassword
                })
            })
            .then(function(res) { 
                return res.json().then(function(data) { return { status: res.status, data: data }; });
            })
            .then(function(response) {
                self.loading = false;
                if (response.data.success) {
                    self.otpSent = true;
                    self.otp = '';
                    self.successMessage = response.data.message || 'Verification code sent to ' + self.email;
                    self.startCooldown(60);
                    self.$nextTick(function() {
                        if (self.$refs.otpInput) self.$refs.otpInput.focus();
                    });
                } else {
                    var msg = response.data.message;
                    if (response.data.errors) {
                        var firstKey = Object.keys(response.data.errors)[0];
                        msg = response.data.errors[firstKey][0];
                    }
                    self.errorMessage = msg || 'Error sending verification code. Please try again.';
                }
            })
            .catch(function() {
                self.loading = false;
                self.errorMessage = 'Failed to connect. Please check your internet connection and try again.';
            });
        },

        resendOtp: function() {
            if (this.cooldown > 0 || this.resending) return;
            var self = this;
            this.resending = true;
            this.errorMessage = '';
            this.successMessage = '';
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            fetch('{{ route("register.send_otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    name: self.name.trim(),
                    email: self.email.trim(),
                    password: self.password,
                    password_confirmation: self.confirmPassword
                })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                self.resending = false;
                if (data.success) {
                    self.successMessage = 'A fresh 6-digit OTP has been sent to ' + self.email;
                    self.startCooldown(60);
                    self.$nextTick(function() {
                        if (self.$refs.otpInput) self.$refs.otpInput.focus();
                    });
                } else {
                    self.errorMessage = data.message || 'Failed to resend OTP. Please try again.';
                }
            })
            .catch(function() {
                self.resending = false;
                self.errorMessage = 'Connection error. Could not resend OTP.';
            });
        },

        submitRegistration: function() {
            this.errorMessage = '';
            this.successMessage = '';

            if (!this.otp || this.otp.trim().length !== 6) {
                this.errorMessage = 'Please enter the complete 6-digit OTP code.';
                if (this.$refs.otpInput) this.$refs.otpInput.focus();
                return;
            }

            var self = this;
            this.loading = true;
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
            var urlParams = new URLSearchParams(window.location.search);
            var redirectVal = urlParams.get('redirect') || '{{ $redirect ?? "" }}';

            fetch('{{ route("register.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: self.name.trim(),
                    email: self.email.trim(),
                    password: self.password,
                    password_confirmation: self.confirmPassword,
                    otp: self.otp.trim(),
                    redirect: redirectVal
                })
            })
            .then(function(res) {
                return res.json().then(function(data) { return { status: res.status, data: data }; });
            })
            .then(function(response) {
                self.loading = false;
                if (response.data.success) {
                    window.location.href = response.data.redirect || '{{ route("account.orders") }}';
                } else {
                    self.errorMessage = response.data.message || 'Invalid or expired verification code. Please check and try again.';
                    self.otp = '';
                    self.$nextTick(function() {
                        if (self.$refs.otpInput) self.$refs.otpInput.focus();
                    });
                }
            })
            .catch(function() {
                self.loading = false;
                self.errorMessage = 'Server error during registration. Please try again.';
            });
        }
    };
}
</script>

@push('scripts')
<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && (window.performance.navigation && window.performance.navigation.type === 2) || (window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward'))) {
            window.location.reload();
        }
    });
</script>
@endpush
@endsection
