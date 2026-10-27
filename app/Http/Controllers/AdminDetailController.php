<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDetailController extends Controller
{
    /** Full details shown in the popup when the admin clicks a name. */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Extra details live in a different table depending on the role
        $profile = match ($user->role) {
            'gym_owner' => DB::table('gym_owners')->where('user_id', $user->id)->first(),
            'member'    => DB::table('members')->where('user_id', $user->id)->first(),
            default     => null,
        };

        return response()->json([
            'user'    => $user->makeHidden(['password', 'remember_token'])->toArray(),
            'profile' => $profile,
        ]);
    }
}