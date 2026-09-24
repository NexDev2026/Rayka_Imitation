<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Composite index for the paginated navGroup + category queries
            // Covers: WHERE category_id IN (...) AND is_active = 1 ORDER BY created_at DESC
            if (! $this->hasIndex('products', 'products_category_active_created_index')) {
                $table->index(['category_id', 'is_active', 'created_at'], 'products_category_active_created_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_active_created_index');
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        return collect($indexes)->contains('name', strtolower($indexName));
    }
};
