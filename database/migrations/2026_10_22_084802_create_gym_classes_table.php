<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gym_classes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $t->foreignId('trainer_id')->constrained('trainers');   // your existing table
            $t->string('name');
            $t->json('days');                                       // [1,3,5] = Mon, Wed, Fri
            $t->time('start_time');                                 // 06:00:00
            $t->unsignedSmallInteger('duration_min')->default(60);
            $t->unsignedSmallInteger('capacity')->default(20);
            $t->timestamps();

            $t->index(['gym_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_classes');
    }
};