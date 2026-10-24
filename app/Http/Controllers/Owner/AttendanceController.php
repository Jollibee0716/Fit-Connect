<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\{Attendance, GymClass, User};
use App\Support\{Audit, GymContext, Membership};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $r)
    {
        $gid = GymContext::require($r);
        $date = $r->query(
            'date',
            today()->toDateString()
        );

        $rows = Attendance::where('gym_id', $gid)
            ->where('session_date', $date)
            ->orderByDesc('checked_in_at')
            ->get();

        return [
            'attendance' => $rows->map(fn ($a) => [
                'id' => $a->id,
                'member_id' => $a->member_id,
                'member_name' => $a->member_name,
                'class_name' => $a->class_name,
                'membership_state' => $a->membership_state,
                'override' => (bool) $a->override,
                'checked_in_at' =>
                    $a->checked_in_at->format('Y-m-d H:i:s'),
            ])
        ];
    }

    public function store(Request $r)
    {
        $gid = (int) GymContext::require($r);
        $today = now()->toDateString();

        $d = $r->validate([
            'member_id' => 'required|integer',
            'class_id' => 'nullable|integer',
            'reason' => 'nullable|string|max:200',
        ]);

        $member = User::findOrFail($d['member_id']);

        abort_unless(
            (int) GymContext::id($member) === $gid,
            403,
            'This member is not in your gym.'
        );

        $class = null;

        if (!empty($d['class_id'])) {
            $class = GymClass::where('gym_id', $gid)
                ->findOrFail($d['class_id']);

            abort_unless(
                in_array(now()->dayOfWeekIso, $class->days),
                422,
                'This class does not run today.'
            );
        }

        $state = Membership::state($member);
        $override = $state !== 'active';

        abort_if(
            $override && blank($d['reason'] ?? null),
            422,
            'A reason is required to check in a member without an active membership.'
        );

        return DB::transaction(function () use (
            $gid,
            $today,
            $d,
            $member,
            $class,
            $state,
            $override
        ) {

            abort_if(
                Attendance::where('gym_id', $gid)
                    ->where('member_id', $member->id)
                    ->where('session_date', $today)
                    ->where('class_id', $class?->id)
                    ->exists(),
                422,
                $member->name . ' is already checked in.'
            );

            $a = Attendance::create([
                'gym_id' => $gid,
                'member_id' => $member->id,
                'member_name' => $member->name,
                'class_id' => $class?->id,
                'class_name' => $class?->name,
                'membership_state' => $state,
                'override' => $override,
                'override_reason' =>
                    $override ? $d['reason'] : null,
                'session_date' => $today,
                'checked_in_at' => now(),
                'method' => 'owner',
            ]);

            // Record every successful check-in.
            Audit::log(
                'attendance',
                $a->id,
                'created',
                $gid,
                "Gym owner checked in {$member->name}" .
                ($class ? " for {$class->name}" : ''),
                null,
                [
                    'member_id' => $member->id,
                    'class_id' => $class?->id,
                    'session_date' => $today,
                    'membership_state' => $state,
                    'override' => $override,
                    'checked_in_at' =>
                        $a->checked_in_at->toDateTimeString(),
                ]
            );

            // Add a separate audit event for an override.
            if ($override) {
                Audit::log(
                    'attendance',
                    $a->id,
                    'override',
                    $gid,
                    "Gym owner approved attendance override for {$member->name}",
                    null,
                    [
                        'member_id' => $member->id,
                        'class_id' => $class?->id,
                        'membership_state' => $state,
                        'override' => true,
                        'reason' => $d['reason'],
                    ]
                );
            }

            return [
                'ok' => true,
                'id' => $a->id,
            ];
        });
    }

    public function destroy(
        Request $r,
        Attendance $attendance
    ) {
        $gid = (int) GymContext::require($r);

        abort_unless(
            (int) $attendance->gym_id === $gid,
            403,
            'This attendance record belongs to another gym.'
        );

        return DB::transaction(function () use (
            $attendance,
            $gid
        ) {

            $attendance = Attendance::where('gym_id', $gid)
                ->lockForUpdate()
                ->findOrFail($attendance->id);

            $oldValues = [
                'member_id' => $attendance->member_id,
                'class_id' => $attendance->class_id,
                'session_date' => $attendance->session_date,
                'membership_state' =>
                    $attendance->membership_state,
                'override' => (bool) $attendance->override,
                'checked_in_at' =>
                    $attendance->checked_in_at?->toDateTimeString(),
            ];

            $attendanceId = $attendance->id;
            $memberName = $attendance->member_name;

            $attendance->delete();

            Audit::log(
                'attendance',
                $attendanceId,
                'deleted',
                $gid,
                "Gym owner removed attendance for {$memberName}",
                $oldValues,
                []
            );

            return ['ok' => true];
        });
    }
}
