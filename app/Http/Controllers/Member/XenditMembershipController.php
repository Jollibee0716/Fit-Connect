<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\XenditMembershipFinalizer;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class XenditMembershipController extends Controller
{
    public function checkout(Request $request, XenditService $xendit)
    {
        $input = $request->validate(['plan_id'=>['required','integer','min:1']]);
        $user = $request->user();
        $gymId = (int) $request->session()->get('fitconnect_selected_gym_id', 0);
        abort_if($gymId < 1, 422, 'Select a gym first.');
        abort_unless(filter_var($user->email, FILTER_VALIDATE_EMAIL), 422, 'Valid member email required.');

        // Short database transaction: reserve a pending payment without external HTTP under a lock.
        $result = DB::transaction(function () use ($input, $user, $gymId) {
            DB::table('users')->where('id', $user->id)->lockForUpdate()->firstOrFail();
            $plan = DB::table('membership_plans')->where('id',$input['plan_id'])
                ->where('gym_id',$gymId)->where('is_active',1)->first();
            abort_unless($plan,404,'This plan is not available for the selected gym.');
            abort_if((float)$plan->price <= 0 || (int)$plan->days <= 0,422,'Invalid plan price or duration.');

            // Existing unresolved Xendit checkout for the SAME plan: reuse its URL.
            $pending = DB::table('payments')->where('gym_id',$gymId)->where('user_id',$user->id)
                ->where('type','membership')->where('status','pending')->get();
            foreach ($pending as $existingPayment) {
                $attempt = DB::table('xendit_membership_sessions')
                    ->where('payment_id',$existingPayment->id)->first();
                if ($attempt && in_array($attempt->status, ['EXPIRED','CANCELED'], true)) {
                    // Reconciled terminal attempt is no longer blocking.
                    continue;
                }
                if ($attempt && $attempt->plan_id == $plan->id && $attempt->status === 'ACTIVE' && $attempt->checkout_url) {
                    return ['reuse'=>true,'payment_id'=>$existingPayment->id,
                        'checkout_url'=>$attempt->checkout_url,'session_id'=>$attempt->payment_session_id];
                }
                throw ValidationException::withMessages([
                    'plan_id'=>'An unpaid membership request already exists. Verify or resolve it before buying another plan.',
                ]);
            }
            $accepted = DB::table('payments')->where('gym_id',$gymId)->where('user_id',$user->id)
                ->where('type','membership')->where('status','accepted')
                ->lockForUpdate()->get();
            foreach ($accepted as $p) {
                if (!$p->due_date) {
                    throw ValidationException::withMessages(['plan_id'=>'Existing membership has no expiry date; please ask the gym to review it.']);
                }
                if ($p->due_date < today()->toDateString()) continue;
                $tier = DB::table('membership_plans')->where('gym_id',$gymId)
                    ->where('name',$p->plan)->value('tier');
                if ($tier === null) {
                    throw ValidationException::withMessages(['plan_id'=>'Existing plan name could not be matched; contact gym owner.']);
                }
                if (mb_strtolower(trim($p->plan)) === mb_strtolower(trim($plan->name))) {
                    throw ValidationException::withMessages(['plan_id'=>'This plan is already active. You may buy it again after it expires.']);
                }
                if ((int)$plan->tier <= (int)$tier) {
                    throw ValidationException::withMessages(['plan_id'=>'Only a higher-tier upgrade is allowed before expiration.']);
                }
            }
            $paymentId = DB::table('payments')->insertGetId([
                'gym_id'=>$gymId,'user_id'=>$user->id,'type'=>'membership',
                'plan'=>$plan->name,'plan_days'=>$plan->days,'amount'=>$plan->price,
                'method'=>'Xendit','reference'=>null,
                // Legacy payments.paid_at is NOT NULL; status stays pending until verified.
                'paid_at'=>today()->toDateString(),'due_date'=>null,'status'=>'pending',
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $reference = 'FC-MEMBER-' . $paymentId . '-' . Str::upper(Str::random(10));
            DB::table('xendit_membership_sessions')->insert([
                'payment_id'=>$paymentId,'user_id'=>$user->id,'gym_id'=>$gymId,
                'plan_id'=>$plan->id,'plan_tier'=>$plan->tier,
                'reference_id'=>$reference,'amount'=>$plan->price,'currency'=>'PHP',
                'status'=>'INITIATING','created_at'=>now(),'updated_at'=>now(),
            ]);
            return ['reuse'=>false,'payment_id'=>$paymentId,'reference'=>$reference,
                'amount'=>(float)$plan->price,'description'=>'FitConnect Membership: ' . $plan->name];
        }, 3);

        if ($result['reuse']) return response()->json(['ok'=>true] + $result);
        try {
            $checkout = $xendit->createCheckout(
                $result['reference'], $result['amount'], $user->email,
                $result['description'], $user->name ?: 'FitConnect Member'
            );
            DB::table('xendit_membership_sessions')->where('payment_id',$result['payment_id'])
                ->where('status','INITIATING')->update([
                    'payment_session_id'=>$checkout['session_id'],
                    'checkout_url'=>$checkout['checkout_url'],
                    'status'=>$checkout['status'] ?? 'ACTIVE','updated_at'=>now(),
                ]);
            return response()->json([
                'ok'=>true,'payment_id'=>$result['payment_id'],
                'checkout_url'=>$checkout['checkout_url'],
                'message'=>'Membership checkout created. Payment is not yet confirmed.',
            ],201);
        } catch (Throwable $e) {
            // Do NOT automatically reset the pending payment: Xendit might
            // have created a session before a timeout. Reconcile first.
            report($e);
            return response()->json([
                'message'=>'Checkout could not be completed. The pending request is preserved for safe reconciliation. Check Laravel logs.',
                'payment_id'=>$result['payment_id'],
            ],502);
        }
    }

    public function verify(Request $request, XenditMembershipFinalizer $finalizer)
    {
        $data = $request->validate(['payment_id'=>['required','integer','min:1']]);
        try {
            return response()->json($finalizer->verify((int)$data['payment_id'], (int)$request->user()->id));
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message'=>'Payment could not be verified. Check Laravel logs.'],409);
        }
    }
}
