<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerProductController extends Controller
{
    private function gymId()
    {
        $gym = DB::table('gyms')
            ->join('gym_owners', 'gym_owners.id', '=', 'gyms.gym_owner_id')
            ->where('gym_owners.user_id', auth()->id())
            ->select('gyms.id')
            ->first();

        abort_unless($gym, 403, 'No gym found for this account.');
        return (int) $gym->id;
    }

    private function rules()
    {
        return [
            'name'        => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'is_active'   => 'boolean',
        ];
    }

    public function index()
    {
        $rows = Product::where('gym_id', $this->gymId())->latest()->get()->map(function ($product) {
            $product->physical_stock = (int) $product->stock;
            $product->stock = max(0, (int) $product->stock - (int) $product->reserved_stock);
            return $product;
        });
        return ['products' => $rows];
    }

    public function store(Request $r)
    {
        $p = Product::create($r->validate($this->rules()) + ['gym_id' => $this->gymId()]);
        Audit::log('products', $p->id, 'created', $p->gym_id,
            "Added product {$p->name} (₱{$p->price}, stock {$p->stock})");
        return ['product' => $p];
    }

    public function update(Request $r, Product $product)
    {
        abort_unless((int) $product->gym_id === $this->gymId(), 403);
        $data = $r->validate($this->rules());
        return DB::transaction(function () use ($product, $data) {
            $p = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $old = $p->only(['name', 'price', 'stock', 'reserved_stock', 'is_active']);
            // Owner edits AVAILABLE stock; physical stock = available + reservations.
            $data['stock'] = (int) $data['stock'] + (int) $p->reserved_stock;
            $p->update($data);
            Audit::log('products', $p->id, 'updated', $p->gym_id,
                "Edited product {$p->name}", $old,
                $p->only(['name', 'price', 'stock', 'reserved_stock', 'is_active']));
            $p->physical_stock = (int) $p->stock;
            $p->stock = (int) $p->stock - (int) $p->reserved_stock;
            return ['product' => $p];
        });
    }

    public function destroy(Product $product)
    {
        abort_unless((int) $product->gym_id === $this->gymId(), 403);
        return DB::transaction(function () use ($product) {
            $p = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            abort_if((int) $p->reserved_stock > 0, 422,
                'This product has active reservations; cancel or fulfill them first.');
            abort_if(ProductOrder::where('product_id', $p->id)->exists(), 422,
                'Product has order history. Deactivate it instead of deleting.');
            Audit::log('products', $p->id, 'deleted', $p->gym_id,
                "Deleted product {$p->name}", $p->only(['name', 'price', 'stock']));
            $p->delete();
            return ['ok' => true];
        });
    }

    // Walk-in sale at the front desk (order is completed immediately)
    public function sell(Product $product)
    {
        abort_unless((int) $product->gym_id === $this->gymId(), 403);

        return DB::transaction(function () use ($product) {
            $p = Product::lockForUpdate()->find($product->id);
            if ((int) $p->stock - (int) $p->reserved_stock < 1) {
                abort(422, 'Out of stock');
            }

            $p->decrement('stock');
            $p->increment('sold');

            $o = ProductOrder::create([
                'gym_id'       => $p->gym_id,
                'product_id'   => $p->id,
                'product_name' => $p->name,
                'unit_price'   => $p->price,
                'quantity'     => 1,
                'total'        => $p->price,
                'method'       => 'Cash',
                'status'       => 'completed',
                'paid_at'      => now(),
                'completed_at' => now(),
                'fulfillment_method' => 'pickup',
                'fulfillment_status' => 'picked_up',
                'picked_up_at' => now(),
            ]);

            Audit::log('product_orders', $o->id, 'created', $p->gym_id,
                "Walk-in sale: 1 × {$p->name} for ₱{$p->price}");

            return ['order' => $o, 'product' => $p->fresh()];
        });
    }
}