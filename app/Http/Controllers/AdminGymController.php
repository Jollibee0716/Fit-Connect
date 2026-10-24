<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminGymController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            $request->user() &&
            strtolower((string) $request->user()->role) === 'admin',
            403,
            'Admin access required.'
        );

        if (!Schema::hasTable('gyms')) {
            return response()->json([
                'gyms' => [],
                'message' => 'Gyms table does not exist.'
            ], 500);
        }

        $gyms = DB::table('gyms')
            ->orderByDesc('id')
            ->get()
            ->map(function ($gym) {

                $ownerName = '';
                $ownerEmail = '';
                $ownerUserId = null;

                // Get gym owner information.
                if (
                    !empty($gym->gym_owner_id) &&
                    Schema::hasTable('gym_owners')
                ) {
                    $owner = DB::table('gym_owners')
                        ->where('id', $gym->gym_owner_id)
                        ->first();

                    if ($owner) {
                        $ownerName = $owner->name ?? '';
                        $ownerEmail = $owner->email ?? '';

                        if (isset($owner->user_id)) {
                            $ownerUserId = $owner->user_id;

                            if (Schema::hasTable('users')) {
                                $account = DB::table('users')
                                    ->where('id', $ownerUserId)
                                    ->first();

                                if ($account) {
                                    $ownerName =
                                        $account->name ?? $ownerName;

                                    $ownerEmail =
                                        $account->email ?? $ownerEmail;
                                }
                            }
                        }
                    }
                }

                // Get current members from the SAME table
                // used by Gym Owner's unenrollment function.
                $memberIds = [];

                if (
                    Schema::hasTable('members') &&
                    Schema::hasColumn('members', 'gym_id') &&
                    Schema::hasColumn('members', 'user_id')
                ) {
                    $memberIds = DB::table('members')
                        ->where('gym_id', $gym->id)
                        ->distinct()
                        ->pluck('user_id')
                        ->all();
                }

                return [
                    'id' => $gym->id,

                    'gym_owner_id' => $gym->gym_owner_id,
                    'user_id' => $ownerUserId,

                    'gym_name' => $gym->name,
                    'name' => $gym->name,

                    'gym_address' => $gym->address,
                    'address' => $gym->address,

                    'phone_number' => $gym->phone,
                    'phone' => $gym->phone,

                    'hours' => $gym->hours,
                    'about' => $gym->about,

                    'owner_name' => $ownerName,
                    'owner_email' => $ownerEmail,

                    // Only currently enrolled members.
                    'member_ids' => $memberIds,
                    'total_clients' => count($memberIds),

                    'status' => null,

                    'created_at' => $gym->created_at,
                    'updated_at' => $gym->updated_at,
                ];
            });

        return response()->json([
            'gyms' => $gyms,
        ]);
    }
}
