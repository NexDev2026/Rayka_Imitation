<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CategoryOffer;
use App\Models\HomeBanner;
use App\Models\Inquiry;
use App\Models\NavGroup;
use App\Models\Product;
use App\Models\Review;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    public function index()
    {
        $banners = HomeBanner::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $newArrivals = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->latest('updated_at')
            ->take(8)
            ->get();

        $bestSellers = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest('updated_at')
            ->take(8)
            ->get();

        $trendingProducts = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->where('is_trending', true)
            ->latest('updated_at')
            ->take(8)
            ->get();

        $customerReviews = Review::where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        $trustBadges = [
            ['title' => 'Free Express Shipping', 'desc' => StoreSetting::get('trust_badge_1', 'On all orders above ₹999 across India'), 'icon' => 'truck'],
            ['title' => '100% Verified Payment', 'desc' => StoreSetting::get('trust_badge_2', 'Safe UPI QR code payment with manual verification'), 'icon' => 'shield-check'],
            ['title' => 'Easy Replacement', 'desc' => StoreSetting::get('trust_badge_3', '7-day hassle-free replacement for transit damage'), 'icon' => 'refresh'],
            ['title' => 'Heritage Quality', 'desc' => StoreSetting::get('trust_badge_4', '1 Gram Micro Gold Plating with Anti-Tarnish seal'), 'icon' => 'sparkles'],
        ];

        // Homepage Category Tabs Showcase (Customizable from Admin)
        $showcaseEnabled = StoreSetting::get('showcase_enabled', '1') == '1';
        $showcaseTitle = StoreSetting::get('showcase_title', 'Curated Royal Collections');
        $showcaseSubtitle = StoreSetting::get('showcase_subtitle', 'Select a collection below to discover hand-finished 1 gram micro gold masterpieces.');
        $showcaseLimit = (int) StoreSetting::get('showcase_limit', '10');
        $showcaseCategoryIds = json_decode(StoreSetting::get('showcase_categories', '["1","2","9","3","10"]'), true) ?: [1, 2, 9, 3, 10];

        $showcaseCategories = collect();
        if ($showcaseEnabled && ! empty($showcaseCategoryIds)) {
            $showcaseCategories = Category::whereIn('id', $showcaseCategoryIds)
                ->where('is_active', true)
                ->with(['products' => function ($query) use ($showcaseLimit) {
                    $query->where('is_active', true)
                        ->with(['images', 'category'])
                        ->latest()
                        ->take($showcaseLimit);
                }])
                ->get()
                ->sortBy(function ($cat) use ($showcaseCategoryIds) {
                    $idx = array_search((string) $cat->id, array_map('strval', $showcaseCategoryIds));

                    return $idx !== false ? $idx : 999;
                })
                ->values();
        }

        // Active Category Flash Sale / Timed Offer
        $activeOffer = CategoryOffer::active()
            ->with(['categories', 'category'])
            ->latest('starts_at')
            ->first();

        $offerProducts = collect();
        if ($activeOffer) {
            $catIds = $activeOffer->categories->pluck('id')->all();
            if (empty($catIds) && $activeOffer->category_id) {
                $catIds = [$activeOffer->category_id];
            }
            if (! empty($catIds)) {
                $offerProducts = Product::whereIn('category_id', $catIds)
                    ->where('is_active', true)
                    ->with(['images', 'category'])
                    ->latest()
                    ->take(12)
                    ->get();
            }
        }

        $videoProducts = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->whereNotNull('youtube_url')
            ->where('youtube_url', '!=', '')
            ->latest()
            ->get()
            ->filter(fn ($p) => ! empty($p->youtube_id))
            ->values();

        return view('storefront.home', compact(
            'banners',
            'categories',
            'newArrivals',
            'bestSellers',
            'trendingProducts',
            'customerReviews',
            'trustBadges',
            'showcaseEnabled',
            'showcaseTitle',
            'showcaseSubtitle',
            'showcaseCategories',
            'activeOffer',
            'offerProducts',
            'videoProducts'
        ));
    }

    public function navGroupPage($slug)
    {
        $navGroup = NavGroup::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $categories = $navGroup->categories()->where('is_active', true)->get();
        if ($slug === 'women' && $categories->count() < 3) {
            $categories = \App\Models\Category::whereIn('slug', [
                'necklaces-sets', 'earrings-jhumkas', 'bangles', 'mangalsutras', 'pendants', 'rings',
            ])->where('is_active', true)->get();
        } elseif ($slug === 'men' && $categories->count() < 3) {
            $categories = \App\Models\Category::whereIn('slug', [
                'chains', 'rings', 'bracelets', '2-kaddi', 'kadas', 'pendants', 'merrige-navrati-special',
            ])->where('is_active', true)->get();
        }
        $categoryIds = $categories->pluck('id');

        $perPage = 12;
        $requestedPage = max(1, (int) request()->get('page', 1));

        // Retry loop: SQLite on Windows can throw error 14 (CANTOPEN) transiently
        $products = $this->sqliteRetry(function () use ($categoryIds, $perPage, $requestedPage) {
            $total = Product::whereIn('category_id', $categoryIds)->where('is_active', true)->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $safePage = min($requestedPage, $lastPage);

            return Product::with(['images', 'category'])
                ->whereIn('category_id', $categoryIds)
                ->where('is_active', true)
                ->latest()
                ->paginate($perPage, ['*'], 'page', $safePage)
                ->withQueryString();
        });

        return view('storefront.nav_group', compact('navGroup', 'categories', 'products'));
    }

    /**
     * Retry a database operation up to $attempts times on SQLite CANTOPEN (HY000 error 14).
     */
    private function sqliteRetry(callable $callback, int $attempts = 3): mixed
    {
        $attempt = 0;
        while (true) {
            try {
                return $callback();
            } catch (\Illuminate\Database\QueryException $e) {
                $attempt++;
                $isCantOpen = str_contains($e->getMessage(), 'General error: 14')
                    || str_contains($e->getMessage(), 'unable to open database file');

                if ($isCantOpen && $attempt < $attempts) {
                    \Illuminate\Support\Facades\DB::reconnect();
                    usleep(100000 * $attempt); // 100ms, 200ms backoff
                    continue;
                }

                throw $e;
            }
        }
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Dynamic filters: Get attribute groups linked to this category
        $attributeGroups = $category->attributeGroups()->with('values')->orderBy('sort_order')->get();

        // Build product query
        $query = Product::with(['images', 'category', 'attributeValues'])
            ->where('category_id', $category->id)
            ->where('is_active', true);

        // Product Type filter (e.g. big-size-chain, biskit-chain, unique-chain, rudrakhs)
        $selectedType = $request->get('type');
        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        // Available types in this category for tabs & sidebar filter
        $availableTypes = Product::where('category_id', $category->id)
            ->whereNotNull('product_type')
            ->where('is_active', true)
            ->select('product_type', DB::raw('count(*) as count'))
            ->groupBy('product_type')
            ->orderBy('count', 'desc')
            ->get();

        // Dynamic Hero metadata for type
        $typeMeta = [
            'big-size-chain' => [
                'name' => 'Big Size Chain',
                'title' => 'Big Size Heavy Gold Chains',
                'subtitle' => 'Imperial Cuban, broad rope, and bold link chains in 1 gram micro gold plating',
                'hero_image' => '/storage/categories/chains_wear.webp',
                'tag' => 'Heavy Gold Collection',
            ],
            'biskit-chain' => [
                'name' => 'Biskit Chain',
                'title' => 'Classic Biskit Gold Chains',
                'subtitle' => 'Smooth flat biscuit links and diamond-cut micro gold patterns for effortless daily grace',
                'hero_image' => '/storage/categories/chains_wear.webp',
                'tag' => 'Signature Biskit Collection',
            ],
            'unique-chain' => [
                'name' => 'Unique Chain',
                'title' => 'Designer Unique Gold Chains',
                'subtitle' => 'Artistic link geometry, Figaro, and contemporary textured 1 gram micro gold chains',
                'hero_image' => '/storage/categories/chains_wear.webp',
                'tag' => 'Exclusive Designer Range',
            ],
            'rudrakhs' => [
                'name' => 'Rudrakhs',
                'title' => 'Auspicious Rudrakhs Malas & Chains',
                'subtitle' => 'Sacred 5-Mukhi Rudraksha beads with Trishul, Om, and divine gold caps',
                'hero_image' => '/storage/categories/chains_wear.webp',
                'tag' => 'Spiritual Devotion',
            ],
            'kada' => [
                'name' => 'Kada',
                'title' => 'Royal Rajputana Gold Kadas',
                'subtitle' => 'Heavy Bahubali lion head and ornate carved brass core gold kadas',
                'hero_image' => '/storage/categories/kadas_wear.webp',
                'tag' => 'Heritage Sovereign Kadas',
            ],
            'ring' => [
                'name' => 'Ring',
                'title' => 'Handcrafted Gold Rings',
                'subtitle' => 'Brilliant CZ solitaires, antique signet, and traditional royal cocktail rings',
                'hero_image' => '/storage/categories/rings_wear.webp',
                'tag' => 'Regal Finger Rings',
            ],
            '1-gram-ring' => [
                'name' => '1 Gram Ring',
                'title' => '1 Gram Micro Gold Signature Rings',
                'subtitle' => 'Precision textured signet bands and CZ royal masterworks in 24K 1 gram micro gold',
                'hero_image' => '/storage/categories/rings_wear.webp',
                'tag' => 'Signature Micro Gold',
            ],
            'merrige-navratri-special' => [
                'name' => 'Merrige & Navratri Special',
                'title' => 'Merrige & Navratri Festive Sets',
                'subtitle' => 'Grand bridal Kundan chokers, temple haar, and festive Navratri jewelry sets',
                'hero_image' => '/storage/categories/mataji_haar_wear.webp',
                'tag' => 'Bridal & Festive Royale',
            ],
            'mataji-haar' => [
                'name' => 'Mataji Haar',
                'title' => 'Sacred Mataji Haar Royal Malas',
                'subtitle' => 'Auspicious temple jewellery malas, deity worship haar, and grand sherwani necklaces in 1 gram micro gold',
                'hero_image' => '/storage/categories/mataji_haar_wear.webp',
                'tag' => 'Sacred Temple Devotion',
            ],
            '2-kaddi' => [
                'name' => '2 Kaddi Lucky',
                'title' => 'Royal 2-Kaddi Double Interlocking Luckies',
                'subtitle' => 'Traditional Gujarati 2-Kaddi link chains and heavy lucky bracelets in gleaming 1 gram micro gold',
                'hero_image' => '/storage/categories/kadi_wear.webp',
                'tag' => 'Heritage Gujarati 2-Kaddi',
            ],
            'addi-bracelet' => [
                'name' => 'Addi Bracelet',
                'title' => 'Imperial Addi Gold Bracelets',
                'subtitle' => 'Handcrafted Addi pattern links and luxury clasps with micro gold plating',
                'hero_image' => '/storage/categories/bracelets_wear.webp',
                'tag' => 'Imperial Addi Series',
            ],
            'ceramic-bracelet' => [
                'name' => 'Ceramic Bracelet',
                'title' => 'Ceramic Inlay Gold Bracelets',
                'subtitle' => 'Ultra-modern ceramic center links infused with 24K gold plating for contemporary elegance',
                'hero_image' => '/storage/categories/bracelets_wear.webp',
                'tag' => 'Ceramic Luxe Collection',
            ],
            'normal-bracelet' => [
                'name' => 'Classic Link Bracelet',
                'title' => 'Classic Link Micro Gold Bracelets',
                'subtitle' => 'Timeless curb and curb-box link bracelets engineered for smooth daily comfort',
                'hero_image' => '/storage/categories/bracelets_wear.webp',
                'tag' => 'Timeless Daily Wear',
            ],
            'rudrakhse-bracelet' => [
                'name' => 'Rudraksha Bracelet',
                'title' => 'Sacred Rudraksha Gold Bracelets',
                'subtitle' => 'Genuine energized Rudraksha beads encased in 1 gram micro gold filigree',
                'hero_image' => '/storage/categories/bracelets_wear.webp',
                'tag' => 'Divine Protection & Grace',
            ],
            'unique-bracelet' => [
                'name' => 'Designer Unique Bracelet',
                'title' => 'Designer Unique Gold Bracelets',
                'subtitle' => 'Exclusive geometric and architectural links hand-finished with triple-layer gold plating',
                'hero_image' => '/storage/categories/bracelets_wear.webp',
                'tag' => 'Exclusive Designer Range',
            ],
            'pendant' => [
                'name' => 'Royal Pendant',
                'title' => 'Royal 1 Gram Micro Gold Pendants',
                'subtitle' => 'Devotional Om, Trishul, floral, and heritage motif pendants with anti-tarnish gloss',
                'hero_image' => '/storage/products/pendants/pnd_001.webp',
                'tag' => 'Sacred & Daily Pendants',
            ],
            'mangalsutra' => [
                'name' => 'Auspicious Mangalsutra',
                'title' => 'Auspicious 1 Gram Micro Gold Mangalsutras',
                'subtitle' => 'Sacred black bead chains paired with ornate 24K micro gold pendants',
                'hero_image' => '/storage/products/mangalsutras/mgl_1-1.webp',
                'tag' => 'Sacred Nuptial Elegance',
            ],
        ];

        $activeHero = [
            'name' => $category->name,
            'title' => $category->hero_title ?: $category->name,
            'subtitle' => $category->hero_subtitle,
            'hero_image' => $category->hero_image ?: asset('images/banners/banner-1.svg'),
            'tag' => 'Heritage Collection',
        ];

        if ($selectedType && isset($typeMeta[$selectedType])) {
            $activeHero = $typeMeta[$selectedType];
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Dynamic Attribute Filters (multi-faceted: OR within same group, AND across different groups)
        if ($request->filled('attrs') && is_array($request->attrs)) {
            $selectedAttrValueIds = array_filter(array_map('intval', $request->attrs));
            if (! empty($selectedAttrValueIds)) {
                $groupedValues = AttributeValue::whereIn('id', $selectedAttrValueIds)
                    ->get()
                    ->groupBy('attribute_group_id');

                foreach ($groupedValues as $valuesInGroup) {
                    $valIds = $valuesInGroup->pluck('id')->all();
                    $query->whereHas('attributeValues', function ($q) use ($valIds) {
                        $q->whereIn('attribute_values.id', $valIds);
                    });
                }
            }
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->where('is_featured', true)->latest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $perPage = 12;

        // Retry loop: SQLite on Windows can throw error 14 (CANTOPEN) transiently
        $products = $this->sqliteRetry(function () use ($query, $request, $perPage) {
            $totalCount = (clone $query)->count();
            $lastPage = max(1, (int) ceil($totalCount / $perPage));
            $requestedPage = (int) $request->get('page', 1);
            $safePage = max(1, min($requestedPage, $lastPage));

            return $query->paginate($perPage, ['*'], 'page', $safePage)->withQueryString();
        });

        // Calculate price bounds for slider
        $minPrice = (float) (Product::where('category_id', $category->id)->min('price') ?: 0);
        $maxPrice = (float) (Product::where('category_id', $category->id)->max('price') ?: 5000);

        return view('storefront.category', compact(
            'category',
            'attributeGroups',
            'products',
            'minPrice',
            'maxPrice',
            'sort',
            'availableTypes',
            'selectedType',
            'activeHero',
            'typeMeta'
        ));
    }

    public function product($slug)
    {
        $cleanSlug = Str::slug($slug);

        $query = Product::with(['images', 'category', 'variants', 'attributeValues.group', 'approvedReviews', 'documents'])
            ->where(function ($q) use ($slug, $cleanSlug) {
                $q->where('slug', $slug)
                    ->orWhere('slug', $cleanSlug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            });

        if (! (auth()->check() && auth()->user()->role === 'admin')) {
            $query->where('is_active', true);
        }

        $product = $query->firstOrFail();

        // Related products in the same category ("More from Category")
        $relatedProducts = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view('storefront.product', compact('product', 'relatedProducts'));
    }

    public function trending()
    {
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->where('is_trending', true)
            ->latest()
            ->paginate(16);

        return view('storefront.trending', compact('products'));
    }

    public function reviews()
    {
        $reviews = Review::with('product')
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);

        $totalReviews = Review::where('status', 'approved')->count();
        $averageRating = Review::where('status', 'approved')->avg('rating') ?: 5.0;

        return view('storefront.reviews', compact('reviews', 'totalReviews', 'averageRating'));
    }

    public function contact()
    {
        $storeAddress = StoreSetting::get('store_address', 'Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad - 380026, Gujarat');
        $storePhone = StoreSetting::get('store_phone', '+91 81284 98531');
        $storeAltPhone = StoreSetting::get('store_alt_phone', '+91 93164 53838');
        $storeEmail = StoreSetting::get('store_email', 'care@raykajewellery.com');
        $storeWhatsapp = StoreSetting::get('store_whatsapp', '+91 81284 98531');
        $storeInstagramUrl = StoreSetting::get('instagram_url', 'https://www.instagram.com/rayka_imitation_amdavad/?hl=en');
        $storeInstagramHandle = StoreSetting::get('instagram_handle', '@rayka_imitation_amdavad');
        $storeGoogleMapUrl = StoreSetting::get('google_map_url', 'https://share.google/vaohJv28SH29hBV8j');

        return view('storefront.contact', compact(
            'storeAddress',
            'storePhone',
            'storeAltPhone',
            'storeEmail',
            'storeWhatsapp',
            'storeInstagramUrl',
            'storeInstagramHandle',
            'storeGoogleMapUrl'
        ));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,filter|max:255',
            'mobile' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        Inquiry::create($validated);

        return back()->with('success', 'Thank you! Your royal inquiry has been received. Our concierge will contact you shortly.');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->when($q, function ($query, $term) {
                $query->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('short_description', 'like', "%{$term}%")
                        ->orWhereHas('category', function ($cq) use ($term) {
                            $cq->where('name', 'like', "%{$term}%");
                        });
                });
            })
            ->latest()
            ->paginate(16)
            ->withQueryString();

        return view('storefront.search', compact('products', 'q'));
    }

    public function faq()
    {
        return view('storefront.pages.faq');
    }

    public function policy($page)
    {
        $validPages = ['about', 'shipping-policy', 'return-replacement-policy', 'refund-policy', 'privacy-policy', 'terms', 'faq'];
        if (! in_array($page, $validPages)) {
            abort(404);
        }

        return view("storefront.pages.{$page}");
    }
}

