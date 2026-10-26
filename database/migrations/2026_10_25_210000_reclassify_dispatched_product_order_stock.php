<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Before this update, shipping an order immediately deducted stock.
        // Restore the physical quantity as a reservation for orders in transit.
        // Available stock (stock - reserved_stock) therefore remains unchanged.
        DB::table('product_orders')
            ->where('status', 'paid')
            ->where('fulfillment_method', 'delivery')
            ->where('fulfillment_status', 'out_for_delivery')
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    DB::transaction(function () use ($order) {
                        $product = DB::table('products')
                            ->where('id', $order->product_id)
                            ->lockForUpdate()->first();
                        if (!$product) {
                            throw new \RuntimeException("Missing product for order {$order->id}");
                        }
                        $qty = (int) $order->quantity;
                        if ($qty < 1 || (int) $product->sold < $qty) {
                            throw new \RuntimeException("Inventory inconsistent for order {$order->id}");
                        }
                        DB::table('products')->where('id', $product->id)->update([
                            'stock' => (int) $product->stock + $qty,
                            'reserved_stock' => (int) $product->reserved_stock + $qty,
                            'sold' => (int) $product->sold - $qty,
                            'updated_at' => now(),
                        ]);
                    });
                }
            });
    }

    public function down(): void
    {
        // Deliberately irreversible: after live orders progress, reversing this
        // conversion risks applying inventory deductions twice.
        throw new \RuntimeException('Restore a database backup to revert this inventory conversion.');
    }
};
