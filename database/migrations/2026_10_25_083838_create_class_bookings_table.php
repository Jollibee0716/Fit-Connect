<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('gym_classes')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->date('session_date');
            $table->enum('status', ['booked', 'cancelled', 'attended'])->default('booked');
            $table->timestamps();
            $table->unique(['class_id', 'member_id', 'session_date'], 'class_member_session_unique');
            $table->index(['gym_id', 'class_id', 'session_date', 'status'], 'class_booking_session_index');
        });
    }
    public function down(): void { Schema::dropIfExists('class_bookings'); }
};