<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class OwnerAuditController extends Controller
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

    public function index()
    {
        $logs = DB::table('gym_audit_logs as l')
            ->leftJoin('users', 'users.id', '=', 'l.user_id')
            ->where('l.gym_id', $this->gymId())
            ->orderByDesc('l.id')
            ->limit(200)
            ->get(['l.id', 'l.action', 'l.entity', 'l.summary', 'l.created_at', 'users.name as who'])
            ->map(fn ($l) => [
                'id'         => $l->id,
                'who'        => $l->who ?? 'System',
                'action'     => $l->action,
                'what'       => $l->entity,
                'summary'    => $l->summary,
                'created_at' => $l->created_at,
            ]);

        return ['logs' => $logs];
    }
}