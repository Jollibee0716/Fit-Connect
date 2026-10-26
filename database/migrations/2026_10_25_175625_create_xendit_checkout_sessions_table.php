<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xendit_checkout_sessions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');

            $table->string('reference_id', 100)->unique();

            $table->string('payment_session_id', 100)
                ->nullable()->unique();

            $table->text('checkout_url')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('PHP');

            $table->string('status', 30)
                ->default('initiating');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xendit_checkout_sessions');
    }
};
