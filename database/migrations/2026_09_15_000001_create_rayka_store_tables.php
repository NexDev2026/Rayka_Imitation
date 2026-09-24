<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Navigation Groups (Men, Women, 1 Gram Jewellery)
        Schema::create('nav_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Men, Women, 1 Gram Jewellery
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Homepage grid image
            $table->string('hero_image')->nullable(); // Category page banner
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Category - Nav Group Pivot (Multi-assignment allowed)
        Schema::create('category_nav_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nav_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Attribute Groups (e.g. Stone Type, Metal Tone, Occasion, Plating, Size)
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Attribute Values (e.g. Stone Type -> Normal, American Diamond, Kundan, Polki)
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_group_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();
        });

        // 6. Category - Attribute Group Pivot (Which dynamic filters show on which category page)
        Schema::create('category_attribute_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_group_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 7. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('mrp', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('specifications')->nullable();
            $table->text('care_instructions')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        // 8. Product Images
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_url');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_secondary')->default(false); // For hover-swap
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 9. Product Variants (e.g. Sizes: 2.4, 2.6, 2.8, Ring Size 14, 16)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Size"
            $table->string('value'); // e.g. "2.4", "Ring Size 14"
            $table->integer('stock_quantity')->default(10);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->timestamps();
        });

        // 10. Product - Attribute Value Pivot
        Schema::create('product_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 11. Wishlists (Guest token + logged in user support)
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token')->nullable()->index();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 12. Carts
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token')->nullable()->index();
            $table->timestamps();
        });

        // 13. Cart Items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        // 14. Coupons & Offers
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_value', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 15. Addresses
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->string('address_line');
            $table->string('street')->nullable();
            $table->string('road')->nullable();
            $table->string('landmark')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 16. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token')->nullable();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', [
                'Pending Verification',
                'Confirmed',
                'Processing',
                'Shipped',
                'Delivered',
                'Rejected',
                'Cancelled',
            ])->default('Pending Verification');
            $table->string('tracking_carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 17. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku');
            $table->string('product_image')->nullable();
            $table->string('variant_info')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // 18. Payments (Static QR + Uploaded Screenshot Proof)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method')->default('Static QR / UPI');
            $table->string('screenshot_path');
            $table->string('transaction_reference')->nullable();
            $table->enum('status', ['Pending Verification', 'Confirmed', 'Rejected'])->default('Pending Verification');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        // 19. Customer Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->integer('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('comment');
            $table->boolean('is_verified_purchase')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // 20. Customer Inquiries
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('mobile')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'replied'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // 21. Home Banners
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('button_text')->default('Shop The Collection');
            $table->string('button_link')->default('/categories');
            $table->string('image_url');
            $table->string('mobile_image_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 22. Store Settings (Key-Value)
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 23. Page Views & Daily Analytics
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable();
            $table->string('url');
            $table->text('user_agent')->nullable();
            $table->date('viewed_date')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('home_banners');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('product_attribute_value');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('category_attribute_group');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attribute_groups');
        Schema::dropIfExists('category_nav_group');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('nav_groups');
    }
};
