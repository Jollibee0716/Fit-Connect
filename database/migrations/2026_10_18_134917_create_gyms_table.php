<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_owner_id')->unique()->constrained('gym_owners')->cascadeOnDelete();
            $table->string('name');
            $table->string('address');
            $table->string('phone', 30);
            $table->timestamps();
        });

        // Turn gyms already saved on owners into real gym rows
        DB::statement("
            INSERT INTO gyms (gym_owner_id, name, address, phone, created_at, updated_at)
            SELECT id, gym_name, gym_address, phone_number, NOW(), NOW() FROM gym_owners
        ");

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('gym_id')->nullable()->after('user_id')
                ->constrained('gyms')->nullOnDelete();
        });

        // If members were linked to an owner before, carry that over
        if (Schema::hasColumn('members', 'gym_owner_id')) {
            DB::statement("
                UPDATE members m
                JOIN gyms g ON g.gym_owner_id = m.gym_owner_id
                SET m.gym_id = g.id
            ");

            Schema::table('members', function (Blueprint $table) {
                $table->dropConstrainedForeignId('gym_owner_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gym_id');
        });

        Schema::dropIfExists('gyms');
    }
};