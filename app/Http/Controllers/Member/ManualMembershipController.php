<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ManualMembershipController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer|min:1',
            'method' => 'required|in:GCash,Maya,Bank Transfer',
            'reference' => 'required|string|min:4|max:100',
            'proof' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);
        $user = $request->user();
        $gymId = (int) $request->session()->get('fitconnect_selected_gym_id', 0);
        abort_if($gymId < 1, 422, 'Select a gym first.');
        $plan = DB::table('membership_plans')->where('id', $data['plan_id'])
            ->where('gym_id', $gymId)->where('is_active', 1)->first();
        abort_unless($plan, 404, 'Membership plan not found in selected gym.');
        abort_if((float) $plan->price <= 0 || (int) $plan->days <= 0, 422, 'Invalid membership plan.');

        // Save proof privately: only the member and gym owner can retrieve it.
        $path = $request->file('proof')->store('membership-proofs', 'local');
        try {
            $id = DB::transaction(function () use ($data, $user, $gymId, $plan, $path) {
                DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
                $pending = DB::table('payments')->where('gym_id', $gymId)
                    ->where('user_id', $user->id)->where('type', 'membership')
                    ->where('status', 'pending')->exists();
                if ($pending) throw ValidationException::withMessages([
                    'plan_id' => 'You already have a pending membership payment. Ask the gym owner to review it first.'
                ]);
                $accepted = DB::table('payments')->where('gym_id', $gymId)
                    ->where('user_id', $user->id)->where('type', 'membership')
                    ->where('status', 'accepted')->whereDate('due_date', '>=', today())->get();
                foreach ($accepted as $payment) {
                    $tier = DB::table('membership_plans')->where('gym_id', $gymId)
                        ->where('name', $payment->plan)->value('tier');
                    if ($tier === null || (int)$plan->tier <= (int)$tier) {
                        throw ValidationException::withMessages(['plan_id' => 'You already have an active membership. Only a higher-tier upgrade is allowed.']);
                    }
                }
                $duplicate = DB::table('payments')->where('gym_id', $gymId)
                    ->where('method', $data['method'])->where('reference', trim($data['reference']))
                    ->whereIn('status', ['pending', 'accepted'])->exists();
                if ($duplicate) throw ValidationException::withMessages([
                    'reference' => 'This reference number has already been submitted to this gym.'
                ]);
                return DB::table('payments')->insertGetId([
                    'gym_id' => $gymId, 'user_id' => $user->id, 'type' => 'membership',
                    'plan' => $plan->name, 'plan_days' => $plan->days, 'amount' => $plan->price,
                    'method' => $data['method'], 'reference' => trim($data['reference']),
                    'proof_path' => $path,
                    // Existing schema requires this date; status remains pending and access is not granted.
                    'paid_at' => today()->toDateString(), 'due_date' => null,
                    'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
                ]);
            }, 3);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }
        return response()->json(['payment_id' => $id, 'message' => 'Proof submitted. Your gym owner must verify and approve your payment.'], 201);
    }

    public function proof(Request $request, int $payment)
    {
        $row = DB::table('payments')
            ->where('id', $payment)
            ->where('type', 'membership')
            ->first();
        abort_unless($row && $row->proof_path, 404);

        $ownsPayment = (int) $row->user_id === (int) $request->user()->id;
        $owner = $request->user()->gymOwner;
        $ownsGym = $owner && DB::table('gyms')
            ->where('id', $row->gym_id)
            ->where('gym_owner_id', $owner->id)
            ->exists();
        abort_unless($ownsPayment || $ownsGym, 403);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($row->proof_path), 404);

        $path = $disk->path($row->proof_path);
        $mime = mime_content_type($path);
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true), 415,
            'Payment proof is not a supported image.');

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

}
