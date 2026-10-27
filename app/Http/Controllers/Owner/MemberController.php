<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * Get the gym belonging to the logged-in owner.
     */
    private function gymId(): ?int
    {
        $ownerId = DB::table('gym_owners')
            ->where('user_id', auth()->id())
            ->value('id');

        if (!$ownerId) {
            return null;
        }

        $gymId = DB::table('gyms')
            ->where('gym_owner_id', $ownerId)
            ->value('id');

        return $gymId ? (int) $gymId : null;
    }

    /**
     * Display currently enrolled members
     * with their actual membership status.
     */
    public function index()
    {
        $gymId = $this->gymId();

        if (!$gymId) {
            return response()->json([
                'members' => []
            ]);
        }

        $members = DB::table('members')
            ->join(
                'users',
                'users.id',
                '=',
                'members.user_id'
            )
            ->where('members.gym_id', $gymId)
            ->orderBy('users.name')
            ->get([
                'members.id',
                'members.user_id',
                'members.join_date',
                'users.name',
                'users.email'
            ]);

        $userIds = $members->pluck('user_id')->all();

        // Retrieve membership payment information.
        $payments = empty($userIds)
            ? collect()
            : DB::table('payments')
                ->where('gym_id', $gymId)
                ->whereIn('user_id', $userIds)
                ->where('type', 'membership')
                ->whereIn('status', ['accepted', 'pending'])
                ->orderByDesc('id')
                ->get();

        $today = today()->toDateString();

        $results = $members->map(function ($member) use (
            $payments,
            $today
        ) {
            $memberPayments = $payments->where(
                'user_id',
                $member->user_id
            );

            // Latest accepted membership expiration.
            $accepted = $memberPayments
                ->where('status', 'accepted')
                ->sortByDesc('due_date')
                ->first();

            // Latest pending membership request.
            $pending = $memberPayments
                ->where('status', 'pending')
                ->sortByDesc('id')
                ->first();

            $state = 'none';
            $plan = '-';
            $dueDate = null;

            if ($accepted) {
                $plan = $accepted->plan;
                $dueDate = $accepted->due_date;

                $state = ($dueDate && $dueDate > $today)
                    ? 'active'
                    : 'overdue';

            } elseif ($pending) {
                $state = 'pending';
                $plan = $pending->plan;
            }

            return [
                // Enrollment ID used for unenrollment.
                'id' => $member->id,

                // User ID used in payment records.
                'user_id' => $member->user_id,

                'name' => $member->name,
                'email' => $member->email,
                'join_date' => $member->join_date,

                'membership_state' => $state,
                'plan' => $plan,
                'due_date' => $dueDate,
            ];
        });

        return response()->json([
            'members' => $results
        ]);
    }

    /**
     * Unenroll member AND cancel their membership.
     *
     * The account and payment records remain
     * in the database for historical purposes.
     */
    public function destroy($id)
    {
        $gymId = $this->gymId();

        abort_unless(
            $gymId,
            404,
            'Gym not found.'
        );

        DB::transaction(function () use ($id, $gymId) {

            // Lock current enrollment.
            $member = DB::table('members')
                ->where('id', $id)
                ->where('gym_id', $gymId)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $member,
                404,
                'Member not found in your gym.'
            );

            $user = DB::table('users')
                ->where('id', $member->user_id)
                ->first();

            $memberName = $user->name ?? 'Unknown Member';

            // Save previous enrollment data.
            $oldValues = [
                'user_id' => $member->user_id,
                'gym_id' => $member->gym_id,
                'join_date' => $member->join_date,
            ];

            // Lock membership payment records.
            $payments = DB::table('payments')
                ->where('gym_id', $gymId)
                ->where('user_id', $member->user_id)
                ->where('type', 'membership')
                ->whereIn('status', ['accepted', 'pending'])
                ->lockForUpdate()
                ->get(['id', 'status']);

            // Cancel all existing membership approvals
            // and pending requests for THIS gym only.
            $paymentIds = $payments->pluck('id')->all();

            if (!empty($paymentIds)) {
                DB::table('payments')
                    ->whereIn('id', $paymentIds)
                    ->update([
                        'status' => 'revoked',
                        'updated_at' => now(),
                    ]);
            }

            // Disconnect member from the gym.
            DB::table('members')
                ->where('id', $member->id)
                ->where('gym_id', $gymId)
                ->update([
                    'gym_id' => null,
                    'updated_at' => now(),
                ]);

            // Write audit history.
            Audit::log(
                'members',
                $member->id,
                'unenrolled',
                $gymId,
                "Gym owner unenrolled {$memberName} and cancelled membership",
                array_merge($oldValues, [
                    'payment_statuses' => $payments
                        ->pluck('status', 'id')
                        ->all(),
                ]),
                [
                    'user_id' => $member->user_id,
                    'gym_id' => null,
                    'membership_status' => 'cancelled',
                    'cancelled_payment_ids' => $paymentIds,
                ]
            );
        });

        return response()->json([
            'ok' => true,
            'message' =>
                'Member unenrolled and membership cancelled successfully.',
        ]);
    }
}
