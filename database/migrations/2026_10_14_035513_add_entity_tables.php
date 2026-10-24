<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // New columns on users (only added if missing)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'contact_number')) {
                $table->string('contact_number', 30)->nullable();
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
        });

        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_number', 30)->nullable();
                $table->decimal('height_cm', 5, 2)->nullable();
                $table->decimal('weight_kg', 5, 2)->nullable();
                $table->string('fitness_goal')->nullable();
                $table->date('join_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('trainers')) {
            Schema::create('trainers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('specialty')->nullable();
                $table->string('certification')->nullable();
                $table->unsignedInteger('years_experience')->default(0);
                $table->text('bio')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
        Schema::dropIfExists('members');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'contact_number',
                'date_of_birth',
                'address',
            ]);
        });
    }
};