<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\GymContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassBookingController extends Controller
{
    public function index(Request $request, int $class)
    {
        $gymId = (int) GymContext::require($request);
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d']]);
        $date = $data['date'];

        $gymClass = DB::table('gym_classes')
            ->where('gym_id', $gymId)->where('id', $class)->first();
        abort_unless($gymClass, 404, 'Class not found in your gym.');

        $days = json_decode($gymClass->days, true) ?: [];
        $weekday = (int) \Carbon\Carbon::parse($date)->isoWeekday();
        abort_unless(in_array($weekday, array_map('intval', $days), true),
            422, 'This class is not scheduled on the selected date.');

        $bookings = DB::table('class_bookings as b')
            ->join('users as u', 'u.id', '=', 'b.member_id')
            ->where('b.gym_id', $gymId)
            ->where('b.class_id', $class)
            ->where('b.session_date', $date)
            ->select('b.id', 'b.member_id', 'u.name', 'u.email',
                'b.status', 'b.created_at as booked_at')
            ->orderBy('b.created_at')->get();

        $attended = DB::table('attendances')
            ->where('gym_id', $gymId)
            ->where('class_id', $class)
            ->whereDate('session_date', $date)
            ->pluck('member_id')->map(fn ($x) => (int) $x)->all();

        $booked = $bookings->whereIn('status', ['booked', 'attended'])->count();
        return response()->json([
            'class_id' => $class, 'session_date' => $date,
            'capacity' => (int) $gymClass->capacity,
            'booked' => $booked,
            'available' => max(0, (int) $gymClass->capacity - $booked),
            'bookings' => $bookings->map(fn ($b) => [
                'id' => $b->id, 'member_id' => $b->member_id,
                'name' => $b->name, 'email' => $b->email,
                'status' => $b->status, 'booked_at' => $b->booked_at,
                'attended' => in_array((int) $b->member_id, $attended, true),
            ])->values(),
        ]);
    }

    public function update(Request $request, int $booking)
    {
        $gymId = (int) GymContext::require($request);
        $request->validate([
            'status' => ['required', Rule::in(['cancelled'])],
        ]);

        return DB::transaction(function () use ($gymId, $booking) {
            $row = DB::table('class_bookings')
                ->where('gym_id', $gymId)->where('id', $booking)
                ->lockForUpdate()->first();
            abort_unless($row, 404, 'Booking not found.');
            abort_unless($row->status === 'booked', 422,
                'Only active, uncompleted bookings may be cancelled.');
            $hasAttendance = DB::table('attendances')
                ->where('gym_id', $gymId)
                ->where('class_id', $row->class_id)
                ->where('member_id', $row->member_id)
                ->whereDate('session_date', $row->session_date)
                ->exists();
            abort_if($hasAttendance, 422, 'Cannot cancel a checked-in booking.');
            DB::table('class_bookings')->where('id', $booking)
                ->update(['status' => 'cancelled', 'updated_at' => now()]);
            return response()->json(['ok' => true]);
        });
    }
}
