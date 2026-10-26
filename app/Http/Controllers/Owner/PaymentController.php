<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Get the gyms belonging to the logged-in owner.
     */
    private function gymIds($user)
    {
        $go = $user->gymOwner;

        return $go
            ? DB::table('gyms')
                ->where('gym_owner_id', $go->id)
                ->pluck('id')
            : collect();
    }

    /**
     * Display membership payments for this gym owner.
     */
    public function index(Request $request)
    {
        $payments = DB::table('payments as p')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->whereIn(
                'p.gym_id',
                $this->gymIds($request->user())
            )
            ->where('p.type', 'membership')
            ->orderByDesc('p.id')
            ->get([
                'p.id',
                'p.user_id',
                'u.name',
                'p.gym_id',
                'p.plan',
                'p.plan_days',
                'p.amount',
                'p.method',
                'p.reference',
                'p.proof_path',
                'p.paid_at',
                'p.due_date',
                'p.status',
            ]);

        return response()->json([
            'payments' => $payments,
        ]);
    }

    /**
     * Accept or revoke a manual membership payment.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,revoked',
        ]);

        $newStatus = $validated['status'];

        DB::transaction(function () use (
            $request,
            $id,
            $newStatus
        ) {
            // Only the correct gym owner can update payment.
            $payment = DB::table('payments')
                ->where('id', $id)
                ->where('type', 'membership')
                ->whereIn(
                    'gym_id',
                    $this->gymIds($request->user())
                )
                ->lockForUpdate()
                ->first();

            abort_unless(
                $payment,
                404,
                'Membership payment not found.'
            );

            // Protect historical Xendit transactions.
            abort_if(
                strtolower(
                    trim((string) $payment->method)
                ) === 'xendit',
                422,
                'Historical Xendit payments cannot be manually changed.'
            );

            $oldStatus = $payment->status;

            abort_if(
                $oldStatus === $newStatus,
                422,
                'Payment already has this status.'
            );

            // A revoked payment cannot be accepted again.
            abort_if(
                $oldStatus === 'revoked' &&
                $newStatus === 'accepted',
                422,
                'Revoked payments cannot be accepted again.'
            );

            if ($newStatus === 'accepted') {

                // Only pending payments may be accepted.
                abort_unless(
                    $oldStatus === 'pending',
                    422,
                    'Only pending payments can be accepted.'
                );

                // Check membership duration.
                abort_unless(
                    (int) $payment->plan_days > 0,
                    422,
                    'Membership plan duration is invalid.'
                );

                // Check reference number.
                abort_if(
                    !trim((string) $payment->reference),
                    422,
                    'A transaction reference number is required.'
                );

                // Check the uploaded screenshot exists.
                abort_if(
                    !$payment->proof_path ||
                    !Storage::disk('local')
                        ->exists($payment->proof_path),
                    422,
                    'A valid uploaded payment screenshot is required before approval.'
                );
            }

            $updates = [
                'status' => $newStatus,
                'updated_at' => now(),
            ];

            // Membership starts on owner acceptance.
            if ($newStatus === 'accepted') {
                $updates['paid_at'] = today()->toDateString();
            }

            DB::table('payments')
                ->where('id', $payment->id)
                ->update($updates);

            // Automatically enroll the member after approval.
            if ($newStatus === 'accepted') {
                $this->enrollMember(
                    (int) $payment->user_id,
                    (int) $payment->gym_id
                );
            }

            // Recalculate expiration without stacking days.
            $this->recalc(
                $payment->user_id,
                $payment->gym_id
            );

            $name = DB::table('users')
                ->where('id', $payment->user_id)
                ->value('name') ?? 'Member';

            // Record the action in audit logs.
            Audit::log(
                'payments',
                $payment->id,
                'updated',
                (int) $payment->gym_id,
                'Membership payment ' .
                    $newStatus . ' for ' . $name,
                [
                    'status' => $oldStatus,
                ],
                [
                    'status' => $newStatus,
                    'plan' => $payment->plan,
                    'amount' => $payment->amount,
                ]
            );
        }, 3);

        return response()->json([
            'ok' => true,
            'message' => $newStatus === 'accepted'
                ? 'Payment accepted. Membership activated successfully.'
                : 'Membership payment revoked successfully.',
        ]);
    }

    /**
     * Enroll the member in the gym after payment approval.
     */
    private function enrollMember(
        int $userId,
        int $gymId
    ): void {
        // Lock member user before enrollment changes.
        DB::table('users')
            ->where('id', $userId)
            ->lockForUpdate()
            ->firstOrFail();

        $member = DB::table('members')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->lockForUpdate()
            ->first();

        if ($member) {

            $updates = [
                'gym_id' => $gymId,
                'updated_at' => now(),
            ];

            if (
                (int) $member->gym_id !== $gymId ||
                !$member->join_date
            ) {
                $updates['join_date'] =
                    today()->toDateString();
            }

            DB::table('members')
                ->where('id', $member->id)
                ->update($updates);

            return;
        }

        DB::table('members')->insert([
            'user_id' => $userId,
            'gym_id' => $gymId,
            'join_date' => today()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Permanent membership expiration rule.
     *
     * Each accepted manual payment expires exactly
     * plan_days after its own acceptance date.
     *
     * Example:
     * Accepted: 2026-10-26
     * Duration: 365 days
     * Expires: 2027-10-26
     *
     * No stacking of previous membership periods.
     * Historical Xendit payments remain unchanged.
     */
    private function recalc(
        $userId,
        $gymId
    ): void {
        // Exclude historical Xendit payment records.
        $manualPayments = function ($query) {
            $query->whereNull('method')
                ->orWhereRaw(
                    'LOWER(method) <> ?',
                    ['xendit']
                );
        };

        // Get accepted manual payments only.
        $accepted = DB::table('payments')
            ->where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->where('type', 'membership')
            ->where('status', 'accepted')
            ->where($manualPayments)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($accepted as $payment) {

            if (
                !$payment->paid_at ||
                (int) $payment->plan_days <= 0
            ) {
                continue;
            }

            // Start from the payment acceptance date.
            $start = Carbon::parse(
                $payment->paid_at
            )->startOfDay();

            // Add only this payment's plan duration.
            $expiration = $start->copy()
                ->addDays(
                    (int) $payment->plan_days
                )
                ->toDateString();

            DB::table('payments')
                ->where('id', $payment->id)
                ->update([
                    'due_date' => $expiration,
                    'updated_at' => now(),
                ]);
        }

        // Pending and revoked payments cannot have due dates.
        DB::table('payments')
            ->where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->where('type', 'membership')
            ->where('status', '!=', 'accepted')
            ->where($manualPayments)
            ->update([
                'due_date' => null,
                'updated_at' => now(),
            ]);
    }
}
