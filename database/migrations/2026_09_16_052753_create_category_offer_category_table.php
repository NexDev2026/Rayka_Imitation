<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('category_offers', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->change();
        });

        Schema::create('category_offer_category', function (Blueprint $table) {
            $table->foreignId('category_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_offer_id', 'category_id']);
        });

        // Backfill existing category offers into pivot
        $existing = DB::table('category_offers')->whereNotNull('category_id')->get();
        foreach ($existing as $item) {
            DB::table('category_offer_category')->insertOrIgnore([
                'category_offer_id' => $item->id,
                'category_id' => $item->category_id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_offer_category');

        Schema::table('category_offers', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable(false)->change();
        });
    }
};
