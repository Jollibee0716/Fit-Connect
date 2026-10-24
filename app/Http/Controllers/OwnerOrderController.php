<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'nullable|in:ready_for_pickup,picked_up',

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

        DB::transaction(function () use (
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

                    abort_unless(
                        $oldStatus === 'pending',
                        422,
                        'Only pending orders can be marked paid.'
                    );

                    // Owner must verify actual payment.
                    $order->status = 'paid';
                    $order->paid_at = now();

                    if (!$order->fulfillment_status) {
                        $order->fulfillment_status =
                            'awaiting_preparation';
                    }

                    break;

                // READY FOR PICKUP
                case 'ready_for_pickup':

                    abort_unless(
                        $method === 'pickup' &&
                        $oldStatus === 'paid' &&
                        $state === 'awaiting_preparation',
                        422,
                        'Paid pickup order required.'
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

                    $order->fulfillment_status = 'picked_up';
                    $order->picked_up_at = now();
                    $order->status = 'completed';
                    $order->completed_at = now();

                    break;

                // PREPARE DELIVERY
                case 'preparing':

                    abort_unless(
                        $method === 'delivery' &&
                        $oldStatus === 'paid' &&
                        $state === 'awaiting_preparation',
                        422,
                        'Paid delivery order required.'
                    );

                    $order->fulfillment_status = 'preparing';

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

                    $product = $order->product()
                        ->lockForUpdate()
                        ->first();

                    abort_unless(
                        $product,
                        422,
                        'Order product not found.'
                    );

                    abort_if(
                        $product->sold < $order->quantity,
                        422,
                        'Inventory sold count is inconsistent.'
                    );

                    // Restore reserved inventory exactly once.
                    $product->stock += $order->quantity;
                    $product->sold -= $order->quantity;
                    $product->save();

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
        });

        return response()->json([
            'ok' => true,
            'message' => 'Order updated successfully.'
        ]);
    }
}
