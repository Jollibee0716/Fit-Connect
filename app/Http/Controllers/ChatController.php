<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    private const TABLE = 'chat_messages';

    /*
    |--------------------------------------------------------------------------
    | GET GYM IDS BELONGING TO A MEMBER
    |--------------------------------------------------------------------------
    */
    private function memberGymIds($memberId, $includeSession = false)
    {
        $gymIds = [];

        if ($includeSession) {
            $selected = (int) session(
                'fitconnect_selected_gym_id',
                0
            );

            if ($selected > 0) {
                $gymIds[] = $selected;
            }
        }

        $gymIds = array_merge(
            $gymIds,
            DB::table('members')
                ->where('user_id', $memberId)
                ->whereNotNull('gym_id')
                ->pluck('gym_id')
                ->all()
        );

        $gymIds = array_merge(
            $gymIds,
            DB::table('payments')
                ->where('user_id', $memberId)
                ->where('type', 'membership')
                ->whereIn('status', ['pending', 'accepted'])
                ->pluck('gym_id')
                ->all()
        );

        return collect($gymIds)
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | GET GYMS OWNED BY THE LOGGED-IN OWNER
    |--------------------------------------------------------------------------
    */
    private function ownerGymIds($ownerUserId)
    {
        return DB::table('gyms')
            ->join(
                'gym_owners',
                'gyms.gym_owner_id',
                '=',
                'gym_owners.id'
            )
            ->where('gym_owners.user_id', $ownerUserId)
            ->pluck('gyms.id')
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | GET AUTHORIZED CHAT CONTACTS
    |--------------------------------------------------------------------------
    */
    private function allowedContacts($me)
    {
        $ids = [];

        if ($me->role === 'member') {
            $gymIds = $this->memberGymIds(
                $me->id,
                true
            );

            if (!empty($gymIds)) {
                $ids = DB::table('gyms')
                    ->join(
                        'gym_owners',
                        'gyms.gym_owner_id',
                        '=',
                        'gym_owners.id'
                    )
                    ->join(
                        'users',
                        'gym_owners.user_id',
                        '=',
                        'users.id'
                    )
                    ->whereIn('gyms.id', $gymIds)
                    ->where('users.role', 'gym_owner')
                    ->pluck('users.id')
                    ->all();
            }
        }

        elseif ($me->role === 'gym_owner') {
            $gymIds = $this->ownerGymIds($me->id);

            if (!empty($gymIds)) {
                // Existing members
                $ids = array_merge(
                    $ids,
                    DB::table('members')
                        ->whereIn('gym_id', $gymIds)
                        ->pluck('user_id')
                        ->all()
                );

                // Members with membership requests
                $ids = array_merge(
                    $ids,
                    DB::table('payments')
                        ->whereIn('gym_id', $gymIds)
                        ->where('type', 'membership')
                        ->whereIn(
                            'status',
                            ['pending', 'accepted']
                        )
                        ->pluck('user_id')
                        ->all()
                );

                // Members who previously messaged this owner.
                // Allows replies even if the member has
                // not yet paid for membership.
                $previousSenders = DB::table(self::TABLE)
                    ->where('receiver_id', $me->id)
                    ->pluck('sender_id')
                    ->all();

                $ids = array_merge(
                    $ids,
                    $previousSenders
                );
            }
        }

        // Admins can message gym owners. Gym owners can message admins.
        if ($me->role === 'admin') {
            $ids = DB::table('users')
                ->where('role', 'gym_owner')
                ->pluck('id')
                ->all();
        } elseif ($me->role === 'gym_owner') {
            $ids = array_merge(
                $ids,
                DB::table('users')
                    ->where('role', 'admin')
                    ->pluck('id')
                    ->all()
            );
        }

        $ids = collect($ids)
            ->filter(
                fn ($id) => (int) $id > 0 &&
                (int) $id !== (int) $me->id
            )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return [];
        }

        $roles = match ($me->role) {
            'admin' => ['gym_owner'],
            'gym_owner' => ['admin', 'member'],
            'member' => ['gym_owner'],
            default => [],
        };

        return DB::table('users')
            ->whereIn('id', $ids)
            ->whereIn('role', $roles)
            ->pluck('id')
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZE CONVERSATION
    |--------------------------------------------------------------------------
    */
    private function authorizeContact($me, $contactId)
    {
        abort_unless(
            in_array(
                $me->role,
                ['admin', 'member', 'gym_owner']
            ),
            403
        );

        $allowed = $this->allowedContacts($me);

        abort_unless(
            in_array(
                (int) $contactId,
                array_map('intval', $allowed),
                true
            ),
            403,
            'This contact is not connected to your gym.'
        );

        return DB::table('users')
            ->where('id', $contactId)
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | LIST CHAT CONTACTS
    |--------------------------------------------------------------------------
    */
    public function contacts(Request $request)
    {
        $me = $request->user();

        abort_unless(
            in_array(
                $me->role,
                ['admin', 'member', 'gym_owner']
            ),
            403
        );

        $ids = $this->allowedContacts($me);

        if (empty($ids)) {
            return response()->json([
                'contacts' => []
            ]);
        }

        $users = DB::table('users')
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'role']);

        $contacts = $users->map(
            function ($user) use ($me) {
                $latest = DB::table(self::TABLE)
                    ->where(function ($query) use ($me, $user) {
                        $query
                            ->where('sender_id', $me->id)
                            ->where('receiver_id', $user->id);
                    })
                    ->orWhere(function ($query) use ($me, $user) {
                        $query
                            ->where('sender_id', $user->id)
                            ->where('receiver_id', $me->id);
                    })
                    ->orderByDesc('id')
                    ->first();

                $unread = DB::table(self::TABLE)
                    ->where('sender_id', $user->id)
                    ->where('receiver_id', $me->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => match ($user->role) {
                        'gym_owner' => 'owner',
                        'admin' => 'admin',
                        default => 'member',
                    },
                    'last_message' => $latest->body ?? '',
                    'last_message_at' =>
                        $latest->created_at ?? null,
                    'unread' => $unread,
                ];
            }
        );

        return response()->json([
            'contacts' => $contacts
                ->sortByDesc('last_message_at')
                ->values()
                ->all()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD CHAT CONVERSATION
    |--------------------------------------------------------------------------
    */
    public function thread(Request $request, $user)
    {
        $me = $request->user();

        $other = $this->authorizeContact(
            $me,
            $user
        );

        // Mark incoming messages as read
        DB::table(self::TABLE)
            ->where('sender_id', $other->id)
            ->where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        $messages = DB::table(self::TABLE)
            ->where(function ($query) use ($me, $other) {
                $query
                    ->where('sender_id', $me->id)
                    ->where('receiver_id', $other->id);
            })
            ->orWhere(function ($query) use ($me, $other) {
                $query
                    ->where('sender_id', $other->id)
                    ->where('receiver_id', $me->id);
            })
            ->orderByDesc('id')
            ->limit(100)
            ->get([
                'id',
                'sender_id',
                'receiver_id',
                'body',
                'read_at',
                'created_at'
            ])
            ->reverse()
            ->values();

        return response()->json([
            'my_id' => $me->id,
            'contact' => [
                'id' => $other->id,
                'name' => $other->name,
            ],
            'messages' => $messages,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SEND CHAT MESSAGE
    |--------------------------------------------------------------------------
    */
    public function send(Request $request, $user)
    {
        $me = $request->user();

        $other = $this->authorizeContact(
            $me,
            $user
        );

        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'max:2000'
            ],
        ]);

        $body = trim($validated['body']);

        if ($body === '') {
            throw ValidationException::withMessages([
                'body' => 'Please enter a message.'
            ]);
        }

        $id = DB::table(self::TABLE)
            ->insertGetId([
                'sender_id' => $me->id,
                'receiver_id' => $other->id,
                'body' => $body,
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => DB::table(self::TABLE)
                ->where('id', $id)
                ->first(),
        ], 201);
    }
}
