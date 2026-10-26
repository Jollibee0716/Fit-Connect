
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->string('fulfillment_method', 20)
                ->default('pickup');

            $table->string('fulfillment_status', 30)
                ->nullable();

            $table->string('delivery_name', 120)->nullable();
            $table->string('delivery_phone', 30)->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('delivery_notes')->nullable();

            $table->timestamp('ready_for_pickup_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn([
                'fulfillment_method',
                'fulfillment_status',
                'delivery_name',
                'delivery_phone',
                'delivery_address',
                'delivery_notes',
                'ready_for_pickup_at',
                'picked_up_at',
                'out_for_delivery_at',
                'delivered_at',
            ]);
        });
    }
};
