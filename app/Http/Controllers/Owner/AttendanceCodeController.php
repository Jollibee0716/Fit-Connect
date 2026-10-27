<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\{AttendanceCode, GymClass};
use App\Support\GymContext;
use Illuminate\Http\Request;

class AttendanceCodeController extends Controller
{
    public function today(Request $r)
    {
        $gid = GymContext::require($r);
        $now = now();

        $classes = GymClass::with('trainer')->where('gym_id', $gid)->get()
            ->filter(fn ($c) => in_array($now->dayOfWeekIso, $c->days))
            ->sortBy('start_time')->values();

        $codes = AttendanceCode::whereIn('class_id', $classes->pluck('id'))
            ->where('session_date', $now->toDateString())
            ->whereNull('revoked_at')->where('expires_at', '>', $now)
            ->get()->keyBy('class_id');

        return ['classes' => $classes->map(function ($c) use ($now, $codes) {
            $k = $codes->get($c->id);
            $status = $now->lt($c->startsAt()->subMinutes(15)) ? 'upcoming'
                : ($now->gt($c->endsAt()) ? 'ended' : 'open');
            return $c->toApi() + [
                'status' => $status,
                'code' => $k ? [
                    'id' => $k->id,
                    'code' => $k->code,
                    'expires_at' => $k->expires_at->toIso8601String(),
                ] : null,
            ];
        })];
    }

    public function generate(Request $r, GymClass $class)
    {
        $gid = GymContext::require($r);
        abort_unless($class->gym_id === $gid, 403);
        $now = now();

        abort_unless(in_array($now->dayOfWeekIso, $class->days), 422, 'This class does not run today.');
        abort_unless($now->between($class->startsAt()->subMinutes(15), $class->endsAt()), 422,
            'Codes can only be generated from 15 minutes before the class until it ends.');

        // one live code per class
        AttendanceCode::where('class_id', $class->id)->where('session_date', $now->toDateString())
            ->whereNull('revoked_at')->update(['revoked_at' => $now]);

        // unique among live codes in the gym, so members only type the code
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $taken = AttendanceCode::where('gym_id', $gid)->where('code', $code)
                ->whereNull('revoked_at')->where('expires_at', '>', $now)->exists();
        } while ($taken);

        $minutes = min(max((int) $r->input('minutes', 15), 1), 60);
        $expires = $now->copy()->addMinutes($minutes);
        if ($expires->gt($class->endsAt())) $expires = $class->endsAt();

        $k = AttendanceCode::create([
            'gym_id' => $gid,
            'class_id' => $class->id,
            'session_date' => $now->toDateString(),
            'code' => $code,
            'expires_at' => $expires,
            'created_by' => $r->user()->id,
        ]);

        return ['id' => $k->id, 'code' => $k->code, 'expires_at' => $k->expires_at->toIso8601String()];
    }

    public function revoke(Request $r, AttendanceCode $code)
    {
        abort_unless($code->gym_id === GymContext::require($r), 403);
        $code->update(['revoked_at' => now()]);
        return ['ok' => true];
    }
}