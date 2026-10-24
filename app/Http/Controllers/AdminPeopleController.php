<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BrevoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPeopleController extends Controller
{
    private const ROLE_ADMIN = 'admin';
    private const ROLE_COACH = 'gym_owner';

    private const STATUS_ACTIVE   = 'active';
    private const STATUS_INACTIVE = 'inactive';
    private const STATUS_REJECTED = 'rejected';
    private const STATUS_PENDING  = 'pending';

    private function adminOnly(Request $r): void
    {
        abort_unless($r->user()?->role === self::ROLE_ADMIN, 403, 'Admins only.');
    }

    private function statusOf(User $u): string
    {
        return strtolower((string) $u->status);
    }

    // GET /api/admin/people  -> { users: [...], coaches: [...] }
    public function index(Request $r): array
    {
        $this->adminOnly($r);

        $all = User::where('role', '!=', self::ROLE_ADMIN)
            ->orderBy('name')
            ->get();

        [$coachModels, $userModels] = $all->partition(
            fn (User $u) => $u->role === self::ROLE_COACH
        );

        $users = $userModels->map(fn (User $u) => [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'joined'     => optional($u->created_at)->toDateString() ?? now()->toDateString(),
            'last_login' => optional($u->last_login_at)->toIso8601String(),
            'active'     => $this->statusOf($u) === self::STATUS_ACTIVE,
        ])->values();

        $coaches = $coachModels->map(function (User $u) {
            $status = $this->statusOf($u);

            // 'inactive' = an approved coach whose account was deactivated by an admin
            $approval = match ($status) {
                self::STATUS_ACTIVE, self::STATUS_INACTIVE => 'approved',
                self::STATUS_REJECTED                      => 'rejected',
                default                                    => 'pending',
            };

            return [
                'id'         => $u->id,
                'name'       => $u->name,
                'specialty'  => $u->specialty ?? 'General fitness',
                'clients'    => 0, // TODO: replace with withCount('clients') on the query
                'approval'   => $approval,
                'active'     => $status === self::STATUS_ACTIVE,
                'date'       => optional($u->updated_at)->toDateString() ?? now()->toDateString(),
                'last_login' => optional($u->last_login_at)->toIso8601String(),
            ];
        })->values();

        return ['users' => $users, 'coaches' => $coaches];
    }

    // PATCH /api/admin/coaches/{user}/approval  { status: approved|rejected|pending }
    public function approval(Request $r, User $user): array
    {
        $this->adminOnly($r);
        abort_unless($user->role === self::ROLE_COACH, 404, 'Not a coach.');

        $data = $r->validate(['status' => 'required|in:approved,rejected,pending']);

        $wasApproved = in_array(
            $this->statusOf($user),
            [self::STATUS_ACTIVE, self::STATUS_INACTIVE],
            true
        );

        // Already approved (active OR deactivated): do nothing, so a deactivated
        // coach is never silently reactivated and no duplicate email is sent.
        if ($data['status'] === 'approved' && $wasApproved) {
            return ['ok' => true, 'emailed' => false];
        }

        $user->status = $data['status'] === 'approved'
            ? self::STATUS_ACTIVE
            : $data['status'];
        $user->save();

        $emailed = false;

        // Email only when the coach has just become approved
        if ($data['status'] === 'approved') {
            try {
                $error = BrevoMail::send(
                    $user->email,
                    $user->name,
                    'Your Gym Owner account has been approved',
                    $this->approvalEmailHtml($user)
                );

                $emailed = $error === null;

                if ($error) {
                    Log::warning('Coach approval email failed', [
                        'user_id' => $user->id,
                        'error'   => $error,
                    ]);
                }
            } catch (\Throwable $e) {
                report($e); // keeps the original stack trace
            }
        }

        // Details stay in the logs; the client only gets a flag.
        return ['ok' => true, 'emailed' => $emailed];
    }

    // DELETE /api/admin/users/{user}
    public function destroy(Request $r, User $user): array
    {
        $this->adminOnly($r);
        abort_if($user->id === $r->user()->id, 422, 'You cannot delete your own account.');
        abort_if($user->role === self::ROLE_ADMIN, 422, 'Admin accounts cannot be deleted here.');

        // Tip: add the SoftDeletes trait to the User model so this is recoverable.
        $user->delete();

        return ['ok' => true];
    }

    private function approvalEmailHtml(User $user): string
    {
        $first = e($user->first_name ?: $user->name);
        $login = e(route('login'));

        return <<<HTML
<div style="max-width:520px;margin:0 auto;background:#14110e;border-radius:16px;padding:36px;color:#f5f1ea;font-family:Arial,sans-serif;">
  <h1 style="margin:0 0 12px;font-size:24px;">You're approved!</h1>
  <p style="color:#a8a095;line-height:1.6;">Hi {$first},</p>
  <p style="color:#a8a095;line-height:1.6;">An admin has approved your Gym Owner account on Gym Portal. You can now sign in and use your coach dashboard.</p>
  <p style="margin:28px 0;"><a href="{$login}" style="display:inline-block;padding:14px 26px;background:#ff9900;color:#1a1208;font-weight:bold;text-decoration:none;border-radius:10px;">Sign in as Gym Owner</a></p>
  <p style="color:#a8a095;font-size:13px;">On the sign-in page, choose <b>Gym Owner</b> before entering your email and password.</p>
</div>
HTML;
    }
}