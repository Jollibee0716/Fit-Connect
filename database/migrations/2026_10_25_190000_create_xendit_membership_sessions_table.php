<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('xendit_membership_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('gym_id')->index();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedInteger('plan_tier');
            $table->string('reference_id', 64)->unique();
            $table->string('payment_session_id', 100)->nullable()->unique();
            $table->text('checkout_url')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PHP');
            $table->string('status', 30)->default('INITIATING');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('xendit_membership_sessions');
    }
};
