<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\{GymClass, Trainer};
use App\Support\GymContext;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerController extends Controller
{
    private function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'specialty' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
        ];
    }

    public function index(Request $r)
    {
        $gid = GymContext::require($r);

        return [
            'trainers' => Trainer::where('gym_id', $gid)
                ->orderBy('name')
                ->get()
        ];
    }

    public function store(Request $r)
    {
        $gid = GymContext::require($r);
        $data = $r->validate($this->rules());

        return DB::transaction(function () use ($gid, $data) {

            $trainer = Trainer::create(
                $data + ['gym_id' => $gid]
            );

            Audit::log(
                'trainers',
                $trainer->id,
                'created',
                (int) $gid,
                "Gym owner added trainer {$trainer->name}",
                null,
                [
                    'name' => $trainer->name,
                    'specialty' => $trainer->specialty,
                    'phone' => $trainer->phone,
                ]
            );

            return $trainer;
        });
    }

    public function update(Request $r, Trainer $trainer)
    {
        $gid = GymContext::require($r);

        abort_unless(
            (int) $trainer->gym_id === (int) $gid,
            403,
            'This trainer does not belong to your gym.'
        );

        $data = $r->validate($this->rules());

        return DB::transaction(function () use (
            $trainer,
            $gid,
            $data
        ) {
            $trainer = Trainer::where('gym_id', $gid)
                ->lockForUpdate()
                ->findOrFail($trainer->id);

            $oldValues = $trainer->only([
                'name',
                'specialty',
                'phone',
            ]);

            $trainer->update($data);

            $newValues = $trainer->only([
                'name',
                'specialty',
                'phone',
            ]);

            if ($oldValues !== $newValues) {
                Audit::log(
                    'trainers',
                    $trainer->id,
                    'updated',
                    (int) $gid,
                    "Gym owner updated trainer {$trainer->name}",
                    $oldValues,
                    $newValues
                );
            }

            return $trainer;
        });
    }

    public function destroy(Request $r, Trainer $trainer)
    {
        $gid = GymContext::require($r);

        abort_unless(
            (int) $trainer->gym_id === (int) $gid,
            403,
            'This trainer does not belong to your gym.'
        );

        return DB::transaction(function () use ($trainer, $gid) {

            $trainer = Trainer::where('gym_id', $gid)
                ->lockForUpdate()
                ->findOrFail($trainer->id);

            abort_if(
                GymClass::where('trainer_id', $trainer->id)
                    ->exists(),
                422,
                'This coach still has classes. Reassign or delete them first.'
            );

            $oldValues = $trainer->only([
                'name',
                'specialty',
                'phone',
            ]);

            $trainerId = $trainer->id;
            $trainerName = $trainer->name;

            $trainer->delete();

            Audit::log(
                'trainers',
                $trainerId,
                'deleted',
                (int) $gid,
                "Gym owner deleted trainer {$trainerName}",
                $oldValues,
                []
            );

            return ['ok' => true];
        });
    }
}
