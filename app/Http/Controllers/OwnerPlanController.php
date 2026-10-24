<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OwnerPlanController extends Controller
{
    private function gymId()
    {
        $gym = DB::table('gyms')
            ->join('gym_owners', 'gym_owners.id', '=', 'gyms.gym_owner_id')
            ->where('gym_owners.user_id', auth()->id())
            ->select('gyms.id')
            ->first();

        abort_unless($gym, 403, 'No gym found for this account.');
        return (int) $gym->id;
    }

    private function rules($gymId, $ignoreId = null)
    {
        return [
            'name'  => ['required', 'string', 'max:80',
                Rule::unique('membership_plans', 'name')
                    ->where('gym_id', $gymId)
                    ->ignore($ignoreId)],
            'price' => 'required|numeric|min:0',
            'days'  => 'required|integer|min:1|max:3650',
        ];
    }

    private function findOwn($id, $gymId)
    {
        $plan = DB::table('membership_plans')
            ->where('id', $id)->where('gym_id', $gymId)->first();
        abort_unless($plan, 404, 'Plan not found.');
        return $plan;
    }

    public function index()
    {
        $plans = DB::table('membership_plans')
            ->where('gym_id', $this->gymId())
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'days', 'is_active']);

        return ['plans' => $plans];
    }

    public function store(Request $r)
    {
        $gymId = $this->gymId();
        $data  = $r->validate($this->rules($gymId));

        $id = DB::table('membership_plans')->insertGetId($data + [
            'gym_id'     => $gymId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Audit::log('membership_plans', $id, 'created', $gymId,
            "Added plan {$data['name']} (₱{$data['price']}, {$data['days']} days)");

        return ['plan' => DB::table('membership_plans')->find($id)];
    }

    public function update(Request $r, $id)
    {
        $gymId = $this->gymId();
        $old   = $this->findOwn($id, $gymId);
        $data  = $r->validate($this->rules($gymId, $old->id));

        DB::table('membership_plans')->where('id', $old->id)
            ->update($data + ['updated_at' => now()]);

        Audit::log('membership_plans', $old->id, 'updated', $gymId,
            "Edited plan {$data['name']}",
            ['name' => $old->name, 'price' => $old->price, 'days' => $old->days],
            $data);

        return ['plan' => DB::table('membership_plans')->find($old->id)];
    }

    public function destroy($id)
    {
        $gymId = $this->gymId();
        $plan  = $this->findOwn($id, $gymId);

        DB::table('membership_plans')->where('id', $plan->id)->delete();

        Audit::log('membership_plans', $plan->id, 'deleted', $gymId,
            "Deleted plan {$plan->name}",
            ['name' => $plan->name, 'price' => $plan->price, 'days' => $plan->days]);

        return ['ok' => true];
    }
}