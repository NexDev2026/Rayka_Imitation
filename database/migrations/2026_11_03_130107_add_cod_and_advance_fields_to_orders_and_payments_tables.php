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
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('Static QR / UPI')->after('notes');
            }
            if (!Schema::hasColumn('orders', 'advance_amount')) {
                $table->decimal('advance_amount', 10, 2)->default(0)->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'balance_amount')) {
                $table->decimal('balance_amount', 10, 2)->default(0)->after('advance_amount');
            }
            if (!Schema::hasColumn('orders', 'cod_extra_charge')) {
                $table->decimal('cod_extra_charge', 10, 2)->default(0)->after('balance_amount');
            }
            if (!Schema::hasColumn('orders', 'advance_percentage')) {
                $table->unsignedTinyInteger('advance_percentage')->default(100)->after('cod_extra_charge');
            }
            if (!Schema::hasColumn('orders', 'balance_status')) {
                $table->string('balance_status')->default('N/A')->after('advance_percentage');
            }
            if (!Schema::hasColumn('orders', 'delivery_payment_screenshot')) {
                $table->string('delivery_payment_screenshot')->nullable()->after('balance_status');
            }
            if (!Schema::hasColumn('orders', 'delivery_transaction_reference')) {
                $table->string('delivery_transaction_reference')->nullable()->after('delivery_payment_screenshot');
            }
            if (!Schema::hasColumn('orders', 'delivery_paid_at')) {
                $table->timestamp('delivery_paid_at')->nullable()->after('delivery_transaction_reference');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'advance_amount')) {
                $table->decimal('advance_amount', 10, 2)->nullable()->after('transaction_reference');
            }
            if (!Schema::hasColumn('payments', 'balance_amount')) {
                $table->decimal('balance_amount', 10, 2)->nullable()->after('advance_amount');
            }
            if (!Schema::hasColumn('payments', 'advance_percentage')) {
                $table->unsignedTinyInteger('advance_percentage')->nullable()->after('balance_amount');
            }
            if (!Schema::hasColumn('payments', 'delivery_screenshot_path')) {
                $table->string('delivery_screenshot_path')->nullable()->after('admin_note');
            }
            if (!Schema::hasColumn('payments', 'delivery_transaction_reference')) {
                $table->string('delivery_transaction_reference')->nullable()->after('delivery_screenshot_path');
            }
            if (!Schema::hasColumn('payments', 'delivery_paid_at')) {
                $table->timestamp('delivery_paid_at')->nullable()->after('delivery_transaction_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $cols = ['advance_amount', 'balance_amount', 'advance_percentage', 'delivery_screenshot_path', 'delivery_transaction_reference', 'delivery_paid_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $cols = ['payment_method', 'advance_amount', 'balance_amount', 'cod_extra_charge', 'advance_percentage', 'balance_status', 'delivery_payment_screenshot', 'delivery_transaction_reference', 'delivery_paid_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
