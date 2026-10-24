<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'phone'))         $t->string('phone', 30)->nullable();
            if (!Schema::hasColumn('users', 'location'))      $t->string('location', 100)->nullable();
            if (!Schema::hasColumn('users', 'bio'))           $t->text('bio')->nullable();
            if (!Schema::hasColumn('users', 'last_login_at')) $t->timestamp('last_login_at')->nullable();
        });

        Schema::table('gyms', function (Blueprint $t) {
            if (!Schema::hasColumn('gyms', 'address')) $t->string('address')->nullable();
            if (!Schema::hasColumn('gyms', 'phone'))   $t->string('phone', 30)->nullable();
            if (!Schema::hasColumn('gyms', 'hours'))   $t->string('hours', 120)->nullable();
            if (!Schema::hasColumn('gyms', 'about'))   $t->text('about')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['phone', 'location', 'bio', 'last_login_at']));
        Schema::table('gyms',  fn (Blueprint $t) => $t->dropColumn(['address', 'phone', 'hours', 'about']));
    }
};