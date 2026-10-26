<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MembershipPurchaseGuard
{
    /**
     * Must be called inside a database transaction
     * after locking the purchasing member's user row.
     *
     * Assumes membership payments contain:
     * user_id, gym_id, type, plan, status, due_date.
     *
     * $planTiers must come from a trusted server-side
     * configuration, not from the browser.
     */
    public function assertCanPurchase(
        int $userId,
        int $gymId,
        string $requestedPlan,
        array $planTiers
    ): void {
        $normalize = static fn (string $value): string =>
            mb_strtolower(trim($value));

        $tiers = [];

        foreach ($planTiers as $name => $tier) {
            $tiers[$normalize((string) $name)] = (int) $tier;
        }

        $requested = $normalize($requestedPlan);

        if (!array_key_exists($requested, $tiers)) {
            throw ValidationException::withMessages([
                'plan' => 'This membership plan is unavailable.',
            ]);
        }

        $payments = DB::table('payments')
            ->where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->where('type', 'membership')
            ->lockForUpdate()
            ->get();

        $requestedTier = $tiers[$requested];

        foreach ($payments as $payment) {
            $status = $normalize((string) $payment->status);
            $currentPlan = $normalize((string) $payment->plan);

            /*
             * Prevent more than one unresolved membership
             * checkout for the same gym.
             *
             * A retry of an existing checkout should reuse
             * its original payment record.
             */
            if (in_array($status, [
                'pending',
                'processing',
            ], true)) {
                throw ValidationException::withMessages([
                    'plan' =>
                        'You already have a pending membership payment. ' .
                        'Complete or resolve it before starting another.',
                ]);
            }

            /*
             * Only a confirmed, unexpired membership
             * can prevent renewal or determine upgrades.
             */
            if (!in_array($status, [
                'accepted',
                'paid',
            ], true)) {
                continue;
            }

            if (empty($payment->due_date)) {
                // Fail closed: an accepted membership with
                // no expiry must be reviewed, not bypassed.
                throw ValidationException::withMessages([
                    'plan' =>
                        'Your current membership has no expiry date. ' .
                        'Please contact the gym administrator.',
                ]);
            }

            $expiry = Carbon::parse($payment->due_date);

            // The payments table is assumed to store
            // a date-only membership expiry.
            if ($expiry->endOfDay()->isPast()) {
                continue;
            }

            if ($currentPlan === $requested) {
                throw ValidationException::withMessages([
                    'plan' =>
                        'You already have this active membership plan. ' .
                        'You can renew it after it expires.',
                ]);
            }

            if (!array_key_exists($currentPlan, $tiers)) {
                throw ValidationException::withMessages([
                    'plan' =>
                        'Your current membership plan needs review ' .
                        'before another plan can be purchased.',
                ]);
            }

            $currentTier = $tiers[$currentPlan];

            if ($requestedTier <= $currentTier) {
                throw ValidationException::withMessages([
                    'plan' =>
                        'You already have an equal or higher plan. ' .
                        'Only upgrades are allowed before expiry.',
                ]);
            }
        }
    }
}
