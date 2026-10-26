<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/** Shared by localhost verification and the authenticated Xendit webhook. */
class XenditMembershipFinalizer
{
    public function verify(int $paymentId, int $userId = 0): array
    {
        $session = DB::table('xendit_membership_sessions')
            ->where('payment_id', $paymentId)
            ->when($userId > 0, fn ($q) => $q->where('user_id', $userId))
            ->first();
        if (!$session) {
            abort(404, 'Membership checkout not found.');
        }
        if (!$session->payment_session_id) {
            abort(409, 'Checkout is still being created.');
        }

        $key = config('services.xendit.secret_key');
        if (!$key) {
            throw new RuntimeException('Xendit API key missing.');
        }
        $url = rtrim(config('services.xendit.api_url') ?: 'https://api.xendit.co', '/');
        $response = Http::withBasicAuth($key, '')->acceptJson()->timeout(20)
            ->get($url . '/sessions/' . rawurlencode($session->payment_session_id));
        if (!$response->successful()) {
            throw new RuntimeException('Xendit status lookup failed (HTTP ' . $response->status() . ').');
        }
        $remote = $response->json();
        if (!is_array($remote) ||
            ($remote['payment_session_id'] ?? '') !== $session->payment_session_id ||
            ($remote['reference_id'] ?? '') !== $session->reference_id ||
            ($remote['session_type'] ?? '') !== 'PAY' ||
            ($remote['currency'] ?? '') !== 'PHP' ||
            ($session->currency ?? '') !== 'PHP' ||
            !isset($remote['amount']) || !is_numeric($remote['amount']) ||
            (int) round((float) $remote['amount'] * 100) !== (int) round((float) $session->amount * 100)
        ) {
            throw new RuntimeException('Xendit session identity/amount mismatch.');
        }
        $status = strtoupper((string) ($remote['status'] ?? ''));
        if (!in_array($status, ['ACTIVE','COMPLETED','EXPIRED','CANCELED'], true)) {
            throw new RuntimeException('Unexpected Xendit session status.');
        }

        return DB::transaction(function () use ($session, $status) {
            // Lock user first so all purchase/verification operations serialize.
            DB::table('users')->where('id', $session->user_id)->lockForUpdate()->firstOrFail();
            $local = DB::table('xendit_membership_sessions')
                ->where('id', $session->id)->lockForUpdate()->first();
            $payment = DB::table('payments')
                ->where('id', $session->payment_id)
                ->where('user_id', $session->user_id)
                ->where('gym_id', $session->gym_id)
                ->where('type', 'membership')->lockForUpdate()->first();
            if (!$payment || !$local || strtolower((string)$payment->method) !== 'xendit' ||
                (int) round((float)$payment->amount * 100) !== (int) round((float)$session->amount * 100)) {
                throw new RuntimeException('Membership payment record mismatch.');
            }
            if ($local->status === 'COMPLETED' && $payment->status === 'accepted') {
                return ['verified'=>true,'payment_status'=>'COMPLETED','membership_status'=>'accepted','message'=>'Already verified.'];
            }
            if ($status !== 'COMPLETED') {
                if ($local->status !== 'COMPLETED') {
                    DB::table('xendit_membership_sessions')->where('id', $local->id)
                        ->update(['status'=>$status,'updated_at'=>now()]);
                }
                // Terminal unpaid attempts no longer block the next checkout.
                if (in_array($status, ['EXPIRED', 'CANCELED'], true) && $payment->status === 'pending') {
                    DB::table('payments')->where('id',$payment->id)
                        ->update(['status'=>'revoked','updated_at'=>now()]);
                }
                return ['verified'=>false,'payment_status'=>$status,'membership_status'=>$payment->status,'message'=>'No completed payment confirmed.'];
            }
            if ($payment->status !== 'pending') {
                throw new RuntimeException('Completed payment requires reconciliation: local payment is not pending.');
            }
            // Historical accepted memberships for this gym. An upgrade supersedes them.
            $active = DB::table('payments')->where('user_id', $payment->user_id)
                ->where('gym_id', $payment->gym_id)->where('type', 'membership')
                ->where('status','accepted')->whereDate('due_date','>=',today()->toDateString())
                ->lockForUpdate()->get();
            if ($active->contains(fn($p) => $p->due_date === null)) {
                throw new RuntimeException('An active membership has an unknown expiration date.');
            }
            foreach ($active as $old) {
                $oldTier = DB::table('membership_plans')->where('gym_id',$payment->gym_id)
                    ->where('name',$old->plan)->value('tier');
                if ($oldTier === null || (int)$session->plan_tier <= (int)$oldTier) {
                    throw new RuntimeException('Upgrade conflict: selected plan is not above the existing tier.');
                }
            }
            // Stop earlier plans from appearing active; no stacking of expiry dates.
            $activeIds = $active->pluck('id')->all();
            if ($activeIds) {
                DB::table('payments')->whereIn('id',$activeIds)
                    ->update(['status'=>'superseded','updated_at'=>now()]);
            }
            $date = Carbon::today();
            DB::table('payments')->where('id',$payment->id)->update([
                'status'=>'accepted', 'paid_at'=>$date->toDateString(),
                'due_date'=>$date->copy()->addDays((int)$payment->plan_days)->toDateString(),
                'updated_at'=>now(),
            ]);
            // Match the existing owner's member enrollment semantics.
            $member = DB::table('members')->where('user_id',$payment->user_id)->lockForUpdate()->first();
            if ($member) {
                DB::table('members')->where('id',$member->id)->update([
                    'gym_id'=>$payment->gym_id,'join_date'=>$member->join_date ?: today()->toDateString(),
                    'updated_at'=>now(),
                ]);
            } else {
                DB::table('members')->insert([
                    'user_id'=>$payment->user_id,'gym_id'=>$payment->gym_id,
                    'join_date'=>today()->toDateString(), 'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
            DB::table('xendit_membership_sessions')->where('id',$local->id)->update([
                'status'=>'COMPLETED','paid_at'=>now(),'updated_at'=>now(),
            ]);
            return ['verified'=>true,'payment_status'=>'COMPLETED','membership_status'=>'accepted','message'=>'Membership activated.'];
        }, 3);
    }
}
