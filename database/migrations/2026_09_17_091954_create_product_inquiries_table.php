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
        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'type')) {
                $table->enum('type', ['product_inquiry', 'quote_request', 'contact'])->default('product_inquiry')->after('id');
            }
            if (! Schema::hasColumn('inquiries', 'company')) {
                $table->string('company')->nullable()->after('mobile');
            }
            if (! Schema::hasColumn('inquiries', 'source')) {
                $table->enum('source', ['web', 'whatsapp', 'chatbot'])->default('web')->after('status');
            }
            if (! Schema::hasColumn('inquiries', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            }
            if (! Schema::hasColumn('inquiries', 'follow_up_at')) {
                $table->dateTime('follow_up_at')->nullable();
            }
            if (! Schema::hasColumn('inquiries', 'is_read')) {
                $table->boolean('is_read')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
