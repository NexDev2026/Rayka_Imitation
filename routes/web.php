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
