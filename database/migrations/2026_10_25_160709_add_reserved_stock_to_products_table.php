<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('reserved_stock')->default(0)->after('stock');
        });

        // Legacy code already subtracted stock and counted sold at order placement.
        // Reclassify still-active, unfulfilled orders as reservations exactly once.
        // Requires the prior pickup/delivery status migration to be applied first.
        DB::table('products')->orderBy('id')->chunkById(100, function ($products) {
            foreach ($products as $product) {
                $qty = (int) DB::table('product_orders')
                    ->where('product_id', $product->id)
                    ->whereIn('status', ['pending', 'paid'])
                    ->where(function ($q) {
                        $q->whereNull('fulfillment_status')
                          ->orWhereNotIn('fulfillment_status', [
                              'picked_up', 'out_for_delivery', 'delivered', 'cancelled'
                          ]);
                    })
                    ->sum('quantity');
                if ($qty === 0) continue;
                DB::table('products')->where('id', $product->id)->update([
                    'stock' => (int) $product->stock + $qty,
                    'reserved_stock' => $qty,
                    'sold' => max(0, (int) $product->sold - $qty),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Revert to the old available-stock meaning before dropping reservations.
        DB::table('products')->where('reserved_stock', '>', 0)
            ->orderBy('id')->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    DB::table('products')->where('id', $product->id)->update([
                        'stock' => (int) $product->stock - (int) $product->reserved_stock,
                        'sold' => (int) $product->sold + (int) $product->reserved_stock,
                    ]);
                }
            });
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('reserved_stock'));
    }
};
