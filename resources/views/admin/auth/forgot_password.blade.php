<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Forgot Password — Rayka Royal Administration</title>
    <link rel="icon" type="image/png" href="{{ asset('images/rayka-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#150904] flex items-center justify-center min-h-screen p-4 antialiased text-stone-200">

    <div class="max-w-md w-full bg-[#240F06] rounded-3xl border border-[#D4AF6A]/50 p-6 sm:p-10 shadow-2xl space-y-6" x-data="forgotPasswordPage()">
        
        <!-- Brand Crest / Rayka Logo -->
        <div class="text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#FAF7F0] to-[#F3ECE1] border-2 border-[#D4AF6A] flex items-center justify-center mx-auto shadow-sm p-1.5">
                <img src="{{ asset('images/rayka-logo.png') }}" alt="Rayka Crest" class="w-full h-full object-contain">
            </div>

            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-[#3D1A0A] border border-[#D4AF6A]/40 text-[10px] uppercase font-bold tracking-[0.25em] text-[#E7C77B]">
                    Account Recovery
                </span>
                <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#FAF7F0] mt-2">
                    Reset Admin Password
                </h1>
                <p class="text-xs text-stone-400 mt-1">
                    Verify your registered admin email to reset your credentials
                </p>
            </div>
        </div>

        <!-- Dynamic Error Alert -->
        <div x-show="errorMessage" x-cloak class="bg-rose-950/90 border border-rose-500/80 text-rose-200 text-xs p-3.5 rounded-xl flex items-start gap-2.5 shadow-sm">
            <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span class="flex-1 leading-relaxed" x-text="errorMessage"></span>
        </div>

        @if(session('error'))
            <div x-show="!errorMessage" class="bg-rose-950/80 border border-rose-500/60 text-rose-200 text-xs p-3.5 rounded-xl flex items-start gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div class="flex-1 leading-relaxed">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-950/80 border border-rose-500/60 text-rose-200 text-xs p-3.5 rounded-xl text-left space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-1.5">
                        <span class="text-rose-400 font-bold">•</span>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Dynamic Success Message -->
        <div x-show="successMessage" x-cloak class="bg-emerald-950/80 border border-emerald-500/60 text-emerald-200 text-xs p-3.5 rounded-xl flex items-start gap-2.5 shadow-sm">
            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="flex-1 leading-relaxed" x-text="successMessage"></span>
        </div>

        <form @submit.prevent="submitResetPassword()" class="space-y-4 text-left text-xs">
            @csrf

            <!-- STEP 1: Admin Email Input (When !otpSent) -->
            <div x-show="!otpSent" class="space-y-4">
                <div>
                    <label class="block text-stone-300 font-semibold mb-1">Registered Admin Email *</label>
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               x-model="email" 
                               required 
                               autocomplete="off"
                               placeholder="admin@raykajewellery.com" 
                               class="w-full bg-[#180B05] border border-[#D4AF6A]/40 rounded-xl px-3.5 py-3 text-stone-100 placeholder-stone-500 focus:outline-none focus:border-[#E7C77B] focus:ring-2 focus:ring-[#D4AF6A]/30 transition">
                    </div>
                </div>

                <button type="button" 
                        @click="sendResetCode()" 
                        :disabled="loading || !email"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#240F06] font-bold text-xs uppercase tracking-wider hover:opacity-95 hover:shadow-lg hover:shadow-amber-900/30 transition border border-[#E7C77B] flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                    <span x-text="loading ? 'Sending OTP...' : 'Send Password Reset Code'"></span>
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>

            <!-- STEP 2: OTP + New Password (When otpSent) -->
            <div x-show="otpSent" x-cloak class="space-y-4">
                <div class="p-3.5 rounded-xl bg-[#180B05] border border-[#D4AF6A]/30 text-left">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-stone-200 text-xs">Verify Reset Code</p>
                        <button type="button" @click="editEmail()" class="text-[11px] font-bold text-[#E7C77B] hover:underline cursor-pointer flex items-center gap-1 shrink-0 whitespace-nowrap">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Change Email</span>
                        </button>
                    </div>
                    <p class="text-stone-400 text-[11px] sm:text-xs mt-1">
                        A 6-digit reset code has been sent to <strong class="text-stone-200 break-all" x-text="email"></strong>.
                    </p>
                </div>

                <!-- OTP Input -->
                <div>
                    <label class="block text-stone-300 font-semibold mb-1">6-Digit Verification Code *</label>
                    <input type="text" 
                           name="otp" 
                           x-ref="otpInput"
                           x-model="otp" 
                           placeholder="Enter 6-digit code" 
                           maxlength="6" 
                           autocomplete="one-time-code"
                           class="otp-input-dark w-full bg-[#180B05] border border-[#D4AF6A]/40 rounded-xl px-3 py-3 text-stone-100 focus:outline-none focus:border-[#E7C77B] focus:ring-2 focus:ring-[#D4AF6A]/30 transition"
                           :required="otpSent">
                </div>

                <!-- Resend OTP with Live Cooldown Timer -->
                <div class="flex items-center justify-between text-[11px] sm:text-xs pt-1 px-1 gap-2">
                    <span class="text-stone-400 whitespace-nowrap">Didn't receive code?</span>
                    <template x-if="cooldown > 0">
                        <span class="text-stone-500 font-medium whitespace-nowrap">
                            Resend in <strong class="text-stone-300" x-text="cooldown + 's'"></strong>
                        </span>
                    </template>
                    <template x-if="cooldown <= 0">
                        <button type="button" 
                                @click="resendOtp()" 
                                :disabled="resending"
                                class="font-bold text-[#E7C77B] hover:underline cursor-pointer disabled:opacity-50 whitespace-nowrap">
                            <span x-text="resending ? 'Sending...' : 'Resend Code'"></span>
                        </button>
                    </template>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-stone-300 font-semibold mb-1">New Password *</label>
                    <input type="password" 
                           name="password" 
                           x-model="password"
                           autocomplete="new-password"
                           placeholder="••••••••" 
                           class="w-full bg-[#180B05] border border-[#D4AF6A]/40 rounded-xl px-3.5 py-3 text-stone-100 font-mono focus:outline-none focus:border-[#E7C77B] focus:ring-2 focus:ring-[#D4AF6A]/30">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-stone-300 font-semibold mb-1">Confirm New Password *</label>
                    <input type="password" 
                           name="password_confirmation" 
                           x-model="confirmPassword"
                           autocomplete="new-password"
                           placeholder="••••••••" 
                           class="w-full bg-[#180B05] border border-[#D4AF6A]/40 rounded-xl px-3.5 py-3 text-stone-100 font-mono focus:outline-none focus:border-[#E7C77B] focus:ring-2 focus:ring-[#D4AF6A]/30">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            :disabled="loading" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#240F06] font-bold text-xs uppercase tracking-wider hover:opacity-95 hover:shadow-lg hover:shadow-amber-900/30 transition border border-[#E7C77B] flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-text="loading ? 'Updating Password...' : 'Securely Reset Password'"></span>
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </form>

        <div class="pt-4 border-t border-[#D4AF6A]/20 text-center">
            <a href="{{ route('admin.login') }}" class="text-[11px] text-stone-400 hover:text-[#E7C77B] transition flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Back to Admin Login</span>
            </a>
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
                errorMessage: '',
                successMessage: '',
                cooldown: 0,
                cooldownTimer: null,

                startCooldown(seconds) {
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

                editEmail() {
                    this.otpSent = false;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.otp = '';
                },

                async sendResetCode() {
                    this.errorMessage = '';
                    this.successMessage = '';

                    if (!this.email.trim()) {
                        this.errorMessage = 'Please enter your registered admin email address.';
                        return;
                    }

                    var self = this;
                    this.loading = true;

                    try {
                        const response = await fetch('{{ route("admin.password.send_otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email: self.email.trim() })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            self.otpSent = true;
                            self.otp = '';
                            self.successMessage = data.message || 'Verification code sent to ' + self.email;
                            self.startCooldown(60);
                            self.$nextTick(function() {
                                if (self.$refs.otpInput) self.$refs.otpInput.focus();
                            });
                        } else {
                            self.errorMessage = data.message || 'No admin account registered with this email address.';
                        }
                    } catch (error) {
                        self.errorMessage = 'Network error. Please try again.';
                    } finally {
                        self.loading = false;
                    }
                },

                async resendOtp() {
                    if (this.cooldown > 0 || this.resending) return;
                    var self = this;
                    this.resending = true;
                    this.errorMessage = '';
                    this.successMessage = '';

                    try {
                        const response = await fetch('{{ route("admin.password.send_otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email: self.email.trim() })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            self.successMessage = 'A fresh reset code has been sent to ' + self.email;
                            self.startCooldown(60);
                            self.$nextTick(function() {
                                if (self.$refs.otpInput) self.$refs.otpInput.focus();
                            });
                        } else {
                            self.errorMessage = data.message || 'Failed to resend OTP. Please try again.';
                        }
                    } catch (error) {
                        self.errorMessage = 'Connection error. Could not resend reset code.';
                    } finally {
                        self.resending = false;
                    }
                },

                async submitResetPassword() {
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

                    try {
                        const response = await fetch('{{ route("admin.password.reset.submit") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: self.email.trim(),
                                otp: self.otp.trim(),
                                password: self.password,
                                password_confirmation: self.confirmPassword
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            window.location.href = data.redirect || '{{ route("admin.login") }}';
                        } else {
                            self.errorMessage = data.message || 'Invalid or expired verification code. Please request a new OTP.';
                            self.otp = '';
                            self.$nextTick(function() {
                                if (self.$refs.otpInput) self.$refs.otpInput.focus();
                            });
                        }
                    } catch (error) {
                        self.errorMessage = 'Server error while resetting password. Please try again.';
                    } finally {
                        self.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
