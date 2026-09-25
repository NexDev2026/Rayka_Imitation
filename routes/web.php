<?php

use App\Http\Controllers\Admin\AdminAttributeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBannerController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInquiryController;
use App\Http\Controllers\Admin\AdminNavMenuController;
use App\Http\Controllers\Admin\AdminOfferController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\CustomerAccountController;
use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\SitemapController;
use App\Http\Controllers\Storefront\TrackOrderController;
use App\Http\Controllers\Storefront\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/collection/{slug}', [StorefrontController::class, 'navGroupPage'])->name('nav.group');
Route::get('/categories/{slug}', [StorefrontController::class, 'category'])->name('category.show');
Route::get('/product/{slug}', [StorefrontController::class, 'product'])->name('product.show');
Route::get('/trending', [StorefrontController::class, 'trending'])->name('trending');
Route::get('/reviews', [StorefrontController::class, 'reviews'])->name('reviews.all');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');
Route::post('/contact', [StorefrontController::class, 'submitContact'])->name('contact.submit');
Route::get('/search', [StorefrontController::class, 'search'])->name('search');
Route::get('/faq', [StorefrontController::class, 'faq'])->name('faq');
Route::get('/policy/{page}', [StorefrontController::class, 'policy'])->name('policy');

/*
|--------------------------------------------------------------------------
| Storage & Media Direct Fallback Delivery (Zero-Error Safety Net)
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . $path);
    if (! file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('media.storage');

Route::get('/images/{path}', function (string $path) {
    $filePath = public_path('images/' . $path);
    if (! file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('media.images');


/*
|--------------------------------------------------------------------------
| Wishlist & Cart API
|--------------------------------------------------------------------------
*/
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::get('/api/store-counts', [WishlistController::class, 'getCounts'])->name('api.counts');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/product-quantity', [CartController::class, 'updateProductQuantity'])->name('cart.product_quantity');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');

/*
|--------------------------------------------------------------------------
| Checkout & Order Placement
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/order-confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('order.confirmation');

/*
|--------------------------------------------------------------------------
| Public Track Order Consignment System
|--------------------------------------------------------------------------
*/
Route::get('/track-order', [TrackOrderController::class, 'index'])->name('order.track');
Route::post('/track-order', [TrackOrderController::class, 'search'])->name('order.track.search');
Route::get('/track-order/{orderNumber}/invoice', [TrackOrderController::class, 'downloadInvoice'])->name('order.track.invoice');

/*
|--------------------------------------------------------------------------
| Customer Auth & Account
|--------------------------------------------------------------------------
*/
Route::get('/login', [CustomerAccountController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomerAccountController::class, 'login'])->middleware('throttle:login')->name('login.submit');

Route::post('/api/send-otp', [CustomerAccountController::class, 'sendOtp'])->middleware('throttle:otp')->name('api.send_otp');
Route::post('/api/verify-otp', [CustomerAccountController::class, 'verifyOtpApi'])->middleware('throttle:otp')->name('api.verify_otp');
Route::get('/register', [CustomerAccountController::class, 'showRegister'])->name('register');
Route::post('/register/otp', [CustomerAccountController::class, 'sendRegisterOtp'])->middleware('throttle:otp')->name('register.send_otp');
Route::post('/register', [CustomerAccountController::class, 'register'])->middleware('throttle:register')->name('register.submit');
Route::get('/forgot-password', [CustomerAccountController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password/otp', [CustomerAccountController::class, 'sendResetOtp'])->middleware('throttle:otp')->name('password.send_otp');
Route::post('/forgot-password/reset', [CustomerAccountController::class, 'resetPassword'])->middleware('throttle:password-reset')->name('password.reset.submit');
Route::post('/logout', [CustomerAccountController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Orders
    Route::get('/account/orders', [CustomerAccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{orderNumber}', [CustomerAccountController::class, 'orderDetail'])->name('account.order.detail');
    Route::post('/account/orders/{orderNumber}/cancel', [CustomerAccountController::class, 'cancelOrder'])->name('account.order.cancel');
    Route::get('/account/orders/{orderNumber}/invoice', [CustomerAccountController::class, 'downloadInvoice'])->name('account.order.invoice');

    // Saved Addresses
    Route::get('/account/addresses', [CustomerAccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/account/addresses', [CustomerAccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [CustomerAccountController::class, 'updateAddress'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [CustomerAccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
    Route::post('/account/addresses/{address}/default', [CustomerAccountController::class, 'setDefaultAddress'])->name('account.addresses.default');

    // Settings
    Route::get('/account/settings', [CustomerAccountController::class, 'settings'])->name('account.settings');
    Route::post('/account/settings/email', [CustomerAccountController::class, 'updateEmail'])->name('account.settings.update_email');
    Route::post('/account/settings/email/verify', [CustomerAccountController::class, 'verifyEmailOtp'])->middleware('throttle:otp')->name('account.settings.verify_email');

    // Reviews
    Route::post('/review/submit', [CustomerAccountController::class, 'submitReview'])->name('review.submit');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (/admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:admin-login')->name('login.submit');
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('password.forgot');
    Route::post('/forgot-password/otp', [AdminAuthController::class, 'sendResetOtp'])->middleware('throttle:otp')->name('password.send_otp');
    Route::post('/forgot-password/reset', [AdminAuthController::class, 'resetPassword'])->middleware('throttle:password-reset')->name('password.reset.submit');

    // Authenticated Admin
    Route::middleware('admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('root');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics/telemetry', [AdminDashboardController::class, 'telemetry'])->name('analytics.telemetry');

        // Banners
        Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('banners.create');
        Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{id}/edit', [AdminBannerController::class, 'edit'])->name('banners.edit');
        Route::put('/banners/{id}', [AdminBannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{id}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');
        Route::post('/banners/{id}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');

        // Categories
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Mega-Menu Nav Groups
        Route::get('/nav-menu', [AdminNavMenuController::class, 'index'])->name('nav_menu.index');
        Route::post('/nav-menu/{id}', [AdminNavMenuController::class, 'update'])->name('nav_menu.update');

        // Dynamic Filter / Attribute Manager
        Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
        Route::post('/attributes/groups', [AdminAttributeController::class, 'storeGroup'])->name('attributes.groups.store');
        Route::post('/attributes/values', [AdminAttributeController::class, 'storeValue'])->name('attributes.values.store');
        Route::post('/attributes/groups/{id}/categories', [AdminAttributeController::class, 'updateGroupCategories'])->name('attributes.groups.categories');
        Route::delete('/attributes/values/{id}', [AdminAttributeController::class, 'destroyValue'])->name('attributes.values.destroy');
        Route::delete('/attributes/groups/{id}', [AdminAttributeController::class, 'destroyGroup'])->name('attributes.groups.destroy');

        // Products
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::match(['post', 'put', 'patch'], '/products/images/{id}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');
        Route::match(['post', 'put', 'patch'], '/products/images/{id}/secondary', [AdminProductController::class, 'setSecondaryImage'])->name('products.images.secondary');
        Route::match(['post', 'delete', 'put'], '/products/images/{id}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');
        Route::match(['post', 'delete', 'put'], '/products/documents/{id}', [AdminProductController::class, 'deleteDocument'])->name('products.documents.delete');

        // Orders & Payment Verification
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/verify-payment', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify_payment');
        Route::post('/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status');
        Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice');

        // Reviews Moderation
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.update_status');
        Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Coupons
        Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
        Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
        Route::post('/coupons/{id}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
        Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

        // Category Flash Sales & Timed Offers
        Route::get('/offers', [AdminOfferController::class, 'index'])->name('offers.index');
        Route::post('/offers', [AdminOfferController::class, 'store'])->name('offers.store');
        Route::post('/offers/{id}/toggle', [AdminOfferController::class, 'toggle'])->name('offers.toggle');
        Route::delete('/offers/{id}', [AdminOfferController::class, 'destroy'])->name('offers.destroy');

        // Customer Inquiries
        Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
        Route::post('/inquiries/{id}', [AdminInquiryController::class, 'update'])->name('inquiries.update');
        Route::delete('/inquiries/{id}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

        // Reports & Analytics
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

        // Store Settings & QR
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/email', [AdminSettingController::class, 'updateEmail'])->name('settings.update_email');
        Route::post('/settings/email/verify', [AdminSettingController::class, 'verifyEmailOtp'])->name('settings.verify_email');

        // Users / Customer Directory
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| One-Click Web Deployment & Auto-Migration Endpoint for Hosting
|--------------------------------------------------------------------------
*/
Route::get('/rayka-deploy', function (\Illuminate\Http\Request $request) {
    // Security Layer 1: Strictly restricted to authenticated Admin users
    if (! auth()->check() || ! auth()->user()->isAdmin()) {
        abort(404); // Stealth 404: Hidden from public, bots, and unauthorized users
    }

    // Security Layer 2: Constant-time comparison of deployment token
    $token = (string) $request->query('token');
    $secret = (string) env('DEPLOY_SECRET', 'rayka_deploy_2026');
    if ($token === '' || ! hash_equals($secret, $token)) {
        abort(403, 'Unauthorized deployment token.');
    }

    $dbStatus = '';
    $migrateOutput = '';
    $backupOutput = '';

    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        $dbStatus = "Connected successfully to database: {$dbName}";

        // Safe Non-Destructive Migrations Only (migrate:fresh has been permanently removed for security)
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        if ($request->query('seed') || $request->query('sync') || $request->query('sync_data')) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $migrateOutput .= "\nFULL CATALOGUE SEEDED:\n" . \Illuminate\Support\Facades\Artisan::output();
        }

        // Self-Healing: Ensure category_nav_group has all mega-menu relationships
        try {
            $womenNavCount = \Illuminate\Support\Facades\DB::table('category_nav_group')->where('nav_group_id', 2)->count();
            if ($womenNavCount < 4) {
                $prodJson = database_path('seeders/rayka_production_data.json');
                if (file_exists($prodJson)) {
                    $prodData = json_decode(file_get_contents($prodJson), true);
                    if (!empty($prodData['category_nav_group'])) {
                        \Illuminate\Support\Facades\DB::table('category_nav_group')->delete();
                        foreach (array_chunk($prodData['category_nav_group'], 50) as $chunk) {
                            \Illuminate\Support\Facades\DB::table('category_nav_group')->insert($chunk);
                        }
                        $migrateOutput .= "\n✓ Mega-Menu Self-Healed: Seeded " . count($prodData['category_nav_group']) . " category-nav relations!";
                    }
                }
            }
        } catch (\Throwable $e) {
            $migrateOutput .= "\n! Mega-menu check note: " . $e->getMessage();
        }

        // On-Demand Database Backup Snapshot
        if ($request->query('backup')) {
            \Illuminate\Support\Facades\Artisan::call('db:backup', ['--no-mail' => true]);
            $backupOutput = \Illuminate\Support\Facades\Artisan::output();
        }

    } catch (\Throwable $e) {
        $dbStatus = 'Database issue: ' . $e->getMessage();
        $migrateOutput = 'Migration skipped due to database status: ' . $e->getMessage();
    }

    $catalogueStats = '';
    $womenCatList = '';
    try {
        $pCount = \App\Models\Product::count();
        $cCount = \App\Models\Category::count();
        $bCount = \App\Models\HomeBanner::count();
        $iCount = \App\Models\ProductImage::count();
        $catalogueStats = "Products: {$pCount} | Categories: {$cCount} | Banners: {$bCount} | Product Images: {$iCount}";

        $womenGroup = \App\Models\NavGroup::with('categories')->where('slug', 'women')->first();
        if ($womenGroup) {
            $womenCatList = $womenGroup->categories->pluck('name')->implode(', ');
        }
    } catch (\Throwable $e) {
        $catalogueStats = 'Catalogue note: ' . $e->getMessage();
    }

    $storageOutput = '';
    try {
        \Illuminate\Support\Facades\Artisan::call('rayka:setup-storage');
        $storageOutput = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $storageOutput = 'Storage setup note: ' . $e->getMessage();
    }

    $cacheOutput = '';
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $cacheOutput = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $cacheOutput = 'Cache note: ' . $e->getMessage();
    }

    // List recent backups found on host
    $backupFilesInfo = [];
    $checkBackupDirs = [
        'Storage App Backups' => storage_path('app/backups'),
        'External Root Backups (/backups)' => base_path('../backups'),
        'Local App Backups (/backups)' => base_path('backups'),
    ];
    foreach ($checkBackupDirs as $label => $bDir) {
        if (is_dir($bDir)) {
            $found = glob($bDir . '/*.sql*');
            $backupFilesInfo[] = "<b>{$label}</b> (" . count($found) . " files): " . implode(', ', array_map('basename', array_slice($found, -3)));
        } else {
            $backupFilesInfo[] = "<b>{$label}</b>: Directory not found";
        }
    }
    $backupsSummary = implode("<br>", $backupFilesInfo);

    // Live Email Test Diagnostic
    $mailTestOutput = '';
    $mailConfigSummary = 'Default Driver: ' . config('mail.default') . ' | From: ' . config('mail.from.address') . ' | Brevo Key: ' . (config('services.brevo.key') ? substr(config('services.brevo.key'), 0, 14) . '...' : 'NOT CONFIGURED');
    
    if ($request->query('test_mail')) {
        $testRecipient = $request->query('to') ?: config('services.brevo.admin_email', 'nexdevstudio01@gmail.com');
        try {
            \Illuminate\Support\Facades\Mail::to($testRecipient)->send(new \App\Mail\LoginOtpMail('849201'));
            $mailTestOutput = "✓ SUCCESS: Test OTP email (849201) successfully dispatched to {$testRecipient} via " . config('mail.default') . "!";
        } catch (\Throwable $e) {
            $mailTestOutput = "✗ FAILED: " . $e->getMessage();
        }
    }

    // Recent Server Log Snippet (Last 25 lines)
    $logLines = [];
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $raw = file($logFile);
        $logLines = array_slice($raw, -25);
    }
    $recentLogs = !empty($logLines) ? implode('', $logLines) : 'No recent log entries found.';

    $tokenParam = urlencode($secret);

    return response("<div style='background:#1a1412;color:#FAF7F0;padding:30px;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;max-width:880px;margin:40px auto;border-radius:16px;border:1px solid #D4AF6A;box-shadow:0 10px 25px rgba(0,0,0,0.5);'>
        <h2 style='color:#E7C77B;margin-top:0;border-bottom:1px solid #D4AF6A;padding-bottom:10px;'>✨ Rayka Auto-Deploy & Sync Report</h2>
        
        <h4 style='color:#D4AF6A;margin-bottom:6px;'>1. Current Database & Catalogue Status:</h4>
        <div style='background:#2E180E;padding:12px;border-radius:8px;border-left:4px solid #4ade80;margin-bottom:10px;'>
            <p style='margin:0 0 6px 0;color:#FAF7F0;font-weight:bold;font-size:13px;'>📊 {$catalogueStats}</p>
            <p style='margin:0;color:#D4AF6A;font-size:12px;'>👑 <b>Women's Dropdown Categories:</b> {$womenCatList}</p>
        </div>
        <pre style='background:#2E180E;padding:12px;border-radius:8px;overflow-x:auto;color:#E7C77B;font-size:12px;'>".e($dbStatus)."\n\n".e($migrateOutput)."</pre>
        
        " . (!empty($backupOutput) ? "<h4 style='color:#D4AF6A;margin-bottom:6px;'>💾 On-Demand Database Backup Execution:</h4><pre style='background:#2E180E;padding:12px;border-radius:8px;overflow-x:auto;color:#4ade80;font-size:12px;'>" . e($backupOutput) . "</pre>" : "") . "

        <h4 style='color:#D4AF6A;margin-bottom:6px;'>2. Transactional Email System Status:</h4>
        <div style='background:#2E180E;padding:12px;border-radius:8px;color:#FAF7F0;font-size:12px;line-height:1.6;'>
            <p style='margin:0 0 6px 0;color:#D4AF6A;'><b>Config:</b> {$mailConfigSummary}</p>
            " . (!empty($mailTestOutput) ? "<pre style='margin:8px 0 0 0;padding:10px;background:#1a1412;border-radius:6px;color:" . (str_contains($mailTestOutput, 'SUCCESS') ? '#4ade80' : '#f87171') . ";font-size:12px;font-weight:bold;'>" . e($mailTestOutput) . "</pre>" : "<p style='margin:0;color:#a8a29e;'>Click the test email button below to verify instant live delivery to admin.</p>") . "
        </div>

        <h4 style='color:#D4AF6A;margin-bottom:6px;margin-top:16px;'>3. Storage & Static Media Routing:</h4>
        <pre style='background:#2E180E;padding:12px;border-radius:8px;overflow-x:auto;color:#E7C77B;font-size:12px;'>".e($storageOutput)."</pre>

        <h4 style='color:#D4AF6A;margin-bottom:6px;'>4. Backup Folders Status on Server:</h4>
        <div style='background:#2E180E;padding:12px;border-radius:8px;color:#FAF7F0;font-size:12px;line-height:1.6;'>
            {$backupsSummary}
        </div>
        
        <h4 style='color:#D4AF6A;margin-bottom:6px;margin-top:16px;'>5. Recent Server Log Entries (Last 25 lines):</h4>
        <pre style='background:#2E180E;padding:12px;border-radius:8px;overflow-x:auto;color:#a8a29e;font-size:11px;max-height:220px;overflow-y:auto;'>".e($recentLogs)."</pre>

        <h4 style='color:#D4AF6A;margin-bottom:6px;margin-top:16px;'>6. Application Cache Refresh:</h4>
        <pre style='background:#2E180E;padding:12px;border-radius:8px;overflow-x:auto;color:#E7C77B;font-size:12px;'>".e($cacheOutput)."</pre>
        
        <div style='background:rgba(212,175,106,0.1);padding:15px;border-radius:10px;border:1px solid #D4AF6A;margin-top:20px;'>
            <p style='color:#E7C77B;font-weight:bold;margin:0 0 10px 0;'>⚡ Quick Actions:</p>
            <a href='/rayka-deploy?token={$tokenParam}&seed=1' style='display:inline-block;background:#D4AF6A;color:#1A1412;padding:8px 16px;border-radius:6px;font-weight:bold;text-decoration:none;margin-right:10px;margin-bottom:6px;'>🔄 Sync Full Catalogue (710 Products & Multi-Angle Photos)</a>
            <a href='/rayka-deploy?token={$tokenParam}&test_mail=1' style='display:inline-block;background:#059669;color:#FAF7F0;padding:8px 16px;border-radius:6px;font-weight:bold;text-decoration:none;margin-right:10px;margin-bottom:6px;'>✉️ Test Live Email Delivery Now</a>
            <a href='/rayka-deploy?token={$tokenParam}&backup=1' style='display:inline-block;background:#1e40af;color:#FAF7F0;padding:8px 16px;border-radius:6px;font-weight:bold;text-decoration:none;margin-right:10px;margin-bottom:6px;'>💾 Run Instant Database Backup Now</a>
            <a href='/' style='display:inline-block;background:#475569;color:#fff;padding:8px 16px;border-radius:6px;font-weight:bold;text-decoration:none;'>🏠 Go to Storefront</a>
        </div>
    </div>");
})->middleware(['web', 'admin'])->name('rayka.deploy');

/*
|--------------------------------------------------------------------------
| Automated GitHub Webhook Endpoint (Hostinger-Style Instant Deploy)
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/rayka-webhook', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token') ?: $request->input('token') ?: $request->header('X-Rayka-Token');
    $secret = env('DEPLOY_SECRET', 'rayka_deploy_2026');

    if ($token !== $secret) {
        return response()->json(['error' => 'Unauthorized deployment token.'], 403);
    }

    $basePath = base_path();
    $gitOutput = '';
    if (function_exists('shell_exec')) {
        $gitOutput = (string) @shell_exec("cd {$basePath} && git pull origin main 2>&1");
    }

    $migrateOutput = '';
    if (env('DB_AUTO_MIGRATE', false) || $request->has('migrate')) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $migrateOutput = 'Migration error: ' . $e->getMessage();
        }
    }

    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $cacheOutput = \Illuminate\Support\Facades\Artisan::output();

    return response()->json([
        'success' => true,
        'message' => 'Rayka deployment completed automatically via Webhook!',
        'git' => trim($gitOutput) ?: 'Git pull executed',
        'migrate' => trim($migrateOutput),
        'cache' => trim($cacheOutput),
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('rayka.webhook');
