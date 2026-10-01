<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_order_buttons(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertEquals('0', StoreSetting::get('hide_order_buttons', '0'));

        // Toggle to hidden (Catalog Mode)
        $response = $this->actingAs($admin)
            ->post(route('admin.settings.toggle_order_buttons'));

        $response->assertRedirect();
        $this->assertEquals('1', StoreSetting::get('hide_order_buttons'));

        // Toggle back to visible (Live)
        $response = $this->actingAs($admin)
            ->post(route('admin.settings.toggle_order_buttons'));

        $response->assertRedirect();
        $this->assertEquals('0', StoreSetting::get('hide_order_buttons'));
    }

    public function test_cart_add_is_blocked_when_order_buttons_are_hidden(): void
    {
        StoreSetting::set('hide_order_buttons', '1');

        $category = Category::create([
            'name' => 'Necklaces',
            'slug' => 'necklaces',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Royal Bridal Choker',
            'slug' => 'royal-bridal-choker',
            'sku' => 'RAY-CHK-01',
            'price' => 1499,
            'mrp' => 2999,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_checkout_is_redirected_when_order_buttons_are_hidden(): void
    {
        StoreSetting::set('hide_order_buttons', '1');

        $response = $this->get(route('checkout'));

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('error');
    }

    public function test_product_page_renders_with_catalog_mode_notice(): void
    {
        StoreSetting::set('hide_order_buttons', '1');

        $category = Category::create([
            'name' => 'Earrings',
            'slug' => 'earrings',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kundan Jhumka',
            'slug' => 'kundan-jhumka',
            'sku' => 'RAY-JHM-01',
            'price' => 799,
            'mrp' => 1499,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Catalog Mode');
    }
}
