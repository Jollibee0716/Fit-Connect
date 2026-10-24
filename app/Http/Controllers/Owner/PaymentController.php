<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private function gymIds($user)
    {
        $go = $user->gymOwner;

        return $go
            ? DB::table('gyms')
                ->where('gym_owner_id', $go->id)
                ->pluck('id')
            : collect();
    }

    public function index(Request $request)
    {
        $payments = DB::table('payments as p')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->whereIn('p.gym_id', $this->gymIds($request->user()))
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
                'p.paid_at',
                'p.due_date',
                'p.status'
            ]);

        return response()->json([
            'payments' => $payments
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,revoked'
        ]);

        $newStatus = $validated['status'];

        DB::transaction(function () use (
            $request,
            $id,
            $newStatus
        ) {
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

            $oldStatus = $payment->status;

            abort_if(
                $oldStatus === $newStatus,
                422,
                'Payment already has this status.'
            );

            // Revoked payments cannot be reaccepted.
            abort_if(
                $oldStatus === 'revoked' &&
                $newStatus === 'accepted',
                422,
                'Revoked payments cannot be accepted again.'
            );

            // Only pending payments may become accepted.
            if ($newStatus === 'accepted') {
                abort_unless(
                    $oldStatus === 'pending',
                    422,
                    'Only pending payments can be accepted.'
                );

                abort_unless(
                    (int) $payment->plan_days > 0,
                    422,
                    'Membership plan duration is invalid.'
                );
            }

            $updates = [
                'status' => $newStatus,
                'updated_at' => now(),
            ];

            // The membership starts on acceptance,
            // not when the payment request was submitted.
            if ($newStatus === 'accepted') {
                $updates['paid_at'] = today()->toDateString();
            }

            DB::table('payments')
                ->where('id', $payment->id)
                ->update($updates);

            if ($newStatus === 'accepted') {
                $this->enrollMember(
                    (int) $payment->user_id,
                    (int) $payment->gym_id
                );
            }

            // Recalculate the membership expiration.
            $this->recalc(
                $payment->user_id,
                $payment->gym_id
            );

            $name = DB::table('users')
                ->where('id', $payment->user_id)
                ->value('name') ?? 'Member';

            Audit::log(
                'payments',
                $payment->id,
                'updated',
                (int) $payment->gym_id,
                'Membership payment ' . $newStatus .
                ' for ' . $name,
                [
                    'status' => $oldStatus
                ],
                [
                    'status' => $newStatus,
                    'plan' => $payment->plan,
                    'amount' => $payment->amount
                ]
            );
        });

        return response()->json([
            'ok' => true,
            'message' => $newStatus === 'accepted'
                ? 'Payment accepted. Membership activated successfully.'
                : 'Membership payment revoked successfully.'
        ]);
    }

    /**
     * Automatically enroll the paying member
     * in the gym after payment acceptance.
     */
    private function enrollMember(int $userId, int $gymId): void
    {
        // Lock the user to serialize enrollment updates.
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
                $updates['join_date'] = today()->toDateString();
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
     * Calculate the expiration dates of accepted
     * membership payments, oldest first.
     */
    private function recalc($userId, $gymId): void
    {
        $due = null;

        $accepted = DB::table('payments')
            ->where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->where('type', 'membership')
            ->where('status', 'accepted')
            ->orderBy('paid_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($accepted as $row) {
            $paid = Carbon::parse($row->paid_at)
                ->startOfDay();

            $start = (
                $due !== null &&
                $due->greaterThan($paid)
            )
                ? $due->copy()
                : $paid->copy();

            $due = $start->addDays(
                (int) $row->plan_days
            );

            DB::table('payments')
                ->where('id', $row->id)
                ->update([
                    'due_date' => $due->toDateString(),
                    'updated_at' => now(),
                ]);
        }

        // Remove expiration dates from nonaccepted requests.
        DB::table('payments')
            ->where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->where('type', 'membership')
            ->where('status', '!=', 'accepted')
            ->update([
                'due_date' => null,
                'updated_at' => now(),
            ]);
    }
}
