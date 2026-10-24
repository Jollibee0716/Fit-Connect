<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Membership
{
    public static function state(User $u): string
    {
        if (!Schema::hasTable('payments')) return 'active';   // TODO: remove when payments are real
        $due = DB::table('payments')->where('user_id', $u->id)->where('status', 'accepted')->max('due_date');
        if ($due) return Carbon::parse($due)->gte(today()) ? 'active' : 'overdue';
        return DB::table('payments')->where('user_id', $u->id)->where('status', 'pending')->exists() ? 'pending' : 'none';
    }
}