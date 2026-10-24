<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BrevoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    private const TTL_MIN      = 10;  // code lifetime
    private const MAX_ATTEMPTS = 5;   // wrong guesses before the code is thrown away

    /**
     * Call this from AuthController@login AFTER the password (and any role / status checks)
     * have passed, instead of logging the user in.
     */
    public function start(Request $request, User $user)
    {
        $request->session()->regenerate();
        $request->session()->put('2fa.user_id', $user->id);
        $request->session()->put('2fa.remember', $request->boolean('remember'));

        $this->sendCode($user);

        return redirect()->route('two-factor.show');
    }

    public function show(Request $request)
    {
        if (! $this->pendingUser($request)) {
            return redirect()->route('login');
        }

        return view('two-factor');
    }

    public function verify(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|digits:6']);

        $key   = '2fa:code:' . $user->id;
        $tries = '2fa:tries:' . $user->id;
        $hash  = Cache::get($key);

        if (! $hash) {
            return back()->withErrors(['code' => 'This code has expired. Request a new one.']);
        }

        if (Cache::get($tries, 0) >= self::MAX_ATTEMPTS) {
            Cache::forget($key);
            Cache::forget($tries);
            $request->session()->forget(['2fa.user_id', '2fa.remember']);

            return redirect()->route('login')
                ->withErrors(['email' => 'Too many wrong codes. Please sign in again.']);
        }

        if (! Hash::check($request->code, $hash)) {
            Cache::add($tries, 0, now()->addMinutes(self::TTL_MIN));
            Cache::increment($tries);

            return back()->withErrors(['code' => 'That code is not correct.']);
        }

        // Success
        Cache::forget($key);
        Cache::forget($tries);

        if (strtolower((string) $user->status) === 'inactive') {
            $request->session()->forget(['2fa.user_id', '2fa.remember']);

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }

        $remember = (bool) $request->session()->pull('2fa.remember', false);
        $request->session()->forget('2fa.user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('login');
        }

        $this->sendCode($user);

        return back()->with('status', 'A new code has been sent to your email.');
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('2fa.user_id');

        return $id ? User::find($id) : null;
    }

    private function sendCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put('2fa:code:' . $user->id, Hash::make($code), now()->addMinutes(self::TTL_MIN));
        Cache::forget('2fa:tries:' . $user->id);

        $first = e($user->first_name ?: $user->name);
        $mins  = self::TTL_MIN;

        $html = <<<HTML
<div style="max-width:520px;margin:0 auto;background:#14110e;border-radius:16px;padding:36px;color:#f5f1ea;font-family:Arial,sans-serif;">
  <h1 style="margin:0 0 12px;font-size:24px;">Your sign-in code</h1>
  <p style="color:#a8a095;line-height:1.6;">Hi {$first}, use this code to finish signing in:</p>
  <p style="margin:24px 0;font-size:34px;letter-spacing:8px;font-weight:bold;color:#ff9900;">{$code}</p>
  <p style="color:#a8a095;font-size:13px;">It expires in {$mins} minutes. If you did not try to sign in, change your password.</p>
</div>
HTML;

        try {
            $error = BrevoMail::send($user->email, $user->name, 'Your sign-in code', $html);
            if ($error) {
                report(new \RuntimeException('2FA email failed: ' . $error));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}