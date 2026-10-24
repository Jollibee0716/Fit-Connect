<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('gym_id')->index();
            $t->unsignedBigInteger('user_id')->index();
            $t->string('type')->default('membership');      // membership | order
            $t->unsignedBigInteger('product_order_id')->nullable()->index();
            $t->string('plan');                              // plan name, or "Protein x2"
            $t->unsignedInteger('plan_days')->default(0);    // 0 for orders
            $t->decimal('amount', 10, 2);
            $t->string('method')->nullable();
            $t->string('reference')->nullable();
            $t->date('paid_at');
            $t->date('due_date')->nullable();                // memberships only
            $t->string('status')->default('pending');        // pending | accepted | revoked
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('payments'); }
};