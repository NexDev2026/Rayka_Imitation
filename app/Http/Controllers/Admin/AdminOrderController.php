<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancelledByAdminCustomerMail;
use App\Mail\OrderCodBalanceSettledCustomerMail;
use App\Mail\OrderRefundProcessedCustomerMail;
use App\Mail\OrderStatusUpdatedCustomerMail;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\ImageUploadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('mobile', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        $pendingCount = Order::where('status', 'Pending Verification')->count();

        return view('admin.orders.index', compact('orders', 'pendingCount'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'address', 'payment', 'items.product'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function verifyPayment(Request $request, $id)
    {
        $order = Order::with(['payment', 'items.product', 'address', 'user'])->findOrFail($id);
        $oldStatus = $order->status;
        $action = $request->input('action'); // 'confirm' or 'reject'
        $adminNote = $request->input('admin_note', '');

        if ($action === 'confirm') {
            if ($order->isCancelledByCustomer()) {
                return back()->with('error', "Order #{$order->order_number} was cancelled by the customer and cannot be approved.");
            }

            $order->status = 'Confirmed';
            $order->cancelled_by = null;
            $order->cancellation_reason = null;
            $order->cancelled_at = null;
            $order->save();

            // If order was previously rejected/cancelled, re-deduct inventory
            if (in_array($oldStatus, ['Rejected', 'Cancelled'])) {
                $order->deductInventory();
            }

            if ($order->payment) {
                $order->payment->update([
                    'status' => 'Confirmed',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'admin_note' => $adminNote ?: 'Payment verified via screenshot proof by admin.',
                ]);
            }

            // Dispatch customer email with attached Tax Invoice PDF in the background
            defer(function () use ($order, $adminNote) {
                try {
                    $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                    if ($customerEmail) {
                        Mail::to($customerEmail)->send(new OrderStatusUpdatedCustomerMail($order, 'Confirmed', $adminNote, true));
                    }
                } catch (\Throwable $e) {
                    Log::warning("Order #{$order->order_number} payment confirmation email warning: ".$e->getMessage());
                }
            });

            ActivityLog::record(
                action: 'PAYMENT_CONFIRMED',
                description: 'Administrator '.(Auth::user()?->name ?? 'Admin')." verified UPI screenshot and confirmed payment for Order #{$order->order_number} (₹".number_format((float) $order->total_amount, 2).').',
                category: 'orders',
                actorType: 'admin',
                subjectType: 'Order',
                subjectId: (string) $order->id,
                subjectRef: $order->order_number,
                metadata: [
                    'order_number' => $order->order_number,
                    'total_amount' => (float) $order->total_amount,
                    'admin_note' => $adminNote,
                ]
            );

            return back()->with('success', "Order #{$order->order_number} payment has been CONFIRMED. Order is now ready for processing and Tax Invoice has been emailed to the customer.");
        } elseif ($action === 'reject') {
            $reason = trim($request->input('rejection_reason', ''));
            $comment = trim($request->input('rejection_comment', ''));
            $rejectionReason = $reason !== ''
                ? ($reason.($comment !== '' ? " — Details: {$comment}" : ''))
                : ($adminNote ?: 'Payment screenshot rejected by admin. Invalid/illegible proof.');

            $order->status = 'Rejected';
            $order->cancelled_by = 'admin';
            $order->cancellation_reason = $rejectionReason;
            $order->cancelled_at = now();
            $timestamp = now()->format('d M, Y \a\t h:i A');
            $order->notes = trim(($order->notes ?? '')."\n\nRejected by administrator on {$timestamp}. Reason: {$rejectionReason}");
            $order->save();

            // If order was active, restore inventory back to available stock
            if (! in_array($oldStatus, ['Rejected', 'Cancelled'])) {
                $order->restoreInventory();
            }

            if ($order->payment) {
                $order->payment->update([
                    'status' => 'Rejected',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'admin_note' => "Rejected by administrator on {$timestamp}. Reason: {$rejectionReason}",
                ]);
            }

            ActivityLog::record(
                action: 'PAYMENT_REJECTED',
                description: 'Administrator '.(Auth::user()?->name ?? 'Admin')." rejected payment for Order #{$order->order_number}. Reason: {$rejectionReason}",
                category: 'orders',
                actorType: 'admin',
                subjectType: 'Order',
                subjectId: (string) $order->id,
                subjectRef: $order->order_number,
                metadata: [
                    'order_number' => $order->order_number,
                    'rejection_reason' => $rejectionReason,
                ]
            );

            // Dispatch customer rejection notification email in the background
            defer(function () use ($order, $rejectionReason) {
                try {
                    $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                    if ($customerEmail) {
                        Mail::to($customerEmail)->send(new OrderCancelledByAdminCustomerMail($order, 'Rejected', $rejectionReason));
                    }
                } catch (\Throwable $e) {
                    Log::warning("Order #{$order->order_number} rejection email warning: ".$e->getMessage());
                }
            });

            return back()->with('error', "Order #{$order->order_number} payment has been REJECTED. Items returned to stock and notice emailed to customer.");
        }

        return back();
    }

    /**
     * Settle or update Cash on Delivery (COD) balance collection status.
     */
    public function settleBalance(Request $request, $id)
    {
        $order = Order::with(['payment', 'address', 'user'])->findOrFail($id);

        $request->validate([
            'balance_status' => 'required|in:Pending,Collected Cash,Paid Online,Waived',
            'delivery_transaction_reference' => 'nullable|string|max:100',
            'delivery_payment_screenshot' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif,heic|max:10240',
            'admin_balance_note' => 'nullable|string|max:500',
        ]);

        $order->balance_status = $request->balance_status;

        if ($request->filled('delivery_transaction_reference')) {
            $order->delivery_transaction_reference = trim((string) $request->delivery_transaction_reference);
        }

        if (in_array($request->balance_status, ['Collected Cash', 'Paid Online'])) {
            $order->delivery_paid_at = now();
        } elseif ($request->balance_status === 'Pending') {
            $order->delivery_paid_at = null;
        }

        if ($request->hasFile('delivery_payment_screenshot')) {
            $file = $request->file('delivery_payment_screenshot');
            $uploadDir = public_path('uploads/payments');
            if (! file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $rawExt = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = 'delivery_pay_'.time().'_'.Str::random(10).'.'.$rawExt;
            $file->move($uploadDir, $filename);
            $order->delivery_payment_screenshot = '/uploads/payments/'.$filename;
            ImageUploadService::mirrorToExternalUploads($uploadDir.DIRECTORY_SEPARATOR.$filename, 'payments', $filename);
        }

        $order->save();

        if ($order->payment) {
            $order->payment->update([
                'balance_amount' => $order->balance_amount,
                'delivery_screenshot_path' => $order->delivery_payment_screenshot,
                'delivery_transaction_reference' => $order->delivery_transaction_reference,
                'delivery_paid_at' => $order->delivery_paid_at,
                'admin_note' => $request->filled('admin_balance_note') ? trim((string) $request->admin_balance_note) : $order->payment->admin_note,
            ]);
        }

        if (in_array($order->balance_status, ['Collected Cash', 'Paid Online'])) {
            $adminBalanceNote = $request->filled('admin_balance_note') ? trim((string) $request->admin_balance_note) : null;
            defer(function () use ($order, $adminBalanceNote) {
                try {
                    $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                    if ($customerEmail) {
                        Mail::to($customerEmail)->send(new OrderCodBalanceSettledCustomerMail($order, $adminBalanceNote));
                    }
                } catch (\Throwable $e) {
                    Log::warning("Order #{$order->order_number} COD balance settlement email warning: ".$e->getMessage());
                }
            });
        }

        ActivityLog::record(
            action: 'BALANCE_STATUS_UPDATED',
            description: 'Administrator '.(Auth::user()?->name ?? 'Admin')." updated COD balance status to '{$order->balance_status}' for Order #{$order->order_number} (₹".number_format((float) $order->balance_amount, 2).').',
            category: 'orders',
            actorType: 'admin',
            subjectType: 'Order',
            subjectId: (string) $order->id,
            subjectRef: $order->order_number,
            metadata: [
                'order_number' => $order->order_number,
                'balance_amount' => (float) $order->balance_amount,
                'balance_status' => $order->balance_status,
            ]
        );

        return back()->with('success', "COD Balance for Order #{$order->order_number} marked as '{$order->balance_status}'.");
    }

    /**
     * Admin updates refund settlement details for a Cancelled order.
     */
    public function updateRefund(Request $request, $id)
    {
        $order = Order::with(['payment', 'address', 'user'])->findOrFail($id);

        if (! $order->isCancelled()) {
            return back()->with('error', 'Refund management is only available for Cancelled orders.');
        }

        $request->validate([
            'refund_status' => 'required|in:Pending,Refunded,Not Applicable',
            'refund_amount' => 'required|numeric|min:0',
            'refund_method' => 'nullable|string|max:100',
            'refund_transaction_reference' => 'nullable|string|max:100',
            'refund_screenshot' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif,pdf|max:10240',
            'refund_note' => 'nullable|string|max:1000',
        ]);

        $order->refund_status = $request->refund_status;
        $order->refund_amount = (float) $request->refund_amount;
        $order->refund_method = $request->refund_method ? trim((string) $request->refund_method) : null;
        $order->refund_transaction_reference = $request->refund_transaction_reference ? trim((string) $request->refund_transaction_reference) : null;
        $order->refund_note = $request->refund_note ? trim((string) $request->refund_note) : null;

        if ($request->refund_status === 'Refunded') {
            $order->refunded_at = $order->refunded_at ?? now();
            $order->refunded_by = Auth::id();
        } elseif ($request->refund_status === 'Pending') {
            $order->refunded_at = null;
            $order->refunded_by = null;
        }

        if ($request->hasFile('refund_screenshot')) {
            $file = $request->file('refund_screenshot');
            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext === 'pdf') {
                $filename = 'refund_' . time() . '_' . Str::random(8) . '.pdf';
                $destinationPath = public_path('uploads/refunds');
                if (! file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $path = '/uploads/refunds/' . $filename;
            } else {
                $path = ImageUploadService::uploadAndConvertToWebp(
                    file: $file,
                    folder: 'uploads/refunds',
                    prefix: 'refund',
                    maxWidth: 1600,
                    maxHeight: 1600,
                    quality: 88
                );
            }
            $order->refund_screenshot = $path;
            $fn = basename($path);
            ImageUploadService::mirrorToExternalUploads(public_path('uploads/refunds/' . $fn), 'refunds', $fn);
        }

        $order->save();

        if ($order->refund_status === 'Refunded') {
            defer(function () use ($order) {
                try {
                    $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                    if ($customerEmail) {
                        Mail::to($customerEmail)->send(new OrderRefundProcessedCustomerMail($order));
                    }
                } catch (\Throwable $e) {
                    Log::warning("Failed to dispatch refund confirmation email for order #{$order->order_number}: " . $e->getMessage());
                }
            });
        }

        ActivityLog::record(
            action: 'ORDER_REFUND_UPDATED',
            description: 'Administrator ' . (Auth::user()?->name ?? 'Admin') . " updated refund for Cancelled Order #{$order->order_number} to '{$order->refund_status}' (Amount: ₹" . number_format((float) $order->refund_amount, 2) . ").",
            category: 'orders',
            actorType: 'admin',
            subjectType: 'Order',
            subjectId: (string) $order->id,
            subjectRef: $order->order_number,
            metadata: [
                'order_number' => $order->order_number,
                'refund_status' => $order->refund_status,
                'refund_amount' => (float) $order->refund_amount,
                'refund_method' => $order->refund_method,
                'refund_transaction_reference' => $order->refund_transaction_reference,
            ]
        );

        return back()->with('success', "Refund details for Order #{$order->order_number} successfully updated to '{$order->refund_status}'.");
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with(['payment', 'items.product', 'address', 'user'])->findOrFail($id);
        $oldStatus = $order->status;
        $request->validate([
            'status' => 'required|in:Pending Verification,Confirmed,Processing,Shipped,Delivered,Rejected,Cancelled',
            'tracking_carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'cancellation_reason' => 'nullable|string|max:255',
            'cancellation_comment' => 'nullable|string|max:500',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->status;

        // If order was cancelled by customer, do not allow changing to active/dispatch statuses
        if ($order->isCancelledByCustomer() && $newStatus !== 'Cancelled') {
            return back()->with('error', "Order #{$order->order_number} was cancelled by the customer. Dispatching or status updates are disabled.");
        }

        $order->status = $newStatus;
        if ($request->has('tracking_carrier')) {
            $order->tracking_carrier = trim((string) $request->input('tracking_carrier')) ?: null;
        }
        if ($request->has('tracking_number')) {
            $order->tracking_number = trim((string) $request->input('tracking_number')) ?: null;
        }

        $reasonNote = null;
        if (in_array($newStatus, ['Cancelled', 'Rejected'])) {
            $reason = trim($request->input('cancellation_reason', ''));
            $comment = trim($request->input('cancellation_comment', ''));
            $reasonNote = $reason !== ''
                ? ($reason.($comment !== '' ? " — Details: {$comment}" : ''))
                : ($request->input('admin_note') ?: "Order marked as {$newStatus} by administrator.");

            $timestamp = now()->format('d M, Y \a\t h:i A');
            $order->cancelled_by = 'admin';
            $order->cancellation_reason = $reasonNote;
            $order->cancelled_at = now();

            if ($newStatus === 'Cancelled') {
                if ($order->refund_status === 'None' || empty($order->refund_status)) {
                    $refundable = $order->getRefundableAmount();
                    $order->refund_status = $refundable > 0 ? 'Pending' : 'Not Applicable';
                    $order->refund_amount = $refundable;
                }
            }

            $order->notes = trim(($order->notes ?? '')."\n\n{$newStatus} by administrator on {$timestamp}. Reason: {$reasonNote}");
        } elseif (in_array($oldStatus, ['Cancelled', 'Rejected'])) {
            // Reinstating/reactivating order clears cancellation attributes
            $order->cancelled_by = null;
            $order->cancellation_reason = null;
            $order->cancelled_at = null;
            $order->refund_status = 'None';
        }

        $order->save();

        // Handle inventory restore or deduction based on status transitions
        if (in_array($newStatus, ['Rejected', 'Cancelled']) && ! in_array($oldStatus, ['Rejected', 'Cancelled'])) {
            $order->restoreInventory();
        } elseif (! in_array($newStatus, ['Rejected', 'Cancelled']) && in_array($oldStatus, ['Rejected', 'Cancelled'])) {
            $order->deductInventory();
        }

        // Keep payment verification status tightly synchronized with order status
        if ($order->payment) {
            if (in_array($request->status, ['Rejected', 'Cancelled'])) {
                $order->payment->update([
                    'status' => 'Rejected',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'admin_note' => $reasonNote ?: "Order marked as {$request->status} by administrator.",
                ]);
            } elseif (in_array($request->status, ['Confirmed', 'Processing', 'Shipped', 'Delivered'])) {
                if ($order->payment->status !== 'Confirmed') {
                    $order->payment->update([
                        'status' => 'Confirmed',
                        'verified_at' => now(),
                        'verified_by' => Auth::id(),
                    ]);
                }
            } elseif ($request->status === 'Pending Verification') {
                $order->payment->update([
                    'status' => 'Pending Verification',
                    'verified_at' => null,
                    'verified_by' => null,
                ]);
            }
        }

        // Dispatch customer email notifications in the background
        defer(function () use ($order, $newStatus, $reasonNote, $request) {
            try {
                $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                if ($customerEmail) {
                    if (in_array($newStatus, ['Cancelled', 'Rejected'])) {
                        Mail::to($customerEmail)->send(new OrderCancelledByAdminCustomerMail($order, $newStatus, $reasonNote ?: "Order marked as {$newStatus} by administrator."));
                    } elseif (in_array($newStatus, ['Confirmed', 'Processing', 'Shipped', 'Delivered'])) {
                        Mail::to($customerEmail)->send(new OrderStatusUpdatedCustomerMail($order, $newStatus, $request->input('admin_note'), $newStatus === 'Confirmed'));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Order #{$order->order_number} status update email warning: ".$e->getMessage());
            }
        });

        ActivityLog::record(
            action: 'STATUS_UPDATED',
            description: 'Administrator '.(Auth::user()?->name ?? 'Admin')." updated Order #{$order->order_number} status from '{$oldStatus}' to '{$newStatus}'." . ($reasonNote ? " Reason: {$reasonNote}" : ''),
            category: 'orders',
            actorType: 'admin',
            subjectType: 'Order',
            subjectId: (string) $order->id,
            subjectRef: $order->order_number,
            metadata: [
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason_note' => $reasonNote,
            ]
        );

        $msg = "Order #{$order->order_number} status updated to '{$order->status}'.";
        if (in_array($newStatus, ['Rejected', 'Cancelled']) && ! in_array($oldStatus, ['Rejected', 'Cancelled'])) {
            $msg .= ' All ordered units have been safely returned to available stock.';
        }
        $msg .= ' Customer has been notified.';

        return back()->with('success', $msg);
    }

    public function downloadInvoice($id)
    {
        $order = Order::with(['items.product', 'payment', 'address', 'user'])->findOrFail($id);

        $storeName = StoreSetting::get('store_name', 'Rayka Imitation Jewellery');
        $storeAddress = StoreSetting::get('store_address');
        $storePhone = StoreSetting::get('store_phone');
        $storeEmail = StoreSetting::get('store_email');
        $upiId = StoreSetting::get('upi_id');

        $pdf = Pdf::loadView('pdf.invoice', compact('order', 'storeName', 'storeAddress', 'storePhone', 'storeEmail', 'upiId'));

        return $pdf->download("Rayka-Invoice-{$order->order_number}.pdf");
    }

    public function destroy($id)
    {
        $order = Order::with(['items', 'payment', 'address'])->findOrFail($id);

        // Security Guard: Only Cancelled or Rejected orders can be permanently deleted
        if (! $order->canBeDeleted()) {
            return back()->with('error', "Order #{$order->order_number} is currently protected ({$order->status}). Active orders cannot be deleted. Please cancel or reject the order before deleting.");
        }

        $orderNum = $order->order_number;
        $orderTotal = (float) $order->total_amount;
        $orderStatus = $order->status;
        $itemCount = $order->items->count();

        // Clean up attached items and payment records
        $order->items()->delete();
        if ($order->payment) {
            $order->payment->delete();
        }
        if ($order->address) {
            $order->address->delete();
        }
        $order->delete();

        ActivityLog::record(
            action: 'ORDER_DELETED',
            description: 'Administrator '.(Auth::user()?->name ?? 'Admin')." permanently deleted {$orderStatus} Order #{$orderNum} (₹".number_format($orderTotal, 2).", {$itemCount} items).",
            category: 'system',
            actorType: 'admin',
            subjectType: 'Order',
            subjectRef: $orderNum,
            metadata: [
                'order_number' => $orderNum,
                'status_at_deletion' => $orderStatus,
                'total_amount' => $orderTotal,
                'items_count' => $itemCount,
            ]
        );

        return redirect()->route('admin.orders.index')->with('success', "Order #{$orderNum} has been permanently deleted.");
    }
}
