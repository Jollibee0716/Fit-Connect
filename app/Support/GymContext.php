<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GymContext
{
    public static function id(User $u): ?int
    {
        return $u->role === 'gym_owner' ? self::ownerGym($u) : self::memberGym($u);
    }

    public static function require(Request $r): int
    {
        $id = self::id($r->user());
        abort_unless($id, 403, 'No gym is linked to this account.');
        return $id;
    }

    private static function ownerGym(User $u): ?int
    {
        // users.id -> gym_owners.<user column> -> gyms.gym_owner_id
        if (Schema::hasTable('gym_owners')) {
            foreach (['user_id', 'owner_id'] as $col) {
                if (!Schema::hasColumn('gym_owners', $col)) continue;
                $ownerId = DB::table('gym_owners')->where($col, $u->id)->value('id');
                if ($ownerId) {
                    $gid = DB::table('gyms')->where('gym_owner_id', $ownerId)->value('id');
                    if ($gid) return (int) $gid;
                }
            }
        }
        // gym_owner_id may hold the user id directly
        $gid = DB::table('gyms')->where('gym_owner_id', $u->id)->value('id');
        return $gid ? (int) $gid : null;
    }

    private static function memberGym(User $u): ?int
    {
        // Common join tables. Remove or edit once we know yours.
        foreach (['gym_members', 'gym_user', 'memberships', 'members'] as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'gym_id')) continue;
            foreach (['user_id', 'member_id'] as $col) {
                if (!Schema::hasColumn($table, $col)) continue;
                $gid = DB::table($table)->where($col, $u->id)->value('gym_id');
                if ($gid) return (int) $gid;
            }
        }
        return null;
    }
}