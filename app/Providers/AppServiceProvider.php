<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use App\Models\NavGroup;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Detect cPanel / ServerByte / Hostinger public_html structure if deployed in shared hosting
        if (! $this->app->runningInConsole()) {
            if (is_dir(base_path('../public_html')) && file_exists(base_path('../public_html/index.php')) && ! file_exists(base_path('public/index.php'))) {
                $this->app->bind('path.public', fn () => base_path('../public_html'));
            } elseif (is_dir(base_path('public_html')) && file_exists(base_path('public_html/index.php'))) {
                $this->app->bind('path.public', fn () => base_path('public_html'));
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // Auto-run migrations on server if DB_AUTO_MIGRATE=true in .env
        if (env('DB_AUTO_MIGRATE', false)) {
            try {
                if (! \Illuminate\Support\Facades\Schema::hasTable('migrations')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                }
            } catch (\Throwable $e) {
                // Silently ignore if DB connection is not initialized yet
            }
        }

        // Register custom Brevo mail transport
        Mail::extend('brevo', function (array $config = []) {
            $apiKey = $config['key'] ?? config('services.brevo.key') ?? env('BREVO_API_KEY', '');
            $fromEmail = config('services.brevo.from_email') ?? config('mail.from.address') ?? 'noreply@lynkova.in';
            $fromName = config('services.brevo.from_name') ?? config('mail.from.name') ?? 'Rayka Imitation Jewellery';

            return new BrevoTransport($apiKey, $fromEmail, $fromName);
        });

        // -----------------------------------------------------------------
        // SaaS Security: Rate Limiters for Auth & Verification Forms
        // -----------------------------------------------------------------
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey)->response(function (Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many sign-in attempts. Please wait 1 minute before trying again.',
                    ], 429, $headers);
                }

                return back()->withInput($request->except('password'))
                    ->with('error', 'Too many sign-in attempts. Please wait 1 minute before trying again.');
            });
        });

        RateLimiter::for('admin-login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

            return Limit::perMinutes(15, 5)->by($throttleKey)->response(function (Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many admin authentication attempts. Security cooldown active for 15 minutes.',
                    ], 429, $headers);
                }

                return back()->withInput($request->except('password'))
                    ->with('error', 'Too many admin authentication attempts. Security cooldown active for 15 minutes.');
            });
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

            return Limit::perMinutes(15, 5)->by($throttleKey)->response(function (Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many password reset attempts. Please wait 15 minutes before trying again.',
                    ], 429, $headers);
                }

                return back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Too many password reset attempts. Please wait 15 minutes before trying again.');
            });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip())->response(function (Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many registration requests. Please wait a few minutes before trying again.',
                    ], 429, $headers);
                }

                return back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Too many registration requests. Please wait a few minutes before trying again.');
            });
        });

        RateLimiter::for('otp', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

            return Limit::perMinutes(10, 4)->by($throttleKey)->response(function (Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many verification code requests. Please wait 10 minutes before requesting a new OTP.',
                    ], 429, $headers);
                }

                return back()->withInput()
                    ->with('error', 'Too many verification code requests. Please wait 10 minutes before requesting a new OTP.');
            });
        });

        Paginator::defaultView('vendor.pagination.royal');
        Paginator::defaultSimpleView('vendor.pagination.royal');

        view()->composer('layouts.storefront', function ($view) {
            try {
                $navGroups = NavGroup::with(['categories' => function ($q) {
                    $q->where('categories.is_active', true)->orderBy('category_nav_group.sort_order', 'asc');
                }])->where('is_active', true)->orderBy('sort_order', 'asc')->get();

                // Bulletproof Fallback: Ensure Women and Men collections always show complete categories
                foreach ($navGroups as $group) {
                    /** @var \App\Models\NavGroup $group */
                    if ($group->slug === 'women' && $group->categories->count() < 3) {
                        $womenCats = \App\Models\Category::whereIn('slug', [
                            'necklaces-sets', 'earrings-jhumkas', 'bangles', 'mangalsutras', 'pendants', 'rings',
                        ])->where('is_active', true)->get();
                        if ($womenCats->isNotEmpty()) {
                            $group->setRelation('categories', $womenCats);
                        }
                    } elseif ($group->slug === 'men' && $group->categories->count() < 3) {
                        $menCats = \App\Models\Category::whereIn('slug', [
                            'chains', 'rings', 'bracelets', '2-kaddi', 'kadas', 'pendants', 'merrige-navrati-special',
                        ])->where('is_active', true)->get();
                        if ($menCats->isNotEmpty()) {
                            $group->setRelation('categories', $menCats);
                        }
                    }
                }

                $view->with('globalNavGroups', $navGroups);
            } catch (\Throwable $e) {
                $view->with('globalNavGroups', collect());
            }
        });

        // Automatically ensure upload and backup directories exist on host
        $this->ensureStorageDirectories();
    }

    /**
     * Automatically ensure required upload and backup directories exist with proper permissions.
     */
    private function ensureStorageDirectories(): void
    {
        $directories = [
            public_path('uploads/products'),
            public_path('uploads/categories'),
            public_path('uploads/banners'),
            public_path('uploads/documents'),
            public_path('uploads/settings'),
            storage_path('app/backups'),
        ];

        foreach ($directories as $dir) {
            if (! file_exists($dir)) {
                @mkdir($dir, 0777, true);
            }
        }

        // External backup and uploads folders (Hostinger / ServerByte structure outside public_html)
        $externalDirs = [
            base_path('../backups'),
            base_path('../rayka_uploads'),
        ];

        foreach ($externalDirs as $extDir) {
            if (! file_exists($extDir)) {
                @mkdir($extDir, 0777, true);
            }
        }
    }
}
