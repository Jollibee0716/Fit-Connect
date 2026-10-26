<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\GymClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SELECTED GYM
    |--------------------------------------------------------------------------
    */
    private function selectedGym(Request $request): int
    {
        $id = (int) $request->session()
            ->get('fitconnect_selected_gym_id', 0);

        abort_unless(
            $id > 0 &&
            DB::table('gyms')->where('id', $id)->exists(),
            422,
            'Select a gym in Explore Gyms first.'
        );

        return $id;
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVE MEMBERSHIP VALIDATION
    |--------------------------------------------------------------------------
    */
    private function activeMembership(
        int $userId,
        int $gymId
    ): bool {
        return DB::table('payments')
            ->where('user_id', $userId)
            ->where('gym_id', $gymId)
            ->where('type', 'membership')
            ->where('status', 'accepted')
            ->whereNotNull('due_date')
            ->whereDate(
                'due_date',
                '>=',
                today()->toDateString()
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW AVAILABLE CLASSES
    |--------------------------------------------------------------------------
    */
    public function classes(Request $request)
    {
        $gymId = $this->selectedGym($request);

        $classes = GymClass::with('trainer')
            ->where('gym_id', $gymId)
            ->orderBy('start_time')
            ->get()
            ->map(fn ($class) => $class->toApi());

        return response()->json([
            'classes' => $classes,
            'membership_active' => $this->activeMembership(
                $request->user()->id,
                $gymId
            ),
            'gym_id' => $gymId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW MY CLASS BOOKINGS
    |--------------------------------------------------------------------------
    */
    public function bookings(Request $request)
    {
        $gymId = $this->selectedGym($request);

        $rows = DB::table('class_bookings as b')
            ->join(
                'gym_classes as c',
                'c.id',
                '=',
                'b.class_id'
            )
            ->where('b.gym_id', $gymId)
            ->where('b.member_id', $request->user()->id)
            ->select(
                'b.id',
                'b.class_id',
                'c.name as class_name',
                'c.start_time',
                'b.session_date',
                'b.status'
            )
            ->orderByDesc('b.session_date')
            ->limit(100)
            ->get();

        return response()->json([
            'bookings' => $rows
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | JOIN / BOOK A CLASS
    |--------------------------------------------------------------------------
    */
    public function book(Request $request)
    {
        $data = $request->validate([
            'class_id' => [
                'required',
                'integer'
            ],
            'session_date' => [
                'required',
                'date_format:Y-m-d'
            ],
        ]);

        $gymId = $this->selectedGym($request);
        $userId = (int) $request->user()->id;

        abort_unless(
            $this->activeMembership($userId, $gymId),
            422,
            'An active membership is required before joining a class.'
        );

        $date = Carbon::createFromFormat(
            '!Y-m-d',
            $data['session_date']
        );

        abort_unless(
            $date->betweenIncluded(
                today(),
                today()->addDays(60)
            ),
            422,
            'Choose a session within the next 60 days.'
        );

        return DB::transaction(function () use (
            $data,
            $gymId,
            $userId,
            $date
        ) {

            // Lock the class to prevent overbooking
            $class = GymClass::where('gym_id', $gymId)
                ->whereKey($data['class_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $days = array_map(
                'intval',
                $class->days ?? []
            );

            abort_unless(
                in_array(
                    $date->isoWeekday(),
                    $days,
                    true
                ),
                422,
                'The selected class does not meet on this date.'
            );

            $starts = $date->copy()
                ->setTimeFromTimeString(
                    $class->start_time
                );

            abort_unless(
                $starts->isFuture(),
                422,
                'This class session has already started.'
            );

            // Check existing booking
            $existing = DB::table('class_bookings')
                ->where('class_id', $class->id)
                ->where('member_id', $userId)
                ->whereDate(
                    'session_date',
                    $date->toDateString()
                )
                ->lockForUpdate()
                ->first();

            abort_if(
                $existing &&
                $existing->status !== 'cancelled',
                422,
                'You have already booked this class session.'
            );

            // Check remaining slots
            $taken = DB::table('class_bookings')
                ->where('class_id', $class->id)
                ->whereDate(
                    'session_date',
                    $date->toDateString()
                )
                ->whereIn(
                    'status',
                    ['booked', 'attended']
                )
                ->count();

            abort_if(
                $taken >= (int) $class->capacity,
                422,
                'No slots left for this session.'
            );

            // Reactivate a cancelled booking
            if ($existing) {
                DB::table('class_bookings')
                    ->where('id', $existing->id)
                    ->update([
                        'status' => 'booked',
                        'updated_at' => now()
                    ]);

                $id = $existing->id;
            } else {

                // Create a new booking
                $id = DB::table('class_bookings')
                    ->insertGetId([
                        'gym_id' => $gymId,
                        'class_id' => $class->id,
                        'member_id' => $userId,
                        'session_date' => $date->toDateString(),
                        'status' => 'booked',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            return response()->json([
                'ok' => true,
                'booking_id' => $id,
                'message' => 'Your class booking is confirmed.'
            ], $existing ? 200 : 201);

        }, 3);
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL A CLASS BOOKING
    |--------------------------------------------------------------------------
    */
    public function cancelBooking(
        Request $request,
        int $booking
    ) {
        $gymId = $this->selectedGym($request);

        return DB::transaction(function () use (
            $gymId,
            $request,
            $booking
        ) {

            $row = DB::table('class_bookings as b')
                ->join(
                    'gym_classes as c',
                    'c.id',
                    '=',
                    'b.class_id'
                )
                ->where('b.id', $booking)
                ->where('b.gym_id', $gymId)
                ->where(
                    'b.member_id',
                    $request->user()->id
                )
                ->select(
                    'b.*',
                    'c.start_time'
                )
                ->lockForUpdate()
                ->first();

            abort_unless(
                $row,
                404,
                'Booking not found.'
            );

            abort_unless(
                $row->status === 'booked',
                422,
                'Only active bookings can be cancelled.'
            );

            $start = Carbon::parse(
                substr($row->session_date, 0, 10)
            )->setTimeFromTimeString(
                $row->start_time
            );

            abort_unless(
                $start->isFuture(),
                422,
                'This class has already started.'
            );

            $attended = DB::table('attendances')
                ->where('gym_id', $gymId)
                ->where('class_id', $row->class_id)
                ->where(
                    'member_id',
                    $request->user()->id
                )
                ->whereDate(
                    'session_date',
                    $row->session_date
                )
                ->exists();

            abort_if(
                $attended,
                422,
                'Checked-in classes cannot be cancelled.'
            );

            DB::table('class_bookings')
                ->where('id', $row->id)
                ->where('status', 'booked')
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);

            return response()->json([
                'ok' => true,
                'message' => 'Booking cancelled.'
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE HISTORY
    |--------------------------------------------------------------------------
    */
    public function history(Request $request)
    {
        $gymId = $this->selectedGym($request);

        $query = Attendance::where(
            'gym_id',
            $gymId
        )->where(
            'member_id',
            $request->user()->id
        );

        $count = (clone $query)->count();

        $records = $query
            ->orderByDesc('session_date')
            ->orderByDesc('checked_in_at')
            ->limit(100)
            ->get()
            ->map(fn ($attendance) => [
                'id' => $attendance->id,
                'session_date' => $attendance->session_date,
                'class_name' =>
                    $attendance->class_name ?: 'Gym visit',
                'checked_in_at' =>
                    $attendance->checked_in_at
                        ?->toIso8601String(),
                'method' => $attendance->method,
                'membership_state' =>
                    $attendance->membership_state,
            ]);

        return response()->json([
            'attendance' => $records,
            'total' => $count
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE CHECK-IN USING CODE
    |--------------------------------------------------------------------------
    */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'digits:6'
            ]
        ]);

        $gymId = $this->selectedGym($request);
        $user = $request->user();

        abort_unless(
            $this->activeMembership(
                $user->id,
                $gymId
            ),
            422,
            'You need an accepted, unexpired membership to check in.'
        );

        return DB::transaction(function () use (
            $validated,
            $gymId,
            $user
        ) {

            $now = now();

            // Validate attendance code
            $code = AttendanceCode::where(
                'gym_id',
                $gymId
            )
                ->where(
                    'code',
                    $validated['code']
                )
                ->where(
                    'session_date',
                    $now->toDateString()
                )
                ->whereNull('revoked_at')
                ->where('expires_at', '>', $now)
                ->lockForUpdate()
                ->first();

            if (!$code) {
                throw ValidationException::withMessages([
                    'code' =>
                        'Wrong, expired, or revoked attendance code.'
                ]);
            }

            // Find the class
            $class = GymClass::where(
                'gym_id',
                $gymId
            )->findOrFail($code->class_id);

            $days = array_map(
                'intval',
                $class->days ?? []
            );

            abort_unless(
                in_array(
                    $now->dayOfWeekIso,
                    $days,
                    true
                ),
                422,
                'This class is not scheduled today.'
            );

            // Members may check in only from the exact class start time
            // until the class ends (never 15 minutes early).
            $startsAt = $class->startsAt();
            $endsAt = $class->endsAt();

            abort_unless(
                $now->greaterThanOrEqualTo($startsAt) &&
                $now->lessThanOrEqualTo($endsAt),
                422,
                'Check-in opens when your class starts and closes when it ends.'
            );

            // Member must have a booking
            $booking = DB::table('class_bookings')
                ->where('gym_id', $gymId)
                ->where('class_id', $class->id)
                ->where('member_id', $user->id)
                ->whereDate(
                    'session_date',
                    $now->toDateString()
                )
                ->lockForUpdate()
                ->first();

            abort_unless(
                $booking &&
                in_array(
                    $booking->status,
                    ['booked', 'attended'],
                    true
                ),
                422,
                'Join this class session before checking in.'
            );

            // Prevent duplicate attendance
            $already = Attendance::where(
                'gym_id',
                $gymId
            )
                ->where('member_id', $user->id)
                ->where('class_id', $class->id)
                ->where(
                    'session_date',
                    $now->toDateString()
                )
                ->exists();

            if ($already) {
                return response()->json([
                    'ok' => true,
                    'already' => true,
                    'class' => $class->name,
                    'message' => 'Already checked in.'
                ]);
            }

            // Save attendance
            Attendance::create([
                'gym_id' => $gymId,
                'member_id' => $user->id,
                'member_name' => $user->name,
                'class_id' => $class->id,
                'class_name' => $class->name,
                'membership_state' => 'active',
                'override' => false,
                'code_id' => $code->id,
                'session_date' => $now->toDateString(),
                'checked_in_at' => $now,
                'method' => 'member_code',
            ]);

            // Update the booking status
            DB::table('class_bookings')
                ->where('id', $booking->id)
                ->update([
                    'status' => 'attended',
                    'updated_at' => now()
                ]);

            return response()->json([
                'ok' => true,
                'already' => false,
                'class' => $class->name,
                'message' => 'Check-in successful.'
            ]);
        });
    }
}
