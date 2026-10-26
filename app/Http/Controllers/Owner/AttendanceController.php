<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GymClass;
use App\Models\User;
use App\Support\Audit;
use App\Support\GymContext;
use App\Support\Membership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /**
     * Display attendance records for a date.
     */
    public function index(Request $r)
    {
        $gid = (int) GymContext::require($r);

        $date = $r->query(
            'date',
            today()->toDateString()
        );

        $rows = Attendance::where('gym_id', $gid)
            ->where('session_date', $date)
            ->orderByDesc('checked_in_at')
            ->get();

        return response()->json([
            'attendance' => $rows->map(fn ($a) => [
                'id' => $a->id,
                'member_id' => $a->member_id,
                'member_name' => $a->member_name,
                'class_name' => $a->class_name,
                'membership_state' => $a->membership_state,
                'override' => (bool) $a->override,
                'checked_in_at' => $a->checked_in_at
                    ->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    /**
     * Check in a member.
     *
     * member_id = members.id
     * user_id   = users.id
     *
     * Accept exactly one identifier.
     */
    public function store(Request $r)
    {
        $gid = (int) GymContext::require($r);

        $timezone = config('app.timezone', 'Asia/Manila');
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        $d = $r->validate([
            'member_id' => 'nullable|required_without:user_id|integer',
            'user_id' => 'nullable|required_without:member_id|integer',
            'class_id' => 'nullable|integer',
            'reason' => 'nullable|string|max:200',
        ]);

        // Require exactly one identifier.
        if (
            isset($d['member_id']) &&
            isset($d['user_id'])
        ) {
            throw ValidationException::withMessages([
                'member_id' =>
                    'Provide either member_id or user_id, not both.',
            ]);
        }

        /*
         * Resolve member ID to the correct user.
         */
        if (isset($d['member_id'])) {
            $membershipRecord = DB::table('members')
                ->where('id', $d['member_id'])
                ->where('gym_id', $gid)
                ->first();

            abort_unless(
                $membershipRecord,
                404,
                'Member record not found in your gym.'
            );

            $userId = (int) $membershipRecord->user_id;
        } else {
            $userId = (int) $d['user_id'];

            $membershipRecord = DB::table('members')
                ->where('user_id', $userId)
                ->where('gym_id', $gid)
                ->first();

            abort_unless(
                $membershipRecord,
                404,
                'This user is not enrolled in your gym.'
            );
        }

        $member = User::findOrFail($userId);

        /*
         * Validate the scheduled class.
         */
        $class = null;

        if (!empty($d['class_id'])) {
            $class = GymClass::where('gym_id', $gid)
                ->findOrFail($d['class_id']);

            $days = array_map(
                'intval',
                (array) ($class->days ?? [])
            );

            // Class must be scheduled today.
            abort_unless(
                in_array(
                    $now->dayOfWeekIso,
                    $days,
                    true
                ),
                422,
                'This class does not run today.'
            );

            // Class schedule must be valid.
            abort_unless(
                filled($class->start_time) &&
                (int) $class->duration_min > 0,
                422,
                'This class has an invalid schedule.'
            );

            $classStart = Carbon::parse(
                $today . ' ' . $class->start_time,
                $timezone
            );

            $classEnd = $classStart->copy()
                ->addMinutes((int) $class->duration_min);

            // Do not allow check-in after the class ends.
            abort_if(
                $now->greaterThanOrEqualTo($classEnd),
                422,
                'Check-in is closed. This class has already ended.'
            );

            // Optional: prevent checking in before the class starts.
            // To enable, uncomment the following:
            //
            // abort_if(
            //     $now->lessThan($classStart),
            //     422,
            //     'Check-in is not yet open for this class.'
            // );
        }

        /*
         * Validate membership state.
         */
        $state = Membership::state($member);
        $override = $state !== 'active';

        abort_if(
            $override && blank($d['reason'] ?? null),
            422,
            'A reason is required to check in a member without an active membership.'
        );

        /*
         * Create attendance record.
         */
        return DB::transaction(function () use (
            $gid,
            $today,
            $d,
            $member,
            $class,
            $state,
            $override
        ) {
            $alreadyCheckedIn = Attendance::where(
                    'gym_id',
                    $gid
                )
                ->where('member_id', $member->id)
                ->where('session_date', $today)
                ->where('class_id', $class?->id)
                ->exists();

            abort_if(
                $alreadyCheckedIn,
                422,
                $member->name . ' is already checked in.'
            );

            $attendance = Attendance::create([
                'gym_id' => $gid,
                'member_id' => $member->id,
                'member_name' => $member->name,
                'class_id' => $class?->id,
                'class_name' => $class?->name,
                'membership_state' => $state,
                'override' => $override,
                'override_reason' =>
                    $override
                        ? $d['reason']
                        : null,
                'session_date' => $today,
                'checked_in_at' => now(),
                'method' => 'owner',
            ]);

            /*
             * Audit successful check-in.
             */
            Audit::log(
                'attendance',
                $attendance->id,
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
                        $attendance->checked_in_at
                            ->toDateTimeString(),
                ]
            );

            /*
             * Audit membership override.
             */
            if ($override) {
                Audit::log(
                    'attendance',
                    $attendance->id,
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

            return response()->json([
                'ok' => true,
                'id' => $attendance->id,
                'message' => 'Member checked in successfully.',
            ]);
        });
    }

    /**
     * Delete an attendance record.
     */
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
                    $attendance->checked_in_at
                        ?->toDateTimeString(),
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

            return response()->json([
                'ok' => true,
                'message' => 'Attendance record removed successfully.',
            ]);
        });
    }
}
