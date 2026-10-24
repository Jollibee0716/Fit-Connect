<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    private function role(User $user): string
    {
        return match (strtolower(trim((string) $user->role))) {
            'admin', 'administrator' => 'admin',
            'owner', 'gym_owner' => 'owner',
            'member', 'user' => 'member',
            default => 'unknown',
        };
    }

    /**
     * IMPORTANT: This assumes gyms(id,user_id) and gym_members(gym_id,user_id).
     * Adapt to your REAL member enrollment schema before enabling owner-member messaging.
     * Missing tables or columns deny access rather than permitting cross-gym messages.
     */
    private function sharesGym(User $owner, User $member): bool
    {
        if (!Schema::hasTable('gyms') || !Schema::hasTable('gym_members')) {
            return false;
        }
        foreach (['id', 'user_id'] as $column) {
            if (!Schema::hasColumn('gyms', $column)) {
                return false;
            }
        }
        foreach (['gym_id', 'user_id'] as $column) {
            if (!Schema::hasColumn('gym_members', $column)) {
                return false;
            }
        }

        return DB::table('gyms')
            ->join('gym_members', 'gym_members.gym_id', '=', 'gyms.id')
            ->where('gyms.user_id', $owner->id)
            ->where('gym_members.user_id', $member->id)
            ->exists();
    }

    private function allowed(User $me, User $other): bool
    {
        if ($me->id === $other->id) {
            return false;
        }

        $mine = $this->role($me);
        $theirs = $this->role($other);

        // Only Admin <-> Gym Owner (not Admin <-> Member).
        if (($mine === 'admin' && $theirs === 'owner') ||
            ($mine === 'owner' && $theirs === 'admin')) {
            return true;
        }

        // Only Gym Owner <-> registered member of that owner's gym.
        if ($mine === 'owner' && $theirs === 'member') {
            return $this->sharesGym($me, $other);
        }
        if ($mine === 'member' && $theirs === 'owner') {
            return $this->sharesGym($other, $me);
        }

        // Explicitly deny all other combinations.
        return false;
    }

    public function contacts(Request $request)
    {
        /** @var User|null $me */
        $me = $request->user();
        abort_unless($me && $this->role($me) !== 'unknown', 403);

        $users = User::query()
            ->select('id', 'name', 'role')
            ->where('id', '!=', $me->id)
            ->orderBy('name')
            ->get()
            ->filter(fn (User $user) => $this->allowed($me, $user));

        $contacts = $users->map(function (User $user) use ($me) {
            $pair = static function ($query) use ($me, $user) {
                $query->where(function ($q) use ($me, $user) {
                    $q->where('sender_id', $me->id)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($me, $user) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $me->id);
                });
            };

            $last = ChatMessage::where($pair)->latest('id')->first();
            $unread = ChatMessage::where('sender_id', $user->id)
                ->where('receiver_id', $me->id)
                ->whereNull('read_at')
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $this->role($user),
                'last_message' => $last?->body,
                'last_at' => $last?->created_at?->toIso8601String(),
                'unread' => $unread,
            ];
        })->sortByDesc('last_at')->values();

        return response()->json([
            'contacts' => $contacts,
            'unread' => $contacts->sum('unread'),
        ]);
    }

    public function thread(Request $request, User $user)
    {
        /** @var User|null $me */
        $me = $request->user();
        abort_unless($me && $this->allowed($me, $user), 403, 'Conversation not allowed.');

        ChatMessage::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = ChatMessage::where(function ($query) use ($me, $user) {
            $query->where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me->id)
                    ->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)
                    ->where('receiver_id', $me->id);
            });
        })->latest('id')->limit(100)->get()->reverse()->values();

        return response()->json([
            'my_id' => $me->id,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, User $user)
    {
        /** @var User|null $me */
        $me = $request->user();
        abort_unless($me && $this->allowed($me, $user), 403, 'Message not allowed.');

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $body = trim($validated['body']);
        if ($body === '') {
            throw ValidationException::withMessages(['body' => 'Enter a message.']);
        }

        $message = ChatMessage::create([
            'sender_id' => $me->id,
            'receiver_id' => $user->id,
            'body' => $body,
        ]);

        return response()->json(['success' => true, 'message' => $message], 201);
    }
}
