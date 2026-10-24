<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('member','coach','gym_owner','admin') NOT NULL DEFAULT 'member'");

        DB::table('users')->where('role', 'coach')->update(['role' => 'gym_owner']);

        DB::statement("ALTER TABLE users MODIFY role ENUM('member','gym_owner','admin') NOT NULL DEFAULT 'member'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('member','coach','gym_owner','admin') NOT NULL DEFAULT 'member'");

        DB::table('users')->where('role', 'gym_owner')->update(['role' => 'coach']);

        DB::statement("ALTER TABLE users MODIFY role ENUM('member','coach','admin') NOT NULL DEFAULT 'member'");
    }
};