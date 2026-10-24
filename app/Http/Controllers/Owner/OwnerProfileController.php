<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OwnerProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $gym  = $user->gymOwner;

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
        ]);

        $request->user()->gymOwner()->updateOrCreate([], $data);

        return response()->json(['message' => 'Gym details saved']);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors'  => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $user->forceFill(['password' => $request->password])->save();

        return response()->json(['message' => 'Password changed']);
    }
}