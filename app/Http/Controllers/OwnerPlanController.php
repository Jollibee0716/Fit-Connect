<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class OwnerPlanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get the authenticated Gym Owner's gym
    |--------------------------------------------------------------------------
    */

    private function gymId(): int
    {
        $gym = DB::table('gyms')
            ->join(
                'gym_owners',
                'gym_owners.id',
                '=',
                'gyms.gym_owner_id'
            )
            ->where('gym_owners.user_id', auth()->id())
            ->select('gyms.id')
            ->first();

        abort_unless(
            $gym,
            403,
            'No gym found for this account.'
        );

        return (int) $gym->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate membership plan
    |--------------------------------------------------------------------------
    */

    private function rules(
        int $gymId,
        ?int $ignoreId = null
    ): array {
        return [
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('membership_plans', 'name')
                    ->where('gym_id', $gymId)
                    ->ignore($ignoreId),
            ],

            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:99999999.99',
            ],

            'days' => [
                'required',
                'integer',
                'min:1',
                'max:3650',
            ],

            // Optional for older dashboard compatibility.
            'tier' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100000',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Find a plan belonging to this gym
    |--------------------------------------------------------------------------
    */

    private function findOwn(int $id, int $gymId)
    {
        $plan = DB::table('membership_plans')
            ->where('id', $id)
            ->where('gym_id', $gymId)
            ->first();

        abort_unless(
            $plan,
            404,
            'Membership plan not found.'
        );

        return $plan;
    }

    /*
    |--------------------------------------------------------------------------
    | LIST - Unlimited membership plans
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $gymId = $this->gymId();

        $plans = DB::table('membership_plans')
            ->where('gym_id', $gymId)
            ->orderBy('tier')
            ->orderBy('price')
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'price',
                'days',
                'tier',
                'is_active',
            ]);

        return response()->json([
            'plans' => $plans,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE - Add a new membership plan
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $gymId = $this->gymId();

        $data = $request->validate(
            $this->rules($gymId)
        );

        $data['name'] = trim($data['name']);

        if ($data['name'] === '') {
            return response()->json([
                'message' => 'Plan name cannot be empty.',
            ], 422);
        }

        $data['tier'] = (int) ($data['tier'] ?? 1);
        $data['is_active'] = (bool) (
            $data['is_active'] ?? true
        );

        $id = DB::table('membership_plans')
            ->insertGetId([
                'gym_id' => $gymId,
                'name' => $data['name'],
                'price' => $data['price'],
                'days' => $data['days'],
                'tier' => $data['tier'],
                'is_active' => $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        Audit::log(
            'membership_plans',
            $id,
            'created',
            $gymId,
            "Added plan {$data['name']} " .
            "(₱{$data['price']}, " .
            "{$data['days']} days, " .
            "Tier {$data['tier']})"
        );

        return response()->json([
            'message' => 'Membership plan created.',
            'plan' => DB::table('membership_plans')
                ->find($id),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE - Edit existing membership plan
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $gymId = $this->gymId();

        $old = $this->findOwn(
            (int) $id,
            $gymId
        );

        $data = $request->validate(
            $this->rules($gymId, (int) $old->id)
        );

        $data['name'] = trim($data['name']);

        if ($data['name'] === '') {
            return response()->json([
                'message' => 'Plan name cannot be empty.',
            ], 422);
        }

        // Preserve existing tier if the old UI
        // does not submit a tier value.
        $data['tier'] = (int) (
            $data['tier'] ?? $old->tier
        );

        // Preserve current active state if omitted.
        $data['is_active'] = (bool) (
            $data['is_active'] ?? $old->is_active
        );

        $oldValues = [
            'name' => $old->name,
            'price' => $old->price,
            'days' => $old->days,
            'tier' => $old->tier,
            'is_active' => $old->is_active,
        ];

        DB::table('membership_plans')
            ->where('id', $old->id)
            ->where('gym_id', $gymId)
            ->update([
                'name' => $data['name'],
                'price' => $data['price'],
                'days' => $data['days'],
                'tier' => $data['tier'],
                'is_active' => $data['is_active'],
                'updated_at' => now(),
            ]);

        Audit::log(
            'membership_plans',
            $old->id,
            'updated',
            $gymId,
            "Edited plan {$data['name']}",
            $oldValues,
            $data
        );

        return response()->json([
            'message' => 'Membership plan updated.',
            'plan' => DB::table('membership_plans')
                ->find($old->id),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE - Remove or archive a membership plan
    |--------------------------------------------------------------------------
    |
    | If historical membership payments refer to
    | the plan name, archive it instead of deleting.
    |
    | This preserves older payment records.
    */

    public function destroy($id)
    {
        $gymId = $this->gymId();

        $plan = $this->findOwn(
            (int) $id,
            $gymId
        );

        $oldValues = [
            'name' => $plan->name,
            'price' => $plan->price,
            'days' => $plan->days,
            'tier' => $plan->tier,
            'is_active' => $plan->is_active,
        ];

        // Avoid removing plans when we cannot safely
        // determine whether historical payments exist.
        $canCheckHistory =
            Schema::hasTable('payments') &&
            Schema::hasColumn('payments', 'gym_id') &&
            Schema::hasColumn('payments', 'plan');

        $hasHistory = true;

        if ($canCheckHistory) {
            $hasHistory = DB::table('payments')
                ->where('gym_id', $gymId)
                ->where('plan', $plan->name)
                ->exists();
        }

        if ($hasHistory) {
            DB::table('membership_plans')
                ->where('id', $plan->id)
                ->where('gym_id', $gymId)
                ->update([
                    'is_active' => 0,
                    'updated_at' => now(),
                ]);

            Audit::log(
                'membership_plans',
                $plan->id,
                'updated',
                $gymId,
                "Archived plan {$plan->name}",
                $oldValues,
                ['is_active' => 0]
            );

            return response()->json([
                'ok' => true,
                'archived' => true,
                'message' =>
                    'Plan archived to preserve payment history.',
            ]);
        }

        DB::table('membership_plans')
            ->where('id', $plan->id)
            ->where('gym_id', $gymId)
            ->delete();

        Audit::log(
            'membership_plans',
            $plan->id,
            'deleted',
            $gymId,
            "Deleted plan {$plan->name}",
            $oldValues
        );

        return response()->json([
            'ok' => true,
            'archived' => false,
            'message' => 'Membership plan deleted.',
        ]);
    }
}
