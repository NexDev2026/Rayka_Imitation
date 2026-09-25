<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('actor_type', 32)->default('system'); // customer, admin, system
                $table->unsignedBigInteger('actor_id')->nullable()->index();
                $table->string('actor_name')->nullable();
                $table->string('actor_email')->nullable();
                $table->string('action', 64)->index(); // ORDER_PLACED, INVENTORY_RESTORED, etc.
                $table->string('category', 32)->default('orders')->index(); // orders, inventory, auth, system
                $table->text('description');
                $table->string('subject_type', 64)->nullable()->index(); // Order, Product, User, etc.
                $table->string('subject_id', 64)->nullable()->index();
                $table->string('subject_ref', 128)->nullable()->index(); // Order #, SKU, etc.
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['created_at', 'category']);
                $table->index(['created_at', 'actor_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
