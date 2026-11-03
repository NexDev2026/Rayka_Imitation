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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'refund_status')) {
                $table->string('refund_status')->default('None')->after('delivery_paid_at');
            }
            if (!Schema::hasColumn('orders', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->default(0)->after('refund_status');
            }
            if (!Schema::hasColumn('orders', 'refund_method')) {
                $table->string('refund_method')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('orders', 'refund_transaction_reference')) {
                $table->string('refund_transaction_reference')->nullable()->after('refund_method');
            }
            if (!Schema::hasColumn('orders', 'refund_screenshot')) {
                $table->string('refund_screenshot')->nullable()->after('refund_transaction_reference');
            }
            if (!Schema::hasColumn('orders', 'refund_note')) {
                $table->text('refund_note')->nullable()->after('refund_screenshot');
            }
            if (!Schema::hasColumn('orders', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_note');
            }
            if (!Schema::hasColumn('orders', 'refunded_by')) {
                $table->unsignedBigInteger('refunded_by')->nullable()->after('refunded_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = [
                'refund_status',
                'refund_amount',
                'refund_method',
                'refund_transaction_reference',
                'refund_screenshot',
                'refund_note',
                'refunded_at',
                'refunded_by',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
