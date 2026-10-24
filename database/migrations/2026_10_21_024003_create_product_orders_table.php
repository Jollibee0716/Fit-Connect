<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_orders', function (Blueprint $t) {
    $t->id();
    $t->foreignId('gym_id')->constrained()->cascadeOnDelete();
    $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $t->foreignId('product_id')->constrained()->cascadeOnDelete();
    $t->string('product_name');
    $t->decimal('unit_price', 10, 2);
    $t->unsignedInteger('quantity')->default(1);
    $t->decimal('total', 10, 2);
    $t->string('method')->nullable();
    $t->string('reference')->nullable();
    $t->enum('status', ['pending','paid','completed','cancelled'])->default('pending');
    $t->timestamp('paid_at')->nullable();
    $t->timestamp('completed_at')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
