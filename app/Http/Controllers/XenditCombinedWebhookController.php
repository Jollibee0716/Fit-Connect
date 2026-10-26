<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Routes authenticated session events to membership or existing product webhook logic. */
class XenditCombinedWebhookController extends Controller
{
    public function handle(Request $request, XenditMembershipWebhookController $membership, XenditWebhookController $products)
    {
        $expected = config('services.xendit.webhook_token');
        $received = $request->header('x-callback-token');
        if (!is_string($expected) || $expected === '' || !is_string($received) ||
            !hash_equals($expected, $received)) {
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $sessionId = $request->input('data.payment_session_id');
        if (is_string($sessionId) && DB::table('xendit_membership_sessions')
            ->where('payment_session_id', $sessionId)->exists()) {
            return $membership->handle($request, app(\App\Services\XenditMembershipFinalizer::class));
        }
        return $products->handle($request);
    }
}
