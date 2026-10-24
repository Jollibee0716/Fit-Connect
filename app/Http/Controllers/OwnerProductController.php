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
        return ['products' => Product::where('gym_id', $this->gymId())->latest()->get()];
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
        $old = $product->only(['name', 'price', 'stock', 'is_active']);
        $product->update($r->validate($this->rules()));
        Audit::log('products', $product->id, 'updated', $product->gym_id,
            "Edited product {$product->name}", $old,
            $product->only(['name', 'price', 'stock', 'is_active']));
        return ['product' => $product];
    }

    public function destroy(Product $product)
    {
        abort_unless((int) $product->gym_id === $this->gymId(), 403);
        Audit::log('products', $product->id, 'deleted', $product->gym_id,
            "Deleted product {$product->name}", $product->only(['name', 'price', 'stock']));
        $product->delete();
        return ['ok' => true];
    }

    // Walk-in sale at the front desk (order is completed immediately)
    public function sell(Product $product)
    {
        abort_unless((int) $product->gym_id === $this->gymId(), 403);

        return DB::transaction(function () use ($product) {
            $p = Product::lockForUpdate()->find($product->id);
            if ($p->stock < 1) {
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
            ]);

            Audit::log('product_orders', $o->id, 'created', $p->gym_id,
                "Walk-in sale: 1 × {$p->name} for ₱{$p->price}");

            return ['order' => $o, 'product' => $p->fresh()];
        });
    }
}