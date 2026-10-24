<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $t->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $t->string('member_name');                          // snapshot, so history survives renames
            $t->foreignId('class_id')->nullable()->constrained('gym_classes')->nullOnDelete();
            $t->string('class_name')->nullable();               // null = gym floor visit
            $t->string('membership_state', 20)->default('active');  // active | overdue | pending | none
            $t->boolean('override')->default(false);
            $t->string('override_reason', 200)->nullable();
            $t->date('session_date');
            $t->timestamp('checked_in_at')->useCurrent();
            $t->foreignId('code_id')->nullable()->constrained('attendance_codes')->nullOnDelete();
            $t->string('method', 20)->default('owner');         // owner | member_code
            $t->timestamps();

            $t->unique(['member_id', 'class_id', 'session_date'], 'att_member_class_day_unique');
            $t->index(['gym_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};