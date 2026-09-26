<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VerifyNewEmailOtpMail;
use App\Models\Category;
use App\Models\StoreSetting;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => StoreSetting::get('store_name', 'Rayka Imitation Jewellery'),
            'tagline' => StoreSetting::get('tagline', 'Royal Heritage & 1 Gram Micro Gold Imitation Jewellery'),
            'upi_id' => StoreSetting::get('upi_id', 'raykajewellery@icici'),
            'upi_payee_name' => StoreSetting::get('upi_payee_name', 'Rayka Imitation Jewellery Pvt Ltd'),
            'qr_code_image' => StoreSetting::get('qr_code_image', '/images/qr/sample-upi-qr.svg'),
            'store_phone' => StoreSetting::get('store_phone', '+91 98765 43210'),
            'store_whatsapp' => StoreSetting::get('store_whatsapp', '+91 98765 43210'),
            'store_email' => StoreSetting::get('store_email', 'care@raykajewellery.com'),
            'admin_email' => StoreSetting::get('admin_email', config('services.brevo.admin_email', 'admin@raykajewellery.com')),
            'store_address' => StoreSetting::get('store_address', 'Rayka Heritage Complex, Johari Bazaar, Jaipur, Rajasthan 302003'),
            'free_shipping_min' => StoreSetting::get('free_shipping_min', '999'),
            'shipping_flat_fee' => StoreSetting::get('shipping_flat_fee', '99'),
            'trust_badge_1' => StoreSetting::get('trust_badge_1'),
            'trust_badge_2' => StoreSetting::get('trust_badge_2'),
            'trust_badge_3' => StoreSetting::get('trust_badge_3'),
            'trust_badge_4' => StoreSetting::get('trust_badge_4'),
            'showcase_enabled' => StoreSetting::get('showcase_enabled', '1'),
            'showcase_title' => StoreSetting::get('showcase_title', 'Curated Royal Collections'),
            'showcase_subtitle' => StoreSetting::get('showcase_subtitle', 'Select a collection below to discover hand-finished 1 gram micro gold masterpieces.'),
            'showcase_limit' => StoreSetting::get('showcase_limit', '10'),
            'showcase_categories' => json_decode(StoreSetting::get('showcase_categories', '["1","2","9","3","10"]'), true) ?: [1, 2, 9, 3, 10],
        ];

        $allCategories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.settings.index', compact('settings', 'allCategories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'upi_id' => 'nullable|string|max:100',
            'upi_payee_name' => 'nullable|string|max:255',
            'store_phone' => 'nullable|string|max:20',
            'store_whatsapp' => 'nullable|string|max:20',
            'store_email' => 'nullable|email:rfc,filter|max:255',
            'admin_email' => 'nullable|email:rfc,filter|max:255',
            'store_address' => 'nullable|string|max:500',
            'free_shipping_min' => 'nullable|numeric|min:0',
            'shipping_flat_fee' => 'nullable|numeric|min:0',
            'trust_badge_1' => 'nullable|string|max:100',
            'trust_badge_2' => 'nullable|string|max:100',
            'trust_badge_3' => 'nullable|string|max:100',
            'trust_badge_4' => 'nullable|string|max:100',
            'showcase_title' => 'nullable|string|max:200',
            'showcase_subtitle' => 'nullable|string|max:500',
            'showcase_limit' => 'nullable|integer|min:1|max:50',
            // QR Code: max 10 MB, image types only
            'qr_code_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
        ], [
            'qr_code_file.max' => 'The QR code image must not exceed 10MB.',
            'qr_code_file.mimes' => 'The QR code image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
        ]);

        $fields = [
            'store_name',
            'tagline',
            'upi_id',
            'upi_payee_name',
            'store_phone',
            'store_whatsapp',
            'store_email',
            'admin_email',
            'store_address',
            'free_shipping_min',
            'shipping_flat_fee',
            'trust_badge_1',
            'trust_badge_2',
            'trust_badge_3',
            'trust_badge_4',
            'showcase_title',
            'showcase_subtitle',
            'showcase_limit',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                StoreSetting::set($field, $request->input($field));
            }
        }

        // Showcase Enabled Toggle & Categories
        StoreSetting::set('showcase_enabled', $request->has('showcase_enabled') ? '1' : '0');

        if ($request->has('showcase_categories')) {
            $cats = array_map('strval', $request->input('showcase_categories', []));
            StoreSetting::set('showcase_categories', json_encode($cats));
        }

        // QR Code Image Upload (converted to WebP and mirrored to external rayka_uploads/qr and rayka_uploads/settings)
        if ($request->hasFile('qr_code_file')) {
            $qrPath = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('qr_code_file'),
                folder: 'uploads/qr',
                prefix: 'upi_qr',
                maxWidth: 1200,
                maxHeight: 1200,
                quality: 90
            );
            StoreSetting::set('qr_code_image', $qrPath);

            // Double guarantee: mirror QR code to both 'qr' and 'settings' folders
            $qrFn = basename($qrPath);
            ImageUploadService::mirrorToExternalUploads(public_path('uploads/qr/' . $qrFn), 'qr', $qrFn);
            ImageUploadService::mirrorToExternalUploads(public_path('uploads/qr/' . $qrFn), 'settings', $qrFn);
        }

        return back()->with('success', 'Store settings and homepage showcase updated successfully!');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_email' => 'required|email:rfc,filter|unique:users,email',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'The provided current password does not match our records.');
        }

        $otp = (string) random_int(100000, 999999);
        
        Cache::put('email_change_' . $user->id, [
            'new_email' => strtolower(trim($request->new_email)),
            'otp' => $otp,
        ], now()->addMinutes(15));

        defer(function () use ($request, $otp) {
            try {
                Mail::to(strtolower(trim($request->new_email)))->send(new VerifyNewEmailOtpMail($otp));
            } catch (\Throwable $e) {
                Log::warning('Admin verify new email OTP dispatch warning: '.$e->getMessage());
            }
        });

        return back()->with('email_change_otp_sent', true)->with('success', 'An OTP has been sent to your new email address. Please verify to confirm.')->withFragment('account-security');
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = Auth::user();
        $cacheKey = 'email_change_' . $user->id;
        $cachedData = Cache::get($cacheKey);

        if (! $cachedData || $cachedData['otp'] !== $request->otp) {
            return back()->with('email_change_otp_sent', true)->with('error', 'Invalid or expired OTP code.')->withFragment('account-security');
        }

        $user->update([
            'email' => $cachedData['new_email'],
        ]);

        Cache::forget($cacheKey);

        return back()->with('success', 'Your email address has been updated successfully!')->withFragment('account-security');
    }

    /**
     * Run an instant database backup from the admin interface.
     */
    public function runBackup()
    {
        try {
            Artisan::call('db:backup');
            $output = trim(Artisan::output());

            return back()->with('success', 'Database backup completed successfully! ' . $output)->withFragment('backup-sync');
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage())->withFragment('backup-sync');
        }
    }

    /**
     * Sync all local uploads to external rayka_uploads directory.
     */
    public function syncStorage()
    {
        try {
            Artisan::call('rayka:setup-storage');
            $output = trim(Artisan::output());

            return back()->with('success', 'All images and documents mirrored to rayka_uploads successfully!')->withFragment('backup-sync');
        } catch (\Throwable $e) {
            return back()->with('error', 'Storage sync failed: ' . $e->getMessage())->withFragment('backup-sync');
        }
    }
}

