<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('specialty')->nullable();
            $t->string('phone', 30)->nullable();
            $t->timestamps();

            $t->index('gym_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};