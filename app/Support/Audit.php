<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class Audit
{
    public static function log(string $table, $recordId, string $action, ?int $gymId,
                               string $summary, ?array $old = null, array $new = []): void
    {
        try {
            $gymId = $gymId ?? self::resolveGymId();

            DB::table('gym_audit_logs')->insert([
                'gym_id'     => $gymId,
                'user_id'    => auth()->id(),
                'action'     => $action,
                'entity'     => $table,
                'entity_id'  => $recordId,
                'summary'    => mb_substr($summary, 0, 255),
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);   // a logging problem must never block the owner's action
        }
    }

    private static function resolveGymId(): ?int
    {
        $gym = DB::table('gyms')
            ->join('gym_owners', 'gym_owners.id', '=', 'gyms.gym_owner_id')
            ->where('gym_owners.user_id', auth()->id())
            ->select('gyms.id')
            ->first();

        return $gym ? (int) $gym->id : null;
    }
}