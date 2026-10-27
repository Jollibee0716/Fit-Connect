<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Legacy product purchase.
     * Use PortalController for pickup/delivery checkout.
     */
    public function buyProduct(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:10',
            'method' => 'required|in:GCash,Maya,Cash',
            'reference' => 'nullable|string|max:60',
        ]);

        $orderId = DB::transaction(function () use ($request, $data) {

            $product = DB::table('products')
                ->where('id', $data['product_id'])
                ->where('is_active', 1)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $product,
                404,
                'Product not found.'
            );

            abort_if(
                $product->stock < $data['quantity'],
                422,
                'Not enough stock.'
            );

            $orderId = DB::table('product_orders')
                ->insertGetId([
                    'gym_id' => $product->gym_id,
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $data['quantity'],
                    'total' => $product->price * $data['quantity'],
                    'method' => $data['method'],
                    'reference' => $data['reference'] ?? null,
                    'status' => 'pending',
                    'fulfillment_method' => 'pickup',
                    'fulfillment_status' => 'awaiting_preparation',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'stock' => $product->stock - $data['quantity'],
                    'sold' => $product->sold + $data['quantity'],
                    'updated_at' => now(),
                ]);

            return $orderId;
        });

        return response()->json([
            'ok' => true,
            'order_id' => $orderId,
            'message' => 'Product order submitted.',
        ], 201);
    }

    /**
     * Member requests membership.
     * Gym owner must accept the payment
     * before membership becomes active.
     */
    public function payMembership(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer',
            'method' => 'required|in:GCash,Maya,Cash',
            'reference' => 'nullable|string|max:60',
        ]);

        $plan = DB::table('membership_plans')
            ->where('id', $data['plan_id'])
            ->where('is_active', 1)
            ->first();

        abort_unless(
            $plan,
            404,
            'Membership plan not found.'
        );

        abort_unless(
            (int) $plan->days > 0,
            422,
            'Invalid membership duration.'
        );

        // Confirm the member selected the correct gym.
        $selectedGymId = (int) $request->session()
            ->get('fitconnect_selected_gym_id', 0);

        abort_unless(
            $selectedGymId &&
            (int) $plan->gym_id === $selectedGymId,
            422,
            'Select the gym that offers this plan first.'
        );

        // Prevent duplicate pending requests for the same plan.
        $existingPending = DB::table('payments')
            ->where('gym_id', $selectedGymId)
            ->where('user_id', $request->user()->id)
            ->where('type', 'membership')
            ->where('plan', $plan->name)
            ->where('status', 'pending')
            ->exists();

        abort_if(
            $existingPending,
            422,
            'You already have a pending request for this membership plan.'
        );

        // Save payment request.
        $paymentId = DB::table('payments')
            ->insertGetId([
                'gym_id' => $plan->gym_id,
                'user_id' => $request->user()->id,
                'type' => 'membership',
                'plan' => $plan->name,
                'plan_days' => $plan->days,
                'amount' => $plan->price,
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,

                // Required by your existing MySQL schema.
                // Owner approval will reset this date.
                'paid_at' => today()->toDateString(),

                // Expiration is calculated after approval.
                'due_date' => null,

                // Do not activate membership yet.
                'status' => 'pending',

                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'ok' => true,
            'payment_id' => $paymentId,
            'status' => 'pending',
            'message' => 'Membership request submitted. Waiting for gym owner approval.',
        ], 201);
    }
}
