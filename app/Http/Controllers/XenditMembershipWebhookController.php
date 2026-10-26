<?php

namespace App\Http\Controllers;

use App\Services\XenditMembershipFinalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class XenditMembershipWebhookController extends Controller
{
    public function handle(Request $request, XenditMembershipFinalizer $finalizer)
    {
        $expected = config('services.xendit.webhook_token');
        $received = $request->header('x-callback-token');
        if (!is_string($expected) || $expected === '' || !is_string($received) ||
            !hash_equals($expected, $received)) {
            return response()->json(['message'=>'Unauthorized'],401);
        }
        if (!in_array($request->input('event'), [
            'payment_session.completed','payment_session.expired','payment_session.canceled'
        ], true)) return response()->json(['message'=>'Ignored']);
        $id = $request->input('data.payment_session_id');
        if (!is_string($id) || $id === '') return response()->json(['message'=>'Invalid payload'],422);
        $record = DB::table('xendit_membership_sessions')->where('payment_session_id',$id)->first();
        if (!$record) return response()->json(['message'=>'Unknown membership session'],404);
        try {
            // Server-to-server GET is the authoritative payment status.
            $finalizer->verify((int)$record->payment_id);
            return response()->json(['ok'=>true]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message'=>'Verification failed'],503);
        }
    }
}
