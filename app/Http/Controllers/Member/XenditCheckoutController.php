<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class XenditCheckoutController extends Controller
{
    public function store(
        Request $request,
        XenditService $xendit
    ) {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        try {
            return DB::transaction(function () use (
                $validated,
                $user,
                $xendit
            ) {
                /*
                |--------------------------------------------------------------------------
                | 1. Find and lock the member's product order
                |--------------------------------------------------------------------------
                */

                $order = DB::table('product_orders')
                    ->where('id', $validated['order_id'])
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    return response()->json([
                        'message' => 'Order not found.',
                    ], 404);
                }

                /*
                |--------------------------------------------------------------------------
                | 2. Validate payment eligibility
                |--------------------------------------------------------------------------
                */

                if ($order->status !== 'pending') {
                    return response()->json([
                        'message' =>
                            'Only pending orders can be paid.',
                    ], 422);
                }

                // Do not allow checkout for a different payment method.
                if (
                    isset($order->method) &&
                    !in_array(
                        strtolower(trim((string) $order->method)),
                        ['xendit', 'xendit (online checkout)'],
                        true
                    )
                ) {
                    return response()->json([
                        'message' =>
                            'This order does not use Xendit as its payment method.',
                    ], 422);
                }

                /*
                |--------------------------------------------------------------------------
                | 3. Validate amount and customer
                |--------------------------------------------------------------------------
                */

                // Never accept an amount supplied by the browser.
                $amount = (float) $order->total;

                if ($amount <= 0) {
                    return response()->json([
                        'message' => 'Invalid order amount.',
                    ], 422);
                }

                if (
                    empty($user->email) ||
                    !filter_var($user->email, FILTER_VALIDATE_EMAIL)
                ) {
                    return response()->json([
                        'message' =>
                            'A valid account email is required.',
                    ], 422);
                }

                /*
                |--------------------------------------------------------------------------
                | 4. Check for an existing checkout
                |--------------------------------------------------------------------------
                */

                $existing = DB::table('xendit_checkout_sessions')
                    ->where('order_id', $order->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'ACTIVE')
                    ->whereNotNull('checkout_url')
                    ->orderByDesc('id')
                    ->first();

                if ($existing) {
                    return response()->json([
                        'ok' => true,
                        'checkout_url' => $existing->checkout_url,
                        'session_id' =>
                            $existing->payment_session_id,
                        'reference_id' =>
                            $existing->reference_id,
                        'message' =>
                            'Existing Xendit checkout retrieved.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | 5. Generate a unique payment reference
                |--------------------------------------------------------------------------
                */

                $reference = 'FC-ORDER-' .
                    $order->id . '-' .
                    Str::upper(Str::random(12));

                /*
                |--------------------------------------------------------------------------
                | 6. Create the Xendit checkout session
                |--------------------------------------------------------------------------
                */

                // IMPORTANT:
                // The fifth parameter passes the customer's name
                // to XenditService for individual_detail.
                $checkout = $xendit->createCheckout(
                    $reference,
                    $amount,
                    $user->email,
                    'FitConnect Product Order #' . $order->id,
                    $user->name ?: 'FitConnect Member'
                );

                if (
                    empty($checkout['session_id']) ||
                    empty($checkout['checkout_url'])
                ) {
                    throw new RuntimeException(
                        'Xendit returned incomplete checkout details.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 7. Save checkout details to MySQL
                |--------------------------------------------------------------------------
                */

                DB::table('xendit_checkout_sessions')
                    ->insert([
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'reference_id' => $reference,
                        'payment_session_id' =>
                            $checkout['session_id'],
                        'checkout_url' =>
                            $checkout['checkout_url'],
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'status' =>
                            $checkout['status'] ?? 'ACTIVE',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | 8. Return checkout URL
                |--------------------------------------------------------------------------
                */

                // Payment remains pending until verified by webhook.
                return response()->json([
                    'ok' => true,
                    'checkout_url' => $checkout['checkout_url'],
                    'session_id' => $checkout['session_id'],
                    'reference_id' => $reference,
                    'message' =>
                        'Xendit checkout created successfully.',
                ], 201);

            }, 3);

        } catch (Throwable $e) {

            // Log the technical error on the server.
            Log::error('FitConnect Xendit checkout failed', [
                'user_id' => $user->id,
                'order_id' => $validated['order_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' =>
                    'Unable to create checkout. Check the Laravel log for details.',
            ], 502);
        }
    }
}
