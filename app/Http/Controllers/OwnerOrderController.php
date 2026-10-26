<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use App\Support\Audit;
use App\Support\ProductInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OwnerOrderController extends Controller
{
    private function gymId(): int
    {
        $gym = DB::table('gyms')
            ->join(
                'gym_owners',
                'gym_owners.id',
                '=',
                'gyms.gym_owner_id'
            )
            ->where('gym_owners.user_id', auth()->id())
            ->select('gyms.id')
            ->first();

        abort_unless(
            $gym,
            403,
            'No gym found for this account.'
        );

        return (int) $gym->id;
    }

    public function index()
    {
        $orders = ProductOrder::with('user:id,name')
            ->where('gym_id', $this->gymId())
            ->latest()
            ->limit(200)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'receipt' => $o->receipt,
                'user_id' => $o->user_id,
                'name' => $o->user->name ?? 'Walk-in',
                'product' => $o->product_name,
                'quantity' => $o->quantity,
                'total' => $o->total,
                'method' => $o->method,
                'reference' => $o->reference,
                'proof_path' => $o->proof_path,
                'status' => $o->status,
                'paid_at' => $o->paid_at,
                'completed_at' => $o->completed_at,
                'created_at' => $o->created_at,

                // Pickup / Delivery information
                'fulfillment_method' =>
                    $o->fulfillment_method ?? 'pickup',

                'fulfillment_status' =>
                    $o->fulfillment_status,

                'delivery_name' =>
                    $o->delivery_name,

                'delivery_phone' =>
                    $o->delivery_phone,

                'delivery_address' =>
                    $o->delivery_address,

                'delivery_notes' =>
                    $o->delivery_notes,

                'ready_for_pickup_at' =>
                    $o->ready_for_pickup_at,

                'picked_up_at' =>
                    $o->picked_up_at,

                'out_for_delivery_at' =>
                    $o->out_for_delivery_at,

                'delivered_at' =>
                    $o->delivered_at,
            ]);

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function proof(ProductOrder $order)
    {
        // Only the owner of the order's gym can view its private payment proof.
        abort_unless(
            (int) $order->gym_id === $this->gymId(),
            403,
            'Unauthorized access to payment screenshot.'
        );

        abort_unless($order->proof_path, 404, 'No screenshot attached.');

        $disk = Storage::disk('local');
        abort_unless(
            $disk->exists($order->proof_path),
            404,
            'Screenshot file not found.'
        );

        $path = $disk->path($order->proof_path);
        abort_unless(
            is_file($path) && is_readable($path),
            404,
            'Screenshot is not readable.'
        );

        $mime = mime_content_type($path);
        abort_unless(
            in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true),
            415,
            'Unsupported screenshot format.'
        );

        // Return the exact image bytes directly instead of a BinaryFileResponse.
        $contents = file_get_contents($path);
        abort_if($contents === false, 500, 'Could not read screenshot.');

        return response($contents, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'Content-Length' => strlen($contents),
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function update(Request $request, ProductOrder $order)
    {
        $gymId = $this->gymId();

        abort_unless(
            (int) $order->gym_id === $gymId,
            403,
            'This order does not belong to your gym.'
        );

        $data = $request->validate([
            'status' =>
                'nullable|in:paid,completed,cancelled',

            'pickup_action' =>
                'nullable|in:preparing,ready_for_pickup,picked_up',

            'delivery_action' =>
                'nullable|in:preparing,out_for_delivery,delivered',
        ]);

        // Exactly one action must be submitted.
        $actions = array_filter([
            $data['status'] ?? null,
            $data['pickup_action'] ?? null,
            $data['delivery_action'] ?? null,
        ], fn ($value) => $value !== null);

        if (count($actions) !== 1) {
            throw ValidationException::withMessages([
                'action' => 'Submit exactly one order action.'
            ]);
        }

        $action = reset($actions);

        $result = DB::transaction(function () use (
            $order,
            $gymId,
            $action
        ) {
            $order = ProductOrder::where('gym_id', $gymId)
                ->lockForUpdate()
                ->findOrFail($order->id);

            $oldStatus = $order->status;
            $oldFulfillment = $order->fulfillment_status;

            $method = $order->fulfillment_method ?? 'pickup';

            $state = $order->fulfillment_status
                ?: 'awaiting_preparation';

            abort_unless(
                in_array($method, ['pickup', 'delivery'], true),
                422,
                'Invalid fulfillment method.'
            );

            // Finalized orders cannot be changed.
            abort_if(
                in_array(
                    $oldStatus,
                    ['completed', 'cancelled'],
                    true
                ),
                422,
                'This order is already finalized.'
            );

            switch ($action) {

                // MARK PAID
                case 'paid':

                    // Xendit payments may only be confirmed by a verified webhook.
                    abort_if(
                        strcasecmp((string) $order->method, 'Xendit') === 0,
                        422,
                        'Xendit payments must be verified automatically. Manual approval is blocked.'
                    );

                    abort_unless(
                        $oldStatus === 'pending',
                        422,
                        'Only pending orders can be marked paid.'
                    );

                    abort_unless(
                        $order->proof_path && filled($order->reference) &&
                        Storage::disk('local')->exists($order->proof_path),
                        422, 'Payment screenshot and reference number required.'
                    );

                    // Owner must independently verify actual funds received.
                    $order->status = 'paid';
                    $order->paid_at = now();

                    if (!$order->fulfillment_status) {
                        $order->fulfillment_status =
                            'awaiting_preparation';
                    }

                    break;

                // PREPARING - pickup and delivery
                case 'preparing':

                    abort_unless(
                        $oldStatus === 'paid' &&
                        $state === 'awaiting_preparation',
                        422,
                        'Only paid orders awaiting preparation can be prepared.'
                    );

                    $order->fulfillment_status = 'preparing';
                    break;

                // READY FOR PICKUP
                case 'ready_for_pickup':

                    abort_unless(
                        $method === 'pickup' &&
                        $oldStatus === 'paid' &&
                        $state === 'preparing',
                        422,
                        'Pickup order must be preparing first.'
                    );

                    $order->fulfillment_status =
                        'ready_for_pickup';

                    $order->ready_for_pickup_at = now();

                    break;

                // PICKED UP
                // COMPLETE is an alternative button
                case 'picked_up':
                case 'completed':

                    abort_unless(
                        $method === 'pickup' &&
                        $oldStatus === 'paid' &&
                        $state === 'ready_for_pickup',
                        422,
                        'Order must be ready for pickup first.'
                    );

                    ProductInventory::fulfill((int) $order->product_id, (int) $order->quantity);
                    $order->fulfillment_status = 'picked_up';
                    $order->picked_up_at = now();
                    $order->status = 'completed';
                    $order->completed_at = now();

                    break;

                // OUT FOR DELIVERY
                case 'out_for_delivery':

                    abort_unless(
                        $method === 'delivery' &&
                        $oldStatus === 'paid' &&
                        $state === 'preparing',
                        422,
                        'Prepare this delivery first.'
                    );

                    abort_unless(
                        filled($order->delivery_name) &&
                        filled($order->delivery_phone) &&
                        filled($order->delivery_address),
                        422,
                        'Recipient, phone and address are required.'
                    );

                    // Keep the quantity reserved until delivery is confirmed.
                    $order->fulfillment_status =
                        'out_for_delivery';

                    $order->out_for_delivery_at = now();

                    break;

                // DELIVERED
                case 'delivered':

                    abort_unless(
                        $method === 'delivery' &&
                        $oldStatus === 'paid' &&
                        $state === 'out_for_delivery',
                        422,
                        'Order must be out for delivery first.'
                    );

                    // Deduct physical stock and release the reservation only on delivery.
                    // The locked order and transaction prevent double deductions.
                    ProductInventory::fulfill((int) $order->product_id, (int) $order->quantity);
                    $order->fulfillment_status = 'delivered';
                    $order->delivered_at = now();
                    $order->status = 'completed';
                    $order->completed_at = now();

                    break;

                // CANCEL ORDER
                case 'cancelled':

                    abort_unless(
                        in_array(
                            $oldStatus,
                            ['pending', 'paid'],
                            true
                        ),
                        422,
                        'This order cannot be cancelled.'
                    );

                    abort_if(
                        $state === 'out_for_delivery',
                        422,
                        'Cannot cancel an order already dispatched.'
                    );

                    ProductInventory::release((int) $order->product_id, (int) $order->quantity);

                    $order->status = 'cancelled';
                    $order->fulfillment_status = 'cancelled';

                    break;

                default:
                    abort(422, 'Unsupported order action.');
            }

            $order->save();

            // Preserve your existing audit logging.
            Audit::log(
                'product_orders',
                $order->id,
                'updated',
                $gymId,
                "Order {$order->receipt}: {$action}",
                [
                    'status' => $oldStatus,
                    'fulfillment_status' => $oldFulfillment,
                ],
                [
                    'status' => $order->status,
                    'fulfillment_status' =>
                        $order->fulfillment_status,
                    'receipt' => $order->receipt,
                    'product' => $order->product_name,
                    'quantity' => $order->quantity,
                ]
            );

            return [
                'id' => $order->id,
                'old_status' => $oldStatus,
                'old_fulfillment' => $oldFulfillment,
                'status' => $order->status,
                'fulfillment_status' => $order->fulfillment_status,
                'method' => $method,
            ];
        });

        // Email happens AFTER the transaction commits. An email failure must
        // never undo a successfully updated customer order.
        $emailSent = false;
        $emailError = false;
        if ($result['old_fulfillment'] !== $result['fulfillment_status'] ||
            $result['old_status'] !== $result['status']) {
            $updated = ProductOrder::with('user:id,name,email')->find($result['id']);
            $recipient = $updated?->user?->email;
            if ($recipient) {
                $labels = [
                    'awaiting_preparation' => 'Awaiting preparation',
                    'preparing' => 'Preparing your order',
                    'ready_for_pickup' => 'Ready for pickup',
                    'picked_up' => 'Picked up',
                    'out_for_delivery' => 'Out for delivery',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ];
                $label = $labels[$updated->fulfillment_status] ?? ucfirst((string) $updated->status);
                $body = "Hello " . ($updated->user->name ?: 'Customer') . ",\n\n"
                    . "Your FitConnect order {$updated->receipt} has been updated.\n"
                    . "Product: {$updated->product_name}\n"
                    . "Fulfillment: " . ucfirst($updated->fulfillment_method ?: 'pickup') . "\n"
                    . "Current status: {$label}\n\n"
                    . "Sign in to FitConnect and open My Orders to see the latest status.\n\n"
                    . "Thank you,\nFitConnect";
                try {
                    Mail::raw($body, function ($message) use ($recipient, $updated, $label) {
                        $message->to($recipient)
                            ->subject("FitConnect {$updated->receipt} - {$label}");
                    });
                    $emailSent = true;
                } catch (\Throwable $e) {
                    $emailError = true;
                    Log::error('FitConnect order status email failed', [
                        'order_id' => $updated->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Order updated successfully.',
            'email_sent' => $emailSent,
            'email_failed' => $emailError,
        ]);
    }
}
