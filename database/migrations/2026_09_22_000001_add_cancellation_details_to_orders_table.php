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
            $table->string('cancelled_by', 50)->nullable()->after('status');
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
        });

        // Backfill existing cancelled/rejected orders with their latest cancellation data
        $orders = \App\Models\Order::with('payment')->whereIn('status', ['Cancelled', 'Rejected'])->get();
        foreach ($orders as $order) {
            $notes = $order->notes ?? '';
            $adminNote = $order->payment?->admin_note ?? '';
            
            // Check for latest cancellation/rejection record in notes or admin_note
            $cancelledBy = null;
            $reason = null;
            $cancelledAt = $order->updated_at;

            // Collect all entries from notes and admin_note
            $combinedText = $notes . "\n" . $adminNote;
            $lines = array_filter(array_map('trim', explode("\n", $combinedText)));
            
            // Iterate backwards from the end to find the MOST RECENT cancellation/rejection entry
            foreach (array_reverse($lines) as $line) {
                if (preg_match('/(?:Cancelled|Order cancelled)\s+by\s+(user|customer)/i', $line)) {
                    $cancelledBy = 'customer';
                    if (preg_match('/Reason:\s*(.+)$/i', $line, $m)) {
                        $reason = trim($m[1]);
                    }
                    break;
                } elseif (preg_match('/(?:Cancelled|Rejected|Order marked as Cancelled|Order marked as Rejected|Payment & Order rejected)\s+by\s+(?:administrator|admin)/i', $line)) {
                    $cancelledBy = 'admin';
                    if (preg_match('/Reason:\s*(.+)$/i', $line, $m)) {
                        $reason = trim($m[1]);
                    }
                    break;
                }
            }

            if (! $cancelledBy) {
                $cancelledBy = $order->status === 'Cancelled' ? 'admin' : 'admin';
            }
            if (! $reason) {
                $reason = $adminNote ?: ($cancelledBy === 'customer' ? 'Order cancelled by user.' : 'Order cancelled by administrator.');
            }

            \Illuminate\Support\Facades\DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'cancelled_by' => $cancelledBy,
                    'cancellation_reason' => $reason,
                    'cancelled_at' => $cancelledAt,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancelled_by', 'cancellation_reason', 'cancelled_at']);
        });
    }
};
