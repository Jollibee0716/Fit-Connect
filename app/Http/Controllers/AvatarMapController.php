<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvatarMapController extends Controller
{
    /** Admin dashboard: every user that has a photo. */
    public function admin()
    {
        return response()->json(['avatars' => $this->build(User::query())]);
    }

    /** Gym owner dashboard: own members, admins, and the owner. */
    public function owner(Request $request)
    {
        $me  = $request->user();
        $ids = $this->memberIdsFor($me);

        $query = User::query()->where(function ($q) use ($ids, $me) {
            $q->whereIn('id', $ids)
              ->orWhere('role', 'admin')
              ->orWhere('id', $me->id);
        });

        return response()->json(['avatars' => $this->build($query)]);
    }

    private function build($query): array
    {
        return $query->whereNotNull('avatar_path')
            ->get(['id', 'avatar_path', 'updated_at'])
            ->map(fn ($u) => ['id' => $u->id, 'url' => $u->avatar_url])
            ->values()
            ->all();
    }

    /**
     * user -> gym_owners (user_id) -> gyms (gym_owner_id) -> members (gym_id)
     */
    private function memberIdsFor(User $owner): array
    {
        try {
            $gymOwner = $owner->gymOwner;
            if (!$gymOwner) {
                return [];
            }

            $gymIds = DB::table('gyms')
                ->where('gym_owner_id', $gymOwner->id)
                ->pluck('id');

            if ($gymIds->isEmpty()) {
                return [];
            }

            return DB::table('members')
                ->whereIn('gym_id', $gymIds)
                ->pluck('user_id')
                ->all();
        } catch (\Throwable $e) {
            \Log::warning('memberIdsFor failed: '.$e->getMessage());
            return [];
        }
    }
}