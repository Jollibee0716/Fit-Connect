<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberProductController extends Controller
{
    // The gym this member joined. Change if your column or relation is named differently
    private function gymId()
    {
        return auth()->user()->gym_id;
    }

    // What the gym sells (only active products that are in stock)
    public function index()
    {
        return [
            'products' => Product::where('gym_id', $this->gymId())
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->get(['id', 'name', 'description', 'price', 'stock']),
        ];
    }

    // Member places an order
    public function order(Request $r)
    {
        $d = $r->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|min:1|max:10',
            'method'     => 'required|in:GCash,Maya,Cash',
            'reference'  => 'nullable|string|max:60',
        ]);

        return DB::transaction(function () use ($d) {
            // Lock the row so two members can't buy the last item at once
            $p = Product::where('gym_id', $this->gymId())
                ->where('is_active', true)
                ->lockForUpdate()
                ->findOrFail($d['product_id']);

            if ($p->stock < $d['quantity']) {
                abort(422, 'Not enough stock.');
            }

            $p->decrement('stock', $d['quantity']);
            $p->increment('sold', $d['quantity']);

            $o = ProductOrder::create([
                'gym_id'       => $p->gym_id,
                'user_id'      => auth()->id(),
                'product_id'   => $p->id,
                'product_name' => $p->name,
                'unit_price'   => $p->price,
                'quantity'     => $d['quantity'],
                'total'        => $p->price * $d['quantity'],
                'method'       => $d['method'],
                'reference'    => $d['reference'] ?? null,
                'status'       => 'pending',
            ]);

            return ['order' => $o, 'receipt' => $o->receipt];
        });
    }

    // Member's own purchase history and status
    public function mine()
    {
        return [
            'orders' => ProductOrder::where('user_id', auth()->id())
                ->latest()
                ->get()
                ->map(fn ($o) => [
                    'receipt'    => $o->receipt,
                    'product'    => $o->product_name,
                    'quantity'   => $o->quantity,
                    'total'      => $o->total,
                    'status'     => $o->status,
                    'created_at' => $o->created_at,
                ]),
        ];
    }
}