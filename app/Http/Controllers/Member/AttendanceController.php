<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\{Attendance, AttendanceCode};
use App\Support\{GymContext, Membership};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $r)
    {
        $d = $r->validate(['code' => 'required|digits:6']);
        $u = $r->user();
        $gid = GymContext::id($u);
        abort_unless($gid, 403, 'You have not joined a gym.');
        $now = now();

        $k = AttendanceCode::with('class')->where('gym_id', $gid)->where('code', $d['code'])
            ->whereNull('revoked_at')->where('expires_at', '>', $now)->first();
        abort_unless($k, 422, 'Wrong or expired code.');

        $state = Membership::state($u);
        abort_unless($state === 'active', 422, 'Your membership is not active.');

        $a = Attendance::firstOrCreate(
            ['member_id' => $u->id, 'class_id' => $k->class_id, 'session_date' => $k->session_date],
            [
                'gym_id' => $gid,
                'member_name' => $u->name,
                'class_name' => $k->class->name,
                'membership_state' => $state,
                'override' => false,
                'code_id' => $k->id,
                'checked_in_at' => $now,
                'method' => 'member_code',
            ]
        );

        return ['ok' => true, 'class' => $k->class->name, 'already' => !$a->wasRecentlyCreated];
    }
}