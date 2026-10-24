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
        Schema::create('products', function (Blueprint $t) {
    $t->id();
    $t->foreignId('gym_id')->constrained()->cascadeOnDelete();
    $t->string('name');
    $t->text('description')->nullable();
    $t->decimal('price', 10, 2);
    $t->unsignedInteger('stock')->default(0);
    $t->unsignedInteger('sold')->default(0);
    $t->boolean('is_active')->default(true);
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
