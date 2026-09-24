<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your administrator email address.',
            'email.email' => 'Please enter a valid email format (e.g., admin@raykajewellery.com).',
            'password.required' => 'Please enter your admin password.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password) || ! $user->isAdmin()) {
            return back()->withInput($request->only('email'))->with('error', 'Invalid admin credentials or unauthorized account.');
        }

        $remember = $request->boolean('remember', false);
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }

    public function showForgotPassword()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.forgot_password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->whereIn('role', ['admin', 'staff'])->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No admin account registered with this email address.',
            ], 404);
        }

        $otp = (string) random_int(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        defer(function () use ($user, $otp) {
            try {
                Mail::to($user->email)->send(new ResetPasswordOtpMail($otp));
            } catch (\Throwable $e) {
                Log::warning("Admin reset password OTP email failure for {$user->email}: ".$e->getMessage());
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Password reset OTP sent to {$email}!",
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->whereIn('role', ['admin', 'staff'])->firstOrFail();

        if (! $user->otp || $request->otp !== $user->otp || now()->greaterThan($user->otp_expires_at)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification code. Please request a new OTP.',
                ], 422);
            }
            return back()->withInput()->with('error', 'Invalid or expired verification code. Please request a new OTP.')->with('otp_step', true);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.login'),
                'message' => 'Your admin password has been reset successfully! Please sign in.',
            ]);
        }

        return redirect()->route('admin.login')->with('success', 'Your admin password has been reset successfully! Please sign in.');
    }
}

