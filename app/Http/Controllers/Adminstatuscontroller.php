<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BrevoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminStatusController extends Controller
{
    public function update(Request $request, User $user)
    {
        $data = $request->validate(['active' => 'required|boolean']);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot deactivate your own account.'], 422);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admin accounts cannot be deactivated here.'], 422);
        }

        // For coaches, status also stores approval, so only approved coaches can be toggled
        if ($user->role === 'gym_owner' && !in_array(strtolower((string) $user->status), ['active', 'inactive'], true)) {
            return response()->json(['message' => 'Approve this coach first.'], 422);
        }

        $wasInactive = strtolower((string) $user->status) === 'inactive';

        $user->status = $data['active'] ? 'active' : 'inactive';
        $user->save();

        // Reactivation email (only when an inactive account is switched back on)
        $emailed = false;
        $error   = null;

        if ($data['active'] && $wasInactive) {
            $first = e($user->first_name ?: $user->name);
            $login = route('login');
            $who   = $user->role === 'gym_owner' ? 'gym_owner' : 'member';
            $hint  = $user->role === 'gym_owner'
                ? 'On the sign-in page, choose <b>Gym Owner</b> before entering your email and password.'
                : 'Sign in with your usual email and password.';

            $html = <<<HTML
<div style="max-width:520px;margin:0 auto;background:#14110e;border-radius:16px;padding:36px;color:#f5f1ea;font-family:Arial,sans-serif;">
  <h1 style="margin:0 0 12px;font-size:24px;">Your account is active again</h1>
  <p style="color:#a8a095;line-height:1.6;">Hi {$first},</p>
  <p style="color:#a8a095;line-height:1.6;">Good news! An admin has reactivated your {$who} account on Gym Portal. You can sign in again.</p>
  <p style="margin:28px 0;"><a href="{$login}" style="display:inline-block;padding:14px 26px;background:#ff9900;color:#1a1208;font-weight:bold;text-decoration:none;border-radius:10px;">Sign in</a></p>
  <p style="color:#a8a095;font-size:13px;">{$hint}</p>
</div>
HTML;

            try {
                $error   = BrevoMail::send($user->email, $user->name, 'Your account has been reactivated', $html);
                $emailed = $error === null;
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            if ($error) {
                report(new \RuntimeException('Reactivation email failed: ' . $error));
            }
        }

        // Sign the person out everywhere (works when SESSION_DRIVER=database)
        if (!$data['active'] && config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return response()->json([
            'ok'      => true,
            'status'  => $user->status,
            'emailed' => $emailed,
            'error'   => $error,
        ]);
    }
}