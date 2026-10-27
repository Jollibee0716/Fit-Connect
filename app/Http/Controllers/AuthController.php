<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Auth\Events\PasswordReset;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as StrongPassword;

use Illuminate\Support\Str;

class AuthController extends Controller

{

    /** Roles that must enter an emailed code after the password. */

    private const TWO_FACTOR_ROLES = ['admin', 'gym_owner', 'member'];

    public function showLogin()

{

    // Only gyms whose owner is approved can be picked by members

    $gyms = \App\Models\Gym::whereHas('owner.user', fn ($q) => $q->where('status', 'active'))

        ->orderBy('name')

        ->get();

    return view('log_in', compact('gyms'));

}

    public function register(Request $request)

    {

        // Gym owners use their own fields and their own table

        if ($request->input('role') === 'gym_owner') {

            return $this->registerGymOwner($request);

        }

        return $this->registerMember($request);

    }

    /**

      * Gym owner: full name, email, password, confirm password,

      * gym name, phone number, gym address.

      * Account goes in `users` (so login/2FA/reset keep working),

      * gym details go in `gym_owners`.

     */

    private function registerGymOwner(Request $request)

    {

        $data = $request->validate([

            'full_name'    => 'required|string|max:255',

            'email'        => 'required|email|max:255|unique:users,email',

            'password' => [
                'required',
                'string',
                'confirmed',
                'regex:/^\S+$/',
                StrongPassword::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],

            'gym_name'     => 'required|string|max:255',

            'phone_number' => 'required|string|max:30',

            'gym_address'  => 'required|string|max:255',

        ], [
            'password.regex' => 'Spaces are not allowed in passwords.',
        ]);

        DB::transaction(function () use ($data) {

            $user = User::create([

                'name'     => $data['full_name'],

                'email'    => $data['email'],

                'password' => bcrypt($data['password']),

                'role'     => 'gym_owner',

                'status'   => 'pending',

            ]);

            // Fill the existing users columns so the rest of the app keeps working

            $parts = preg_split('/\s+/', trim($data['full_name']), 2);

            $user->forceFill([

                'first_name'     => $parts[0],

                'last_name'      => $parts[1] ?? '',

                'contact_number' => $data['phone_number'],

                'address'        => $data['gym_address'],

            ])->save();

            // Gym details live in the separate table

                        // Owner details

            $owner = $user->gymOwner()->create([

                'gym_name'     => $data['gym_name'],

                'phone_number' => $data['phone_number'],

                'gym_address'  => $data['gym_address'],

            ]);

            // The gym itself is saved in its own table

            $owner->gym()->create([

                'name'    => $data['gym_name'],

                'address' => $data['gym_address'],

                'phone'   => $data['phone_number'],

            ]);

        });

        return redirect()

            ->route('login')

            ->with('success', 'Gym Owner account created. Waiting for admin approval.');

    }

    private function registerMember(Request $request)

    {

        $validated = $request->validate([

            'name'           => 'required|string|max:255',

            'email'          => 'required|email|max:255|unique:users,email',

            'password' => [
                'required',
                'string',
                'confirmed',
                'regex:/^\S+$/',
                StrongPassword::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],

            'role'           => 'required|in:member',

            'gender'         => 'required|in:Male,Female',

            // personal details

            'first_name'     => 'required|string|max:255',

            'last_name'      => 'required|string|max:255',

            'contact_number' => 'required|string|max:30',

            'date_of_birth'  => 'required|date|before:today',

            'address'        => 'required|string|max:255',

            // member only

            'emergency_contact_name'   => 'required|string|max:255',

            'emergency_contact_number' => 'required|string|max:30',

            'height_cm'                => 'nullable|numeric|min:0|max:999',

            'weight_kg'                => 'nullable|numeric|min:0|max:999',

            'fitness_goal'             => 'nullable|string|max:255',

        ], [
            'password.regex' => 'Spaces are not allowed in passwords.',
        ]);

        $user = DB::transaction(function () use ($validated) {

            $user = User::create([

                'name'     => $validated['name'],

                'email'    => $validated['email'],

                'password' => bcrypt($validated['password']),

                'role'     => 'member',

                'gender'   => $validated['gender'],

                'status'   => 'active',

            ]);

            $user->forceFill([

                'first_name'     => $validated['first_name'],

                'last_name'      => $validated['last_name'],

                'contact_number' => $validated['contact_number'],

                'date_of_birth'  => $validated['date_of_birth'],

                'address'        => $validated['address'],

            ])->save();

            DB::table('members')->insert([

                'user_id'                  => $user->id,

                'emergency_contact_name'   => $validated['emergency_contact_name'] ?? null,

                'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,

                'height_cm'                => $validated['height_cm'] ?? null,

                'weight_kg'                => $validated['weight_kg'] ?? null,

                'fitness_goal'             => $validated['fitness_goal'] ?? null,

                'join_date'                => now()->toDateString(),

                'created_at'               => now(),

                'updated_at'               => now(),

            ]);

            return $user;

        });

        Auth::login($user);

        // Members are signed in right after registering, so record it

        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->route('dashboard');

    }

    public function login(Request $request)

    {

        $credentials = $request->validate([

            'email'    => 'required|email',

            'password' => 'required',

            'role'     => 'required|in:member,gym_owner,admin',

        ]);

        // 1) Check the password WITHOUT logging anyone in yet

        $user = User::where('email', $credentials['email'])

            ->where('role', $credentials['role'])

            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {

            return back()

                ->withInput($request->only('email', 'role'))

                ->withErrors(['email' => 'Invalid email, password, or account type.']);

        }

        // 2) Block accounts that may not sign in

        $status = strtolower((string) $user->status);

        if ($status === 'inactive') {

            return back()

                ->withInput($request->only('email', 'role'))

                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);

        }

        if ($status === 'rejected') {

            return back()

                ->withInput($request->only('email', 'role'))

                ->withErrors(['email' => 'Your account application was not approved.']);

        }

        if ($status !== 'active') {

            return back()

                ->withInput($request->only('email', 'role'))

                ->withErrors(['email' => 'Your account is waiting for admin approval.']);

        }

        // 3) Two-factor: send a code instead of logging in

        if (in_array($user->role, self::TWO_FACTOR_ROLES, true)) {

            return app(TwoFactorController::class)->start($request, $user);

        }

        // 4) Normal login

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        // DashboardController@index sends each role to its own dashboard

        return redirect()->route('dashboard');

    }

    public function showForgotPassword()

    {

        return view('forgot-password');

    }

    public function sendResetLink(Request $request)

    {

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Unknown email OR admin: same "not registered" error

        if (! $user || $user->isAdmin()) {

            return back()

                ->withInput($request->only('email'))

                ->withErrors(['email' => 'This email is not registered in the system.']);

        }

        $status = strtolower((string) $user->status);

        if ($status === 'inactive') {

            return back()

                ->withInput($request->only('email'))

                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);

        }

        if ($status !== 'active') {

            return back()

                ->withInput($request->only('email'))

                ->withErrors(['email' => 'Your account is still waiting for admin approval.']);

        }

        try {

            Password::sendResetLink(['email' => $user->email]);

        } catch (\Throwable $e) {

            report($e);

            return back()

                ->withInput($request->only('email'))

                ->withErrors(['email' => 'We could not send the email right now. Please try again later.']);

        }

        return back()->with('success', 'A password reset link has been sent to your email.');

    }

    public function resetPassword(Request $request)

    {

        $request->validate([

            'token'    => 'required',

            'email'    => 'required|email',

            'password' => [
                'required',
                'string',
                'confirmed',
                'regex:/^\S+$/',
                StrongPassword::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],

        ], [
            'password.regex' => 'Spaces are not allowed in passwords.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Admins can never reset through email, even with a valid token.

        // Deactivated or unapproved accounts cannot reset either.

        if (! $user || $user->isAdmin() || strtolower((string) $user->status) !== 'active') {

            return back()

                ->withInput($request->only('email'))

                ->withErrors(['email' => 'This password reset link is invalid.']);

        }

        $status = Password::reset(

            $request->only('email', 'password', 'password_confirmation', 'token'),

            function ($user, $password) {

                $user->forceFill([

                    'password'       => Hash::make($password),

                    'remember_token' => Str::random(60),

                ])->save();

                event(new PasswordReset($user));

            }

        );

        if ($status === Password::PASSWORD_RESET) {

            return redirect()->route('login')->with('success', 'Password reset. You can log in now.');

        }

        return back()

            ->withInput($request->only('email'))

            ->withErrors(['email' => __($status)]);

    }

    public function logout(Request $request)

    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');

    }

}