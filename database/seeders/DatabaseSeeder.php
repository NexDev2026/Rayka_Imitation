<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AttributeGroup;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\HomeBanner;
use App\Models\Inquiry;
use App\Models\NavGroup;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PageView;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\StoreSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Full Production Catalogue if available (741 products, 11 categories, 4 banners, 1,198 images)
        $productionDataFile = database_path('seeders/rayka_production_data.json');
        if (file_exists($productionDataFile)) {
            $this->command?->info('Loading full Rayka production catalogue from rayka_production_data.json...');
            $data = json_decode(file_get_contents($productionDataFile), true);

            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            }

            foreach ($data as $table => $rows) {
                if (empty($rows)) {
                    continue;
                }
                DB::table($table)->delete();
                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
                $this->command?->line("  ✓ Seeded `{$table}` (" . count($rows) . " rows)");
            }

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            }

            $this->command?->info('Full Rayka catalogue successfully seeded into database!');

            return;
        }

        // 1. Users (Admin + Customers)
        $admin = User::create([
            'name' => 'Rayka Administrator',
            'email' => 'admin@raykajewellery.com',
            'mobile' => '9876543210',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
        ]);

        $customer1 = User::create([
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'mobile' => '9812345678',
            'role' => 'customer',
            'password' => Hash::make('secret123'),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
        ]);

        $customer2 = User::create([
            'name' => 'Aarav Patel',
            'email' => 'aarav@example.com',
            'mobile' => '9898765432',
            'role' => 'customer',
            'password' => Hash::make('secret123'),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
        ]);

        // 2. Navigation Groups
        $navMen = NavGroup::create([
            'name' => 'Men',
            'slug' => 'men',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $navWomen = NavGroup::create([
            'name' => 'Women',
            'slug' => 'women',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $nav1Gram = NavGroup::create([
            'name' => '1 Gram Jewellery',
            'slug' => '1-gram-jewellery',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 3. Categories
        $categoriesData = [
            [
                'name' => 'Chains',
                'slug' => 'chains',
                'description' => 'Royal handcrafted chains in 1 gram micro gold plating and traditional filigree link craft.',
                'image' => '/images/categories/chains.svg',
                'hero_image' => '/images/banners/banner-1.svg',
                'hero_title' => 'Imperial Royal Chains',
                'hero_subtitle' => 'Heirloom link craftsmanship with durable 1 gram micro gold sheen',
                'sort_order' => 1,
            ],
            [
                'name' => 'Rings',
                'slug' => 'rings',
                'description' => 'Regal cocktail rings, sparkling solitaire CZ, and vintage Mughal signet rings.',
                'image' => '/images/categories/rings.svg',
                'hero_image' => '/images/banners/banner-2.svg',
                'hero_title' => 'Signature Heritage Rings',
                'hero_subtitle' => 'Statement pieces set with high-grade zircon and polki accents',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bracelets & Kadas',
                'slug' => 'bracelets-kadas',
                'description' => 'Heavy Rajputana lion-face kadas, openable cuff bracelets, and delicate gold banglets.',
                'image' => '/images/categories/bracelets-kadas.svg',
                'hero_image' => '/images/banners/banner-3.svg',
                'hero_title' => 'Regal Bracelets & Kadas',
                'hero_subtitle' => 'Solid brass foundation bathed in 24k long-life micron plating',
                'sort_order' => 3,
            ],
            [
                'name' => 'Necklaces & Sets',
                'slug' => 'necklaces-sets',
                'description' => 'Bridal Kundan chokers, temple haar sets, and delicate festive pendant collars.',
                'image' => '/images/categories/necklaces-sets.svg',
                'hero_image' => '/images/banners/banner-1.svg',
                'hero_title' => 'Grand Bridal Necklaces & Sets',
                'hero_subtitle' => 'Uncut kundan glass work embellished with semi-precious bead clusters',
                'sort_order' => 4,
            ],
            [
                'name' => 'Earrings & Jhumkas',
                'slug' => 'earrings-jhumkas',
                'description' => 'Antique peacock jhumkas, chandbalis, and shimmering contemporary ear drops.',
                'image' => '/images/categories/earrings-jhumkas.svg',
                'hero_image' => '/images/banners/banner-2.svg',
                'hero_title' => 'Artisan Jhumkas & Chandbalis',
                'hero_subtitle' => 'Lightweight all-day comfort with grand royal presence',
                'sort_order' => 5,
            ],
            [
                'name' => 'Bangles',
                'slug' => 'bangles',
                'description' => 'Sets of 2, 4 and bridal chooda imitation bangles in exquisite lattice patterns.',
                'image' => '/images/categories/bangles.svg',
                'hero_image' => '/images/banners/banner-3.svg',
                'hero_title' => 'Traditional Designer Bangles',
                'hero_subtitle' => 'Velvet-smooth inner contours with precision carved filigree',
                'sort_order' => 6,
            ],
            [
                'name' => 'Mangalsutras',
                'slug' => 'mangalsutras',
                'description' => 'Sacred black bead chains paired with auspicious wati, diamond, and floral pendants.',
                'image' => '/images/categories/mangalsutras.svg',
                'hero_image' => '/images/banners/banner-1.svg',
                'hero_title' => 'Sacred Bond Mangalsutras',
                'hero_subtitle' => 'Timeless devotion meeting royal aesthetic grace',
                'sort_order' => 7,
            ],
            [
                'name' => 'Pendants',
                'slug' => 'pendants',
                'description' => 'Spiritual deities, floral medallions, and geometric polki lockets.',
                'image' => '/images/categories/pendants.svg',
                'hero_image' => '/images/banners/banner-2.svg',
                'hero_title' => 'Devotional & Sovereign Pendants',
                'hero_subtitle' => 'Versatile charms for everyday spiritual resonance',
                'sort_order' => 8,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::create($c);
        }

        // 4. Bind Categories to Nav Groups (Mega-Menu)
        // Men Nav Group
        $navMen->categories()->attach([
            $categories['chains']->id => ['sort_order' => 1],
            $categories['bracelets-kadas']->id => ['sort_order' => 2],
            $categories['rings']->id => ['sort_order' => 3],
            $categories['pendants']->id => ['sort_order' => 4],
        ]);

        // Women Nav Group
        $navWomen->categories()->attach([
            $categories['necklaces-sets']->id => ['sort_order' => 1],
            $categories['earrings-jhumkas']->id => ['sort_order' => 2],
            $categories['bangles']->id => ['sort_order' => 3],
            $categories['mangalsutras']->id => ['sort_order' => 4],
            $categories['rings']->id => ['sort_order' => 5],
            $categories['bracelets-kadas']->id => ['sort_order' => 6],
            $categories['pendants']->id => ['sort_order' => 7],
            $categories['chains']->id => ['sort_order' => 8],
        ]);

        // 1 Gram Jewellery Nav Group
        $nav1Gram->categories()->attach([
            $categories['chains']->id => ['sort_order' => 1],
            $categories['bangles']->id => ['sort_order' => 2],
            $categories['necklaces-sets']->id => ['sort_order' => 3],
            $categories['mangalsutras']->id => ['sort_order' => 4],
            $categories['bracelets-kadas']->id => ['sort_order' => 5],
            $categories['rings']->id => ['sort_order' => 6],
        ]);

        // 5. Dynamic Attribute Groups & Values
        $stoneGroup = AttributeGroup::create(['name' => 'Stone Type', 'slug' => 'stone-type', 'sort_order' => 1]);
        $stoneNormal = AttributeValue::create(['attribute_group_id' => $stoneGroup->id, 'value' => 'Normal / Plain Metal']);
        $stoneAD = AttributeValue::create(['attribute_group_id' => $stoneGroup->id, 'value' => 'American Diamond (CZ)']);
        $stoneKundan = AttributeValue::create(['attribute_group_id' => $stoneGroup->id, 'value' => 'Kundan & Polki']);
        $stoneRuby = AttributeValue::create(['attribute_group_id' => $stoneGroup->id, 'value' => 'Ruby & Emerald CZ']);
        $stonePearl = AttributeValue::create(['attribute_group_id' => $stoneGroup->id, 'value' => 'South Sea Pearl']);

        $metalGroup = AttributeGroup::create(['name' => 'Metal Tone / Finish', 'slug' => 'metal-tone', 'sort_order' => 2]);
        $metal1Gram = AttributeValue::create(['attribute_group_id' => $metalGroup->id, 'value' => '1 Gram Micro Gold Plated']);
        $metalAntique = AttributeValue::create(['attribute_group_id' => $metalGroup->id, 'value' => 'Antique Matte Gold']);
        $metalRose = AttributeValue::create(['attribute_group_id' => $metalGroup->id, 'value' => 'Rose Gold Finish']);
        $metalSilver = AttributeValue::create(['attribute_group_id' => $metalGroup->id, 'value' => 'Rhodium Silver']);

        $occasionGroup = AttributeGroup::create(['name' => 'Occasion', 'slug' => 'occasion', 'sort_order' => 3]);
        $occBridal = AttributeValue::create(['attribute_group_id' => $occasionGroup->id, 'value' => 'Bridal & Wedding']);
        $occFestive = AttributeValue::create(['attribute_group_id' => $occasionGroup->id, 'value' => 'Festive Celebration']);
        $occDaily = AttributeValue::create(['attribute_group_id' => $occasionGroup->id, 'value' => 'Daily Office Wear']);
        $occGift = AttributeValue::create(['attribute_group_id' => $occasionGroup->id, 'value' => 'Anniversary & Gifting']);

        $sizeGroup = AttributeGroup::create(['name' => 'Size / Length', 'slug' => 'size-length', 'sort_order' => 4]);
        $sizeAdj = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => 'Adjustable Cord / Free Size']);
        $size18 = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => '18 Inches']);
        $size22 = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => '22 Inches']);
        $size24 = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => '24 Inches']);
        $sizeBangle24 = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => '2.4 Size']);
        $sizeBangle26 = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => '2.6 Size']);
        $sizeBangle28 = AttributeValue::create(['attribute_group_id' => $sizeGroup->id, 'value' => '2.8 Size']);

        // Bind Attribute Groups to Categories for Dynamic Filters
        $allCatIds = collect($categories)->pluck('id');
        foreach ($allCatIds as $catId) {
            // Every category gets Stone Type, Metal Tone, and Occasion
            Category::find($catId)->attributeGroups()->syncWithoutDetaching([
                $stoneGroup->id,
                $metalGroup->id,
                $occasionGroup->id,
                $sizeGroup->id,
            ]);
        }

        // 6. Products
        $productsData = [
            [
                'category' => 'necklaces-sets',
                'name' => 'Rajputana Royal Kundan Choker Set with Matching Jhumkas',
                'sku' => 'RAY-NCK-001',
                'price' => 2499.00,
                'mrp' => 4999.00,
                'stock_quantity' => 15,
                'is_featured' => true,
                'is_trending' => true,
                'short_description' => 'Majestic handcrafted Kundan choker embellished with teardrop green onyx beads and micro pearls.',
                'description' => 'Step into regal glory with the Rajputana Royal Kundan Choker Set. Handcrafted by heritage artisans in Rajasthan, this choker combines intricate meenakari backing with uncut polki glass stones, finished with high grade gold plating and hand-strung emerald green droplets. Comes with matching jhumkas and adjustable dori.',
                'specifications' => "Material: Pure Brass alloy with 1 Gram Micro Gold Plating\nStones: Uncut Polki Glass, High-grade CZ & Synthetic Emerald beads\nClosure: Handwoven adjustable golden zari dori\nEarring Closure: Push-back with rubber stopper\nPackage Includes: 1 Choker Necklace, 1 Pair Earrings, Authenticity Guarantee Card",
                'care_instructions' => 'Keep away from moisture, perfumes, lotions, and chemical sprays. Store in airtight zip lock pouches or the velvet case provided. Gently wipe with clean dry cotton cloth after use.',
                'images' => ['/images/products/p1-a.svg', '/images/products/p1-b.svg'],
                'attributes' => [$stoneKundan->id, $metal1Gram->id, $occBridal->id, $sizeAdj->id],
                'variants' => ['Standard Set', 'With Maang Tikka (+ ₹300)'],
            ],
            [
                'category' => 'chains',
                'name' => '1 Gram Micro Gold Plated Men Royal Curb Link Chain (22 Inch)',
                'sku' => 'RAY-CHN-002',
                'price' => 1299.00,
                'mrp' => 2499.00,
                'stock_quantity' => 25,
                'is_featured' => true,
                'is_trending' => true,
                'short_description' => 'Substantial 6mm curb link chain with high-luster 1 gram gold plating engineered for daily wear.',
                'description' => 'The definitive masculine accessory. Engineered with solid interlocking curb links and double micron 24k gold bath, this chain offers the unmistakable weight and shine of real gold jewellery without the anxiety. Equipped with a sturdy S-hook clasp.',
                'specifications' => "Length: 22 Inches (56 cm)\nWidth: 6 mm thickness\nPlating: 1 Gram Micro Pure Gold Plating\nBase Metal: Hypoallergenic Copper-Brass alloy\nClasp: Sturdy sovereign S-hook",
                'care_instructions' => 'Wipe with soft microfiber cloth after sweating. Avoid wearing during swimming or bathing. Store separately to avoid scratching.',
                'images' => ['/images/products/p2-a.svg', '/images/products/p2-b.svg'],
                'attributes' => [$stoneNormal->id, $metal1Gram->id, $occDaily->id, $size22->id],
                'variants' => ['20 Inch Length', '22 Inch Length', '24 Inch Length'],
            ],
            [
                'category' => 'earrings-jhumkas',
                'name' => 'Mayur Temple Gold Antique Jhumkas with Ruby CZ Drops',
                'sku' => 'RAY-EAR-003',
                'price' => 899.00,
                'mrp' => 1799.00,
                'stock_quantity' => 18,
                'is_featured' => true,
                'is_trending' => false,
                'short_description' => 'Intricately embossed peacock motif temple jhumkas with tinkling gold bead hangings.',
                'description' => 'A tribute to classical South Indian temple heritage. Featuring dancing peacock finials, delicate filigree bell domes, and dangling ruby-red synthetic gemstones that sway with every step.',
                'specifications' => "Length: 6 cm\nWeight: 22 grams (pair)\nFinish: Antique Matte Temple Gold\nStones: Lab-created Ruby CZ and Pearl Seed drops\nBacking: Smooth Bombay screw back",
                'care_instructions' => 'Store in individual soft cloth bags. Avoid contact with hair sprays and alcohol-based cosmetics.',
                'images' => ['/images/products/p3-a.svg', '/images/products/p3-b.svg'],
                'attributes' => [$stoneRuby->id, $metalAntique->id, $occFestive->id, $sizeAdj->id],
                'variants' => ['Antique Gold Finish', 'Antique Matte Finish'],
            ],
            [
                'category' => 'rings',
                'name' => 'Imperial Maharani CZ Floral Cocktail Ring (Adjustable)',
                'sku' => 'RAY-RNG-004',
                'price' => 649.00,
                'mrp' => 1299.00,
                'stock_quantity' => 30,
                'is_featured' => false,
                'is_trending' => true,
                'short_description' => 'Dazzling floral statement ring encrusted with brilliant cut Swiss cubic zirconias.',
                'description' => 'Crown your fingers with the Imperial Maharani ring. The blooming flower silhouette catches light from every angle, featuring a raised center solitaire surrounded by concentric halos of micro-pave stones.',
                'specifications' => "Crown Diameter: 2.8 cm\nSize: Universal Adjustable Band (fits Indian sizes 12 to 22)\nStones: AAA Grade Swiss Cubic Zirconia\nPlating: 1 Gram Micro Yellow Gold Plating",
                'care_instructions' => 'Remove while washing hands or applying sanitizer to preserve crystal brilliance.',
                'images' => ['/images/products/p4-a.svg', '/images/products/p4-b.svg'],
                'attributes' => [$stoneAD->id, $metal1Gram->id, $occFestive->id, $sizeAdj->id],
                'variants' => ['Yellow Gold Tone', 'Rose Gold Tone'],
            ],
            [
                'category' => 'bangles',
                'name' => 'Traditional 1 Gram Gold Designer Bangles (Set of 4)',
                'sku' => 'RAY-BNG-005',
                'price' => 1899.00,
                'mrp' => 3799.00,
                'stock_quantity' => 12,
                'is_featured' => true,
                'is_trending' => true,
                'short_description' => 'Set of four heirloom textured bangles with floral laser-cut detailing and velvet-smooth edges.',
                'description' => 'Designed to evoke the timeless grace of 22k heirloom jewellery, these bangles feature intricate jaali work and diamond-cut facets that catch the sunlight. Wear together or pair with silk thread kadas for a full bridal stack.',
                'specifications' => "Quantity: Set of 4 Bangles\nAvailable Sizes: 2.4, 2.6, 2.8\nWidth: 5 mm per bangle\nPlating: 1 Gram Triple-layer Micro Gold Plated\nBase: Premium virgin brass",
                'care_instructions' => 'Clean with soft chamois cloth. Avoid harsh friction against rough surfaces.',
                'images' => ['/images/products/p5-a.svg', '/images/products/p5-b.svg'],
                'attributes' => [$stoneNormal->id, $metal1Gram->id, $occBridal->id, $sizeBangle26->id],
                'variants' => ['2.4 Size (2-4/16 inch)', '2.6 Size (2-6/16 inch)', '2.8 Size (2-8/16 inch)'],
            ],
            [
                'category' => 'mangalsutras',
                'name' => 'Auspicious Floral Wati Mangalsutra with Dual-Line Black Beads',
                'sku' => 'RAY-MNG-006',
                'price' => 1199.00,
                'mrp' => 2299.00,
                'stock_quantity' => 20,
                'is_featured' => true,
                'is_trending' => false,
                'short_description' => 'Traditional Maharashtrian dual wati cups accented with modern CZ floral crest.',
                'description' => 'Celebrate marital sanctity with this elegant dual-line wati mangalsutra. Handcrafted auspicious black spinel crystal beads woven with micro gold beads, leading to a radiant wati pendant.',
                'specifications' => "Chain Length: 18 Inches with 2 Inch extension\nPendant Size: 3 cm width\nPlating: 24k Micron Gold Plating\nThread: Durable nylon-reinforced jewelry wire",
                'care_instructions' => 'Do not tug sharply. Remove before applying body oils or fragrances.',
                'images' => ['/images/products/p6-a.svg', '/images/products/p6-b.svg'],
                'attributes' => [$stoneAD->id, $metal1Gram->id, $occDaily->id, $size18->id],
                'variants' => ['18 Inch Short Style', '22 Inch Medium Style', '30 Inch Long Style'],
            ],
            [
                'category' => 'bracelets-kadas',
                'name' => 'Lion Face Sovereign Micro Gold Plated Men Kada (Openable)',
                'sku' => 'RAY-KDA-007',
                'price' => 1599.00,
                'mrp' => 3199.00,
                'stock_quantity' => 16,
                'is_featured' => true,
                'is_trending' => true,
                'short_description' => 'Formidable openable lion head kada with ruby crystal eyes and textured rope shank.',
                'description' => 'Symbolizing strength and royalty, this openable Kada features intricately engraved lion head terminals with red gemstone eyes and a sturdy spring hinge for easy slipping onto any wrist size.',
                'specifications' => "Inner Diameter: 6.5 cm (Fits standard to broad wrists)\nMechanism: Concealed spring hinge openable lock\nFinish: 1 Gram Micro Gold with Antique oxidation accents\nWeight: 48 grams solid feel",
                'care_instructions' => 'Clean with damp cloth and dry immediately. Keep away from chlorine.',
                'images' => ['/images/products/p7-a.svg', '/images/products/p7-b.svg'],
                'attributes' => [$stoneRuby->id, $metal1Gram->id, $occFestive->id, $sizeAdj->id],
                'variants' => ['Medium (Fits 2.4 - 2.6)', 'Large (Fits 2.8 - 3.0)'],
            ],
            [
                'category' => 'pendants',
                'name' => 'Navratna Polki Medallion Pendant with 1 Gram Gold Chain',
                'sku' => 'RAY-PND-008',
                'price' => 999.00,
                'mrp' => 1999.00,
                'stock_quantity' => 22,
                'is_featured' => false,
                'is_trending' => true,
                'short_description' => 'Cosmic nine-gem circular medallion set in radiant sunburst frame with 20-inch chain.',
                'description' => 'Harmonizing Vedic astrology and royal palace elegance. The circular pendant highlights nine vibrant simulated stones representing cosmic energies, suspended from a delicate rope chain.',
                'specifications' => "Pendant Diameter: 3.2 cm\nChain Length: 20 Inches\nStones: 9 Hand-set faceted multi-color crystals\nPlating: 1 Gram Micro Gold Finish",
                'care_instructions' => 'Store flat in cotton lining to prevent tangling.',
                'images' => ['/images/products/p8-a.svg', '/images/products/p8-b.svg'],
                'attributes' => [$stoneKundan->id, $metal1Gram->id, $occGift->id, $sizeAdj->id],
                'variants' => ['With 20 Inch Chain', 'Pendant Only (- ₹250)'],
            ],
            [
                'category' => 'rings',
                'name' => 'American Diamond Solitaire Cocktail Ring with Micro Pave',
                'sku' => 'RAY-RNG-009',
                'price' => 799.00,
                'mrp' => 1599.00,
                'stock_quantity' => 35,
                'is_featured' => true,
                'is_trending' => true,
                'short_description' => '2-carat look radiant solitaire ring with double micro-pave eternity band.',
                'description' => 'Flawless brilliance that rivals genuine diamonds. The center cushion-cut stone is set in four prongs above a shimmering split shank band.',
                'specifications' => "Stone: 8mm Cushion Cut AAA Cubic Zirconia\nFinish: Rhodium Silver & Platinum Sheen\nBand Type: Comfort-fit tapered band",
                'care_instructions' => 'Gentle clean with soft toothbrush and mild soapy water once a month.',
                'images' => ['/images/products/p9-a.svg', '/images/products/p9-b.svg'],
                'attributes' => [$stoneAD->id, $metalSilver->id, $occFestive->id, $sizeAdj->id],
                'variants' => ['Rhodium Silver', 'Rose Gold', 'Yellow Gold'],
            ],
            [
                'category' => 'necklaces-sets',
                'name' => 'Bridal Heritage Long Haar Temple Set with Goddess Motif',
                'sku' => 'RAY-NCK-010',
                'price' => 3299.00,
                'mrp' => 6999.00,
                'stock_quantity' => 8,
                'is_featured' => true,
                'is_trending' => false,
                'short_description' => 'Elaborate 26-inch temple long haar featuring carved Lakshmi motifs and emerald drops.',
                'description' => 'The jewel in the bridal trousseau. Intricately carved temple pendants linked with stamped floral coins (kasu), radiating divine auspicious blessing and antique gold majesty.',
                'specifications' => "Haar Length: 26 Inches\nPendant Dimensions: 7 cm x 5 cm\nEarrings: Matching hanging jhumkas (5 cm)\nPlating: 1 Gram Micro Antique Gold Plated",
                'care_instructions' => 'Wrap in mulmul cotton cloth and keep inside wooden or hard velvet box.',
                'images' => ['/images/products/p10-a.svg', '/images/products/p10-b.svg'],
                'attributes' => [$stonePearl->id, $metalAntique->id, $occBridal->id, $size24->id],
                'variants' => ['Antique Gold Haar Set', 'With Matching Waist Belt (+ ₹1499)'],
            ],
            [
                'category' => 'chains',
                'name' => 'Micro Gold Plated Rudraksha Mala Chain for Men',
                'sku' => 'RAY-CHN-011',
                'price' => 1099.00,
                'mrp' => 2199.00,
                'stock_quantity' => 19,
                'is_featured' => false,
                'is_trending' => true,
                'short_description' => 'Authentic Panchmukhi Rudraksha beads encased in handcrafted gold caps with durable link chain.',
                'description' => 'Spiritual resonance meets royal masculine refinement. Each natural 5-faced Rudraksha bead is capped with micro gold flowerets and connected with hand-wired links.',
                'specifications' => "Bead Count: 54 + 1 Guru Bead\nBead Size: 7-8 mm\nLength: 26 Inches overhead wear\nCaps: Micro Gold Plated copper alloy",
                'care_instructions' => 'Can be worn daily. Apply a drop of sandalwood or olive oil onto beads occasionally.',
                'images' => ['/images/products/p11-a.svg', '/images/products/p11-b.svg'],
                'attributes' => [$stoneNormal->id, $metal1Gram->id, $occDaily->id, $size24->id],
                'variants' => ['24 Inch Length', '28 Inch Length'],
            ],
            [
                'category' => 'earrings-jhumkas',
                'name' => 'Chandbali Kundan Bridal Drop Earrings with Pearl Fringe',
                'sku' => 'RAY-EAR-012',
                'price' => 749.00,
                'mrp' => 1499.00,
                'stock_quantity' => 28,
                'is_featured' => true,
                'is_trending' => true,
                'short_description' => 'Crescent moon shaped chandbalis adorned with polki stones and seed pearl tassels.',
                'description' => 'A royal Mughal silhouette reimagined for modern festivities. Lightweight construction ensures maximum comfort throughout long wedding celebrations.',
                'specifications' => "Length: 7.5 cm\nWidth: 4.5 cm\nWeight: 18 grams\nStones: Uncut Polki glass and freshwater seed pearls\nPlating: High gloss 1 gram gold plating",
                'care_instructions' => 'Avoid direct spray of perfumes. Store flat in airtight container.',
                'images' => ['/images/products/p12-a.svg', '/images/products/p12-b.svg'],
                'attributes' => [$stoneKundan->id, $metal1Gram->id, $occFestive->id, $sizeAdj->id],
                'variants' => ['Emerald Green Drops', 'Ruby Pink Drops', 'Classic White Pearls'],
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $cat = $categories[$pData['category']];
            $product = Product::create([
                'category_id' => $cat->id,
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']),
                'sku' => $pData['sku'],
                'price' => $pData['price'],
                'mrp' => $pData['mrp'],
                'stock_quantity' => $pData['stock_quantity'],
                'short_description' => $pData['short_description'],
                'description' => $pData['description'],
                'specifications' => $pData['specifications'],
                'care_instructions' => $pData['care_instructions'],
                'is_featured' => $pData['is_featured'],
                'is_trending' => $pData['is_trending'],
                'is_active' => true,
                'meta_title' => "Buy {$pData['name']} Online | Rayka Imitation Jewellery",
                'meta_description' => $pData['short_description'],
            ]);

            // Add Product Images (Primary + Secondary)
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $pData['images'][0],
                'is_primary' => true,
                'is_secondary' => false,
                'sort_order' => 1,
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $pData['images'][1],
                'is_primary' => false,
                'is_secondary' => true,
                'sort_order' => 2,
            ]);

            // Add Variants
            foreach ($pData['variants'] as $idx => $vName) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => 'Option / Size',
                    'value' => $vName,
                    'stock_quantity' => rand(5, 15),
                    'price_override' => $idx > 0 ? $product->price + 200 : $product->price,
                ]);
            }

            // Bind Attributes
            $product->attributeValues()->sync($pData['attributes']);

            $createdProducts[] = $product;
        }

        // 7. Home Banners
        HomeBanner::create([
            'title' => 'ROYAL HERITAGE JEWELLERY',
            'subtitle' => 'Handcrafted Rajputana Kundan & Polki Bridal Sets with 1 Gram Micro Gold Brilliance',
            'badge_text' => '✨ EXCLUSIVE FESTIVE LAUNCH',
            'button_text' => 'SHOP THE COLLECTION',
            'button_link' => '/categories/necklaces-sets',
            'image_url' => '/images/banners/banner-1.svg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        HomeBanner::create([
            'title' => '1 GRAM MICRO GOLD CHAIN & KADAS',
            'subtitle' => 'Unmatched Masculine Elegance, Authentic Artisan Casting & Anti-Tarnish Finish',
            'badge_text' => '👑 SOVEREIGN MEN SELECTION',
            'button_text' => 'EXPLORE MEN COLLECTION',
            'button_link' => '/categories/chains',
            'image_url' => '/images/banners/banner-2.svg',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        HomeBanner::create([
            'title' => 'TIMELESS BRIDAL MASTERPIECES',
            'subtitle' => 'Dazzle in Everlasting Splendor with Authentic 100% Replacement Guarantee',
            'badge_text' => '💎 100% SECURE & VERIFIED',
            'button_text' => 'DISCOVER BRIDAL HAAR',
            'button_link' => '/categories/necklaces-sets',
            'image_url' => '/images/banners/banner-3.svg',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 8. Coupons
        Coupon::create([
            'code' => 'ROYAL10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_value' => 999,
            'max_discount' => 500,
            'usage_limit' => 500,
            'used_count' => 34,
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FESTIVE20',
            'type' => 'percentage',
            'value' => 20,
            'min_order_value' => 1999,
            'max_discount' => 1000,
            'usage_limit' => 200,
            'used_count' => 18,
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FLAT200',
            'type' => 'flat',
            'value' => 200,
            'min_order_value' => 1499,
            'max_discount' => 200,
            'usage_limit' => 300,
            'used_count' => 45,
            'expires_at' => now()->addMonths(4),
            'is_active' => true,
        ]);

        // 9. Store Settings
        StoreSetting::set('store_name', 'Rayka Imitation Jewellery');
        StoreSetting::set('tagline', 'Royal Heritage & 1 Gram Micro Gold Imitation Jewellery');
        StoreSetting::set('upi_id', 'raykajewellery@icici');
        StoreSetting::set('upi_payee_name', 'Rayka Imitation Jewellery Pvt Ltd');
        StoreSetting::set('qr_code_image', '/images/qr/sample-upi-qr.svg');
        StoreSetting::set('store_phone', '+91 98765 43210');
        StoreSetting::set('store_whatsapp', '+91 98765 43210');
        StoreSetting::set('store_email', 'care@raykajewellery.com');
        StoreSetting::set('store_address', 'Rayka Heritage Complex, Johari Bazaar, Jaipur, Rajasthan 302003');
        StoreSetting::set('free_shipping_min', '999');
        StoreSetting::set('shipping_flat_fee', '99');
        StoreSetting::set('trust_badge_1', 'Free Express Shipping above ₹999 across India');
        StoreSetting::set('trust_badge_2', '100% Secure Payment via Direct UPI Verification');
        StoreSetting::set('trust_badge_3', 'Easy 7-Day Replacement for any transit defect');
        StoreSetting::set('trust_badge_4', 'Heritage Quality: 1 Gram Triple-layer Micro Plating');

        // 10. Customer Reviews
        $sampleReviews = [
            [
                'product_id' => $createdProducts[0]->id,
                'customer_name' => 'Meenakshi Rathore',
                'customer_email' => 'meenakshi@example.com',
                'rating' => 5,
                'title' => 'Looks exactly like real Kundan!',
                'comment' => 'I wore this Rajputana Choker set to my cousin’s wedding and received non-stop compliments. The green beads and gold finish look like pure 22k gold heirloom jewellery. Packaging was royal!',
                'is_verified_purchase' => true,
                'status' => 'approved',
            ],
            [
                'product_id' => $createdProducts[1]->id,
                'customer_name' => 'Vikramaditya Singh',
                'customer_email' => 'vikram@example.com',
                'rating' => 5,
                'title' => 'Solid weight and royal curb link polish',
                'comment' => 'The curb link chain has genuine weight and high polish. Have been wearing it daily for 3 weeks and zero fading. Fast delivery to Udaipur.',
                'is_verified_purchase' => true,
                'status' => 'approved',
            ],
            [
                'product_id' => $createdProducts[2]->id,
                'customer_name' => 'Ananya Iyer',
                'customer_email' => 'ananya@example.com',
                'rating' => 5,
                'title' => 'Gorgeous temple jhumkas!',
                'comment' => 'The peacock detailing is so sharp and clean. Very lightweight on the ears even though it looks very heavy. Worth every rupee.',
                'is_verified_purchase' => true,
                'status' => 'approved',
            ],
            [
                'product_id' => $createdProducts[4]->id,
                'customer_name' => 'Sunita Agarwal',
                'customer_email' => 'sunita@example.com',
                'rating' => 5,
                'title' => 'Perfect 2.6 size bangles',
                'comment' => 'The 1 gram micro plating is so bright and rich. The floral laser cutwork is exquisite. Thank you Rayka Jewellery!',
                'is_verified_purchase' => true,
                'status' => 'approved',
            ],
            [
                'product_id' => $createdProducts[6]->id,
                'customer_name' => 'Rajveer Shekhawat',
                'customer_email' => 'rajveer@example.com',
                'rating' => 5,
                'title' => 'Lion Kada makes a bold statement',
                'comment' => 'Awesome lion head carving with red ruby eyes. Sturdy spring mechanism makes it very easy to put on and take off. 10/10.',
                'is_verified_purchase' => true,
                'status' => 'approved',
            ],
        ];

        foreach ($sampleReviews as $rev) {
            Review::create($rev);
        }

        // 11. Customer Inquiries
        Inquiry::create([
            'name' => 'Kavita Deshmukh',
            'email' => 'kavita.d@example.com',
            'mobile' => '9822334455',
            'subject' => 'Custom Bridal Kundan Trousseau Booking',
            'message' => 'Hello Rayka team, I would like to order 5 matching Kundan sets for my bridesmaids for December wedding. Can you assist with bulk pricing?',
            'status' => 'new',
        ]);

        Inquiry::create([
            'name' => 'Rohit Verma',
            'email' => 'rohit.v@example.com',
            'mobile' => '9811223344',
            'subject' => 'Query about 1 Gram Gold warranty',
            'message' => 'Hi, how long does the 1 gram micro gold plating last with regular usage? Do you provide a re-plating service?',
            'status' => 'replied',
            'admin_notes' => 'Replied via WhatsApp: 1-2 years lifetime with care; re-polishing support provided.',
        ]);

        // 12. Sample Addresses
        $addr1 = Address::create([
            'user_id' => $customer1->id,
            'name' => 'Priya Sharma',
            'mobile' => '9812345678',
            'email' => 'priya@example.com',
            'address_line' => 'Flat 402, Royal Palms Residency',
            'street' => 'MG Road',
            'road' => 'Near Central Mall',
            'landmark' => 'Shiv Mandir',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'pincode' => '302015',
            'is_default' => true,
        ]);

        // 13. Sample Orders & Payments (With QR Verification Workflow)
        // Order 1: Confirmed with verified payment
        $order1 = Order::create([
            'order_number' => 'RAY-'.date('Ymd').'-1001',
            'user_id' => $customer1->id,
            'address_id' => $addr1->id,
            'subtotal' => 3398.00,
            'coupon_discount' => 200.00,
            'coupon_code' => 'FLAT200',
            'shipping_fee' => 0.00,
            'total_amount' => 3198.00,
            'status' => 'Confirmed',
            'tracking_carrier' => 'Blue Dart Express',
            'tracking_number' => 'BLUEDART-88239102',
            'notes' => 'Customer requested careful velvet gift box packaging.',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[0]->id,
            'product_name' => $createdProducts[0]->name,
            'product_sku' => $createdProducts[0]->sku,
            'product_image' => $createdProducts[0]->effective_primary_image,
            'variant_info' => 'Standard Set',
            'unit_price' => 2499.00,
            'quantity' => 1,
            'subtotal' => 2499.00,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[2]->id,
            'product_name' => $createdProducts[2]->name,
            'product_sku' => $createdProducts[2]->sku,
            'product_image' => $createdProducts[2]->effective_primary_image,
            'variant_info' => 'Antique Gold Finish',
            'unit_price' => 899.00,
            'quantity' => 1,
            'subtotal' => 899.00,
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'payment_method' => 'Static QR / UPI',
            'screenshot_path' => '/images/qr/sample-upi-qr.svg',
            'transaction_reference' => 'UPI/428901839210/PAY',
            'status' => 'Confirmed',
            'verified_at' => Carbon::now()->subDays(2)->addHours(1),
            'verified_by' => $admin->id,
            'admin_note' => 'Payment received in ICICI account ₹3,198 on time. Verified.',
        ]);

        // Order 2: Pending Verification (Shows up in Admin Queue!)
        $order2 = Order::create([
            'order_number' => 'RAY-'.date('Ymd').'-1002',
            'user_id' => $customer2->id,
            'address_id' => $addr1->id,
            'subtotal' => 1599.00,
            'coupon_discount' => 0.00,
            'shipping_fee' => 0.00,
            'total_amount' => 1599.00,
            'status' => 'Pending Verification',
            'notes' => 'Urgent delivery requested for Saturday function.',
            'created_at' => Carbon::now()->subHours(4),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[6]->id,
            'product_name' => $createdProducts[6]->name,
            'product_sku' => $createdProducts[6]->sku,
            'product_image' => $createdProducts[6]->effective_primary_image,
            'variant_info' => 'Medium (Fits 2.4 - 2.6)',
            'unit_price' => 1599.00,
            'quantity' => 1,
            'subtotal' => 1599.00,
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'payment_method' => 'Static QR / UPI',
            'screenshot_path' => '/images/qr/sample-upi-qr.svg',
            'transaction_reference' => 'GPay-Ref-99201948',
            'status' => 'Pending Verification',
            'admin_note' => 'Awaiting admin verification.',
        ]);
    }
}
