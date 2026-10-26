<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class ProductInventory
{
    /** Called inside an existing database transaction; caller locks the product row. */
    public static function reserve(int $productId, int $quantity): void
    {
        $product = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
        abort_unless($product && $quantity > 0, 422, 'Product not found.');
        $available = (int) $product->stock - (int) $product->reserved_stock;
        abort_if($available < $quantity, 422, 'Insufficient available stock.');
        DB::table('products')->where('id', $productId)->update([
            'reserved_stock' => (int) $product->reserved_stock + $quantity,
            'updated_at' => now(),
        ]);
    }

    /** Releases a reservation; only call once while locking the order first. */
    public static function release(int $productId, int $quantity): void
    {
        $product = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
        abort_unless($product, 422, 'Order product no longer exists.');
        abort_if((int) $product->reserved_stock < $quantity, 409, 'Reserved stock is inconsistent.');
        DB::table('products')->where('id', $productId)->update([
            'reserved_stock' => (int) $product->reserved_stock - $quantity,
            'updated_at' => now(),
        ]);
    }

    /** Permanently moves reserved items out of the physical inventory. */
    public static function fulfill(int $productId, int $quantity): void
    {
        $product = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
        abort_unless($product, 422, 'Order product no longer exists.');
        abort_if((int) $product->reserved_stock < $quantity || (int) $product->stock < $quantity,
            409, 'Stock and reservations are inconsistent.');
        DB::table('products')->where('id', $productId)->update([
            'stock' => (int) $product->stock - $quantity,
            'reserved_stock' => (int) $product->reserved_stock - $quantity,
            'sold' => (int) $product->sold + $quantity,
            'updated_at' => now(),
        ]);
    }
}
