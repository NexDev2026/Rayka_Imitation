<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        
        $staticPages = [
            route('home'),
            route('trending'),
            route('reviews.all'),
            route('contact'),
            route('policy', 'shipping-policy'),
            route('policy', 'return-replacement-policy'),
            route('policy', 'refund-policy'),
            route('policy', 'privacy-policy'),
            route('policy', 'terms'),
            route('policy', 'about'),
            route('faq'),
        ];

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . view('storefront.sitemap', compact('categories', 'products', 'staticPages'))->render();

        return response($content)->header('Content-Type', 'text/xml');
    }
}
