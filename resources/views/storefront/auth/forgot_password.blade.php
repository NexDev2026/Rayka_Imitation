@extends('layouts.storefront')

@section('title', 'Forgot Password — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-12" x-data="forgotPasswordPage()">

    <div class="bg-white rounded-2xl border-2 border-[#D4AF6A]/50 p-6 sm:p-8 shadow-xl text-center space-y-6">
        
        <!-- Royal Luxury Key Icon -->
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#FAF7F0] to-[#F3ECE1] border-2 border-[#D4AF6A] flex items-center justify-center mx-auto shadow-sm text-[#996E2E]">
            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>

        <div>
            <span class="text-[10px] uppercase font-bold tracking-[0.25em] text-[#996E2E]">Account Recovery</span>
            <h1 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-0.5">
                Reset Royal Password
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Verify your registered email with an OTP code to set a new password.
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

        <form @submit.prevent="submitResetPassword()" class="space-y-4 text-left text-xs">
            @csrf

            <!-- STEP 1: Enter Email (When !otpSent) -->
            <div x-show="!otpSent" class="space-y-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Registered Email Address *</label>
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               x-model="email" 
                               required 
                               autocomplete="off"
                               placeholder="example@raykajewellery.com" 
                               class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pl-9 focus:border-[#D4AF6A] focus:outline-hidden text-xs">
                        <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                </div>

                <button type="button" 
                        @click="sendResetCode()" 
                        :disabled="loading" 
                        class="w-full py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-full font-bold text-xs uppercase tracking-wider hover:shadow-md transition border border-[#D4AF6A] mt-2 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <span x-text="loading ? 'Sending OTP...' : 'Send Password Reset Code'"></span>
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>

            <!-- STEP 2: OTP + New Password (When otpSent) -->
            <div x-show="otpSent" x-cloak class="space-y-4">
                <div class="p-3.5 sm:p-4 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/40 text-left">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-stone-800 text-xs">Verify Reset Code</p>
                        <button type="button" @click="editEmail()" class="text-[11px] font-bold text-[#996E2E] hover:underline cursor-pointer flex items-center gap-1 shrink-0 whitespace-nowrap">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Change Email</span>
                        </button>
                    </div>
                    <p class="text-stone-500 text-[11px] sm:text-xs mt-1">
                        A 6-digit reset code has been sent to <strong class="text-stone-800 break-all" x-text="email"></strong>.
                    </p>
                </div>

                <!-- OTP Input -->
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

                <!-- New Password & Confirmation -->
                <div class="space-y-3 pt-1">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">New Password *</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   x-model="password"
                                   autocomplete="new-password" 
                                   placeholder="••••••••" 
                                   class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-9 focus:border-[#D4AF6A] focus:outline-hidden">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-2.5 text-stone-400 hover:text-stone-700 cursor-pointer">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Confirm New Password *</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" 
                                   name="password_confirmation" 
                                   x-model="confirmPassword"
                                   autocomplete="new-password" 
                                   placeholder="••••••••" 
                                   class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-9 focus:border-[#D4AF6A] focus:outline-hidden">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-2.5 text-stone-400 hover:text-stone-700 cursor-pointer">
                                <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="loading" 
                            class="w-full py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-full font-bold text-xs uppercase tracking-wider hover:shadow-md transition border border-[#D4AF6A] mt-2 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <span x-text="loading ? 'Updating Password...' : 'Update Password & Sign In'"></span>
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </form>

        <div class="pt-2 text-xs text-stone-500 border-t border-[#D4AF6A]/20 flex items-center justify-center gap-1">
            <span>Remembered your password?</span>
            <a href="{{ route('login') }}" class="font-bold text-[#996E2E] hover:underline">Back to Sign In</a>
        </div>

    </div>

</div>

<script>
function forgotPasswordPage() {
    return {
        email: '{{ old("email") }}',
        otp: '',
        password: '',
        confirmPassword: '',
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

        sendResetCode: function() {
            this.errorMessage = '';
            this.successMessage = '';

            if (!this.email.trim()) {
                this.errorMessage = 'Please enter your registered email address.';
                return;
            }

            var self = this;
            this.loading = true;
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            fetch('{{ route("password.send_otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: self.email.trim() })
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
                    self.errorMessage = response.data.message || 'No account registered with this email address.';
                }
            })
            .catch(function() {
                self.loading = false;
                self.errorMessage = 'Failed to send password reset code. Please check your connection.';
            });
        },

        resendOtp: function() {
            if (this.cooldown > 0 || this.resending) return;
            var self = this;
            this.resending = true;
            this.errorMessage = '';
            this.successMessage = '';
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            fetch('{{ route("password.send_otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: self.email.trim() })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                self.resending = false;
                if (data.success) {
                    self.successMessage = 'A fresh reset code has been sent to ' + self.email;
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
                self.errorMessage = 'Connection error. Could not resend reset code.';
            });
        },

        submitResetPassword: function() {
            this.errorMessage = '';
            this.successMessage = '';

            if (!this.otp || this.otp.trim().length !== 6) {
                this.errorMessage = 'Please enter the complete 6-digit OTP code.';
                if (this.$refs.otpInput) this.$refs.otpInput.focus();
                return;
            }
            if (!this.password || this.password.length < 6) {
                this.errorMessage = 'New password must be at least 6 characters.';
                return;
            }
            if (this.password !== this.confirmPassword) {
                this.errorMessage = 'Passwords do not match.';
                return;
            }

            var self = this;
            this.loading = true;
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

            fetch('{{ route("password.reset.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: self.email.trim(),
                    otp: self.otp.trim(),
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
                    window.location.href = response.data.redirect || '{{ route("login") }}';
                } else {
                    self.errorMessage = response.data.message || 'Invalid or expired verification code. Please request a new OTP.';
                    self.otp = '';
                    self.$nextTick(function() {
                        if (self.$refs.otpInput) self.$refs.otpInput.focus();
                    });
                }
            })
            .catch(function() {
                self.loading = false;
                self.errorMessage = 'Server error while resetting password. Please try again.';
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
