<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Support\GymContext;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    private function rules(int $gid): array
    {
        return [
            'name' => 'required|string|max:100',
            'trainer_id' => [
                'required',
                Rule::exists('trainers', 'id')
                    ->where('gym_id', $gid),
            ],
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'duration_min' => 'required|integer|between:15,240',
            'capacity' => 'required|integer|min:1|max:500',
        ];
    }

    private function cleanDays(array $d): array
    {
        $d['days'] = array_values(
            array_unique(
                array_map('intval', $d['days'])
            )
        );

        sort($d['days']);

        return $d;
    }

    private function auditValues(GymClass $class): array
    {
        return [
            'name' => $class->name,
            'trainer_id' => $class->trainer_id,
            'days' => $class->days,
            'start_time' => $class->start_time,
            'duration_min' => $class->duration_min,
            'capacity' => $class->capacity,
        ];
    }

    public function index(Request $r)
    {
        $gid = GymContext::require($r);

        return [
            'classes' => GymClass::with('trainer')
                ->where('gym_id', $gid)
                ->orderBy('start_time')
                ->get()
                ->map->toApi()
        ];
    }

    public function store(Request $r)
    {
        $gid = GymContext::require($r);

        $d = $this->cleanDays(
            $r->validate($this->rules($gid))
        );

        return DB::transaction(function () use ($gid, $d) {

            $class = GymClass::create(
                $d + ['gym_id' => $gid]
            );

            Audit::log(
                'classes',
                $class->id,
                'created',
                (int) $gid,
                "Gym owner created class {$class->name}",
                null,
                $this->auditValues($class)
            );

            return $class->load('trainer')->toApi();
        });
    }

    public function update(Request $r, GymClass $class)
    {
        $gid = GymContext::require($r);

        abort_unless(
            (int) $class->gym_id === (int) $gid,
            403,
            'This class does not belong to your gym.'
        );

        $d = $this->cleanDays(
            $r->validate($this->rules($gid))
        );

        return DB::transaction(function () use (
            $class,
            $gid,
            $d
        ) {
            $class = GymClass::where('gym_id', $gid)
                ->lockForUpdate()
                ->findOrFail($class->id);

            $oldValues = $this->auditValues($class);

            $class->update($d);

            $newValues = $this->auditValues($class);

            if ($oldValues != $newValues) {
                Audit::log(
                    'classes',
                    $class->id,
                    'updated',
                    (int) $gid,
                    "Gym owner updated class {$class->name}",
                    $oldValues,
                    $newValues
                );
            }

            return $class->load('trainer')->toApi();
        });
    }

    public function destroy(Request $r, GymClass $class)
    {
        $gid = GymContext::require($r);

        abort_unless(
            (int) $class->gym_id === (int) $gid,
            403,
            'This class does not belong to your gym.'
        );

        return DB::transaction(function () use ($class, $gid) {

            $class = GymClass::where('gym_id', $gid)
                ->lockForUpdate()
                ->findOrFail($class->id);

            $oldValues = $this->auditValues($class);

            $classId = $class->id;
            $className = $class->name;

            $class->delete();

            Audit::log(
                'classes',
                $classId,
                'deleted',
                (int) $gid,
                "Gym owner deleted class {$className}",
                $oldValues,
                []
            );

            return ['ok' => true];
        });
    }
}
