<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Authenticate the Xendit webhook.
        $expected = config('services.xendit.webhook_token');
        $received = $request->header('x-callback-token');

        if (
            !is_string($expected) ||
            $expected === '' ||
            !is_string($received) ||
            !hash_equals($expected, $received)
        ) {
            return response()->json([
                'message' => 'Unauthorized webhook',
            ], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data', []);

        if (!in_array($event, [
            'payment_session.completed',
            'payment_session.expired',
        ], true)) {
            return response()->json([
                'message' => 'Event ignored',
            ]);
        }

        if (
            !is_array($data) ||
            empty($data['payment_session_id']) ||
            empty($data['reference_id'])
        ) {
            return response()->json([
                'message' => 'Invalid event',
            ], 422);
        }

        $expectedStatus = $event === 'payment_session.completed'
            ? 'COMPLETED'
            : 'EXPIRED';

        if (($data['status'] ?? null) !== $expectedStatus) {
            return response()->json([
                'message' => 'Session status mismatch',
            ], 422);
        }

        try {
            return DB::transaction(function () use ($event, $data) {

                // 2. Find the checkout previously created by FitConnect.
                $session = DB::table('xendit_checkout_sessions')
                    ->where('payment_session_id', $data['payment_session_id'])
                    ->where('reference_id', $data['reference_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$session) {
                    Log::warning('Unknown Xendit payment session', [
                        'session_id' => $data['payment_session_id'],
                    ]);

                    return response()->json([
                        'message' => 'Unknown payment session',
                    ], 404);
                }

                // 3. Validate all payment details.
                if (
                    ($data['session_type'] ?? null) !== 'PAY' ||
                    ($data['currency'] ?? null) !== 'PHP' ||
                    !isset($data['amount']) ||
                    !is_numeric($data['amount']) ||
                    (int) round(((float) $data['amount']) * 100) !==
                    (int) round(((float) $session->amount) * 100)
                ) {
                    Log::warning('Xendit payment details mismatch', [
                        'session_id' => $session->payment_session_id,
                    ]);

                    return response()->json([
                        'message' => 'Payment details mismatch',
                    ], 422);
                }

                // 4. Lock the corresponding order.
                $order = DB::table('product_orders')
                    ->where('id', $session->order_id)
                    ->where('user_id', $session->user_id)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    return response()->json([
                        'message' => 'Associated order not found',
                    ], 404);
                }

                // 5. Process a successful payment.
                if ($event === 'payment_session.completed') {

                    // A repeated webhook must not apply payment twice.
                    if ($session->status === 'COMPLETED') {
                        return response()->json([
                            'message' => 'Already processed',
                        ]);
                    }

                    if ($order->status === 'cancelled') {
                        Log::error('Payment received for cancelled order', [
                            'order_id' => $order->id,
                        ]);

                        return response()->json([
                            'message' => 'Payment requires reconciliation',
                        ], 409);
                    }

                    if ($order->status !== 'pending') {
                        Log::warning('Payment requires manual review', [
                            'order_id' => $order->id,
                            'status' => $order->status,
                        ]);

                        return response()->json([
                            'message' => 'Order state requires review',
                        ], 409);
                    }

                    // Do NOT deduct physical stock here.
                    // Stock remains reserved until pickup or dispatch.
                    DB::table('product_orders')
                        ->where('id', $order->id)
                        ->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'updated_at' => now(),
                        ]);

                    DB::table('xendit_checkout_sessions')
                        ->where('id', $session->id)
                        ->update([
                            'status' => 'COMPLETED',
                            'paid_at' => now(),
                            'updated_at' => now(),
                        ]);

                    return response()->json([
                        'message' => 'Payment confirmed',
                    ]);
                }

                // 6. Process an expired checkout.
                // Do NOT automatically cancel a paid order.
                if ($session->status === 'COMPLETED') {
                    return response()->json([
                        'message' => 'Already paid',
                    ]);
                }

                DB::table('xendit_checkout_sessions')
                    ->where('id', $session->id)
                    ->update([
                        'status' => 'EXPIRED',
                        'updated_at' => now(),
                    ]);

                return response()->json([
                    'message' => 'Payment session expired',
                ]);

            }, 3);

        } catch (\Throwable $e) {
            Log::error('Xendit webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed',
            ], 500);
        }
    }
}
