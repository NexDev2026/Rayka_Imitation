<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_type',
        'name',
        'slug',
        'sku',
        'price',
        'mrp',
        'stock_quantity',
        'short_description',
        'description',
        'specifications',
        'care_instructions',
        'youtube_url',
        'is_featured',
        'is_trending',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const DEFAULT_CARE_INSTRUCTIONS = "Follow these simple care rules to keep your gold plated chain shiny for longer. This guide is useful for all gold plated jewellery (chains, bracelets, rings, pendants, etc.). Plating life depends mainly on water, sweat, chemicals, and storage.\n\nBest Rule: Wear last • Remove first\nAvoid: Water • Sweat • Perfume\nStorage: Separate pouch / box\n\nCare Tips (English):\n• Keep away from water: remove before bath, swimming, rain, or washing face/hands.\n• Avoid chemicals: perfume, deodorant, sanitizer, hairspray, and cleaning liquids can dull the shine.\n• Remove before workout: sweat reduces plating life faster.\n• Wear last, remove first: wear after perfume/makeup dries; remove before sleeping.\n• Wipe after use: gently clean with a soft, dry cloth every time.\n• Store separately: keep in a pouch/box to avoid scratches, rubbing, and tangling.\n• No harsh cleaning: don't use toothpaste, brushes, or strong cleaners.\n• If it gets wet: dry immediately with a soft cloth and store only when fully dry.\n• Avoid heat: keep away from long sunlight exposure and high heat areas.\n• Quick tip: If you wear your chain daily, rotate with another chain to reduce plating wear.\n\nदेखभाल के टिप्स (Hindi):\n• पानी से दूर रखें: नहाने, स्विमिंग, बारिश या फेस/हैंड वॉश के समय उतार दें।\n• केमिकल्स से बचाएं: परफ्यूम, डियो, सैनिटाइज़र, हेयर स्प्रे और क्लीनिंग लिक्विड से चमक कम हो सकती है।\n• वर्कआउट से पहले उतार दें: पसीने से प्लेटिंग जल्दी फीकी हो सकती है।\n• लास्ट में पहनें, पहले उतारें: परफ्यूम/मेकअप सूखने के बाद पहनें; सोने से पहले उतार दें।\n• यूज़ के बाद पोंछें: हर बार सॉफ्ट सूखे कपड़े से हल्का पोंछ लें।\n• अलग स्टोर करें: स्क्रैच/रगड़ और उलझने से बचाने के लिए अलग पाउच/बॉक्स में रखें।\n• हार्श क्लीनिंग न करें: टूथपेस्ट, ब्रश या स्ट्रॉन्ग क्लीनर का उपयोग न करें।\n• गीली हो जाए तो: तुरंत सुखाकर ही स्टोर करें (पूरी तरह सूखने के बाद)।\n• गर्मी से बचाएं: ज्यादा धूप और हाई-हीट जगहों से दूर रखें।\n• टिप: रोज़ पहनते हैं तो दूसरे चेन के साथ रोटेशन रखें ताकि प्लेटिंग लाइफ बेहतर रहे。\n\nSmall habits = longer shine ✨\nGold plated jewellery stays premium for longer when it's kept dry, cleaned gently, and stored safely.";

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->care_instructions)) {
                $product->care_instructions = self::DEFAULT_CARE_INSTRUCTIONS;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProductDocument::class)->orderBy('sort_order', 'asc');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function secondaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_secondary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_value');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->mrp > $this->price && $this->mrp > 0) {
            return (int) round((($this->mrp - $this->price) / $this->mrp) * 100);
        }

        return 0;
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->approvedReviews()->avg('rating') ?: 5.0);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getEffectivePrimaryImageAttribute(): string
    {
        $primary = $this->images->firstWhere('is_primary', true);
        if ($primary && ! empty($primary->image_url)) {
            return $this->resolveImageUrl($primary->image_url);
        }
        $first = $this->images->first();
        if ($first && ! empty($first->image_url)) {
            return $this->resolveImageUrl($first->image_url);
        }

        return asset('images/no-image-placeholder.svg');
    }

    public function getEffectiveSecondaryImageAttribute(): string
    {
        $secondary = $this->images->firstWhere('is_secondary', true);
        if ($secondary && ! empty($secondary->image_url)) {
            return $this->resolveImageUrl($secondary->image_url);
        }
        if ($this->images->count() > 1) {
            $second = $this->images->values()->get(1);
            if ($second && ! empty($second->image_url)) {
                return $this->resolveImageUrl($second->image_url);
            }
        }

        return $this->effective_primary_image;
    }

    public function resolveImageUrl(?string $url): string
    {
        if (empty($url)) {
            return asset('images/no-image-placeholder.svg');
        }
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return asset(ltrim($url, '/'));
    }

    public function getParsedSpecificationsAttribute(): array
    {
        if (empty($this->specifications)) {
            return [];
        }

        $decoded = json_decode($this->specifications, true);
        if (is_array($decoded)) {
            $filtered = [];
            foreach ($decoded as $k => $v) {
                if (in_array(strtolower($k), ['views', 'type'])) {
                    continue;
                }
                if (is_scalar($v) && trim((string) $v) !== '') {
                    $filtered[$k] = (string) $v;
                }
            }
            if (! empty($filtered)) {
                return $filtered;
            }
        }

        $lines = preg_split('/[\r\n]+/', (string) $this->specifications);
        $specs = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, ':')) {
                [$k, $v] = explode(':', $line, 2);
                $specs[trim($k)] = trim($v);
            } else {
                $specs[] = $line;
            }
        }

        return $specs;
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $this->youtube_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id ? "https://www.youtube-nocookie.com/embed/{$id}?rel=0&modestbranding=1" : null;
    }

    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    public function getActiveCategoryOfferAttribute(): ?CategoryOffer
    {
        if (! $this->category_id) {
            return null;
        }

        static $offersCache = null;
        if ($offersCache === null) {
            $offersCache = [];
            $activeOffers = CategoryOffer::active()
                ->with('categories')
                ->latest('starts_at')
                ->get();

            foreach ($activeOffers as $offer) {
                $catIds = $offer->categories->pluck('id')->all();
                if (empty($catIds) && $offer->category_id) {
                    $catIds = [$offer->category_id];
                }

                foreach ($catIds as $cId) {
                    if (! isset($offersCache[$cId])) {
                        $offersCache[$cId] = $offer;
                    }
                }
            }
        }

        return $offersCache[$this->category_id] ?? null;
    }

    public function getEffectivePriceAttribute(): float
    {
        $offer = $this->active_category_offer;
        if ($offer && $offer->discount_percentage > 0) {
            $discountAmount = ($this->price * $offer->discount_percentage) / 100;

            return round(max(0, $this->price - $discountAmount), 2);
        }

        return (float) $this->price;
    }

    public function getHasActiveOfferAttribute(): bool
    {
        return $this->active_category_offer !== null;
    }

    public function getEffectiveDiscountPercentAttribute(): int
    {
        $effectivePrice = $this->effective_price;
        if ($this->mrp > $effectivePrice && $this->mrp > 0) {
            return (int) round((($this->mrp - $effectivePrice) / $this->mrp) * 100);
        }

        return 0;
    }

    public function getOfferBadgeTextAttribute(): ?string
    {
        $offer = $this->active_category_offer;
        if (! $offer) {
            return null;
        }

        return $offer->badge_text ?: ('⚡ '.(int) $offer->discount_percentage.'% FLASH SALE');
    }
}
