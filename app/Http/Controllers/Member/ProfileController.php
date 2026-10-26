<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'user' => $this->data($request->user())
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $changes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        foreach (['phone', 'location', 'bio'] as $field) {
            if (Schema::hasColumn('users', $field)) {
                $changes[$field] = $validated[$field] ?? null;
            }
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update($changes);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $this->data($user->fresh())
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check(
            $validated['current_password'],
            $user->password
        )) {
            return response()->json([
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($validated['password'])
            ]);

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }

    private function data($user)
    {
        $result = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'avatar_url' => $user->avatar_url ?? null,
        ];

        foreach ([
            'phone',
            'location',
            'bio',
            'status',
            'last_login_at'
        ] as $field) {
            $result[$field] = Schema::hasColumn('users', $field)
                ? $user->{$field}
                : null;
        }

        return $result;
    }
}
