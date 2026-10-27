<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OwnerProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $gym  = $user->gymOwner;
        $gymRow = $gym ? DB::table('gyms')->where('gym_owner_id', $gym->id)->first() : null;

        return response()->json([
            'user' => [
                'name'          => $user->name,
                'email'         => $user->email,
                'phone'         => $user->phone,
                'location'      => $user->location,
                'bio'           => $user->bio,
                'last_login_at' => optional($user->last_login_at)->toIso8601String(),
                'created_at'    => optional($user->created_at)->toIso8601String(),
            ],
            'gym' => [
                'gym_name'     => $gym->gym_name ?? '',
                'gym_address'  => $gym->gym_address ?? '',
                'phone_number' => $gym->phone_number ?? '',
                'about'        => $gymRow->about ?? '',
                'hours'        => $gymRow->hours ?? '',
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:255'],
            'bio'      => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profile saved']);
    }

    public function updateGym(Request $request): JsonResponse
    {
        $data = $request->validate([
            'gym_name'     => ['required', 'string', 'max:255'],
            'gym_address'  => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'about'        => ['nullable', 'string', 'max:2000'],
            'hours'        => ['nullable', 'string', 'max:120'],
        ]);

        $request->user()->load('gymOwner');
        $owner = $request->user()->gymOwner;
        abort_unless($owner, 404, 'Gym owner profile not found.');

        abort_unless(DB::table('gyms')->where('gym_owner_id', $owner->id)->exists(), 404, 'Gym record not found.');

        DB::transaction(function () use ($owner, $data) {
            // Keep the legacy owner profile fields synchronized.
            $owner->update([
                'gym_name' => $data['gym_name'],
                'gym_address' => $data['gym_address'] ?? '',
                'phone_number' => $data['phone_number'] ?? '',
            ]);

            // The gyms table is the shared source read by Admin and Members.
            DB::table('gyms')->where('gym_owner_id', $owner->id)->update([
                'name' => $data['gym_name'],
                'address' => $data['gym_address'] ?? '',
                'phone' => $data['phone_number'] ?? '',
                'about' => $data['about'] ?? null,
                'hours' => $data['hours'] ?? null,
                'updated_at' => now(),
            ]);
        });

        return response()->json(['message' => 'Gym details saved']);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'regex:/^\S+$/u',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ], [
            'password.regex' => 'Spaces and other whitespace are not allowed in passwords.',
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors'  => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        if (Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Please choose a password different from your current password.',
                'errors' => ['password' => ['Your new password must be different from your current password.']],
            ], 422);
        }

        $user->forceFill(['password' => Hash::make($validated['password'])])->save();

        return response()->json(['message' => 'Password changed']);
    }
}
