<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Member\PortalController;

class MemberProductController extends Controller
{
    // Preserve the older endpoints, but use one authoritative purchase flow.
    public function index(Request $request)
    {
        $gymId = (int) $request->session()->get('fitconnect_selected_gym_id', 0);
        abort_unless($gymId && DB::table('gyms')->where('id', $gymId)->exists(),
            422, 'Select a gym first.');
        $products = DB::table('products')->where('gym_id', $gymId)
            ->where('is_active', 1)->whereRaw('stock > reserved_stock')
            ->get(['id', 'name', 'description', 'price', 'stock', 'reserved_stock'])
            ->map(function ($p) {
                $p->stock = (int) $p->stock - (int) $p->reserved_stock;
                unset($p->reserved_stock);
                return $p;
            });
        return ['products' => $products];
    }

    public function order(Request $request)
    {
        return app(PortalController::class)->placeOrder($request);
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
                    'fulfillment_method' => $o->fulfillment_method ?? 'pickup',
                    'fulfillment_status' => $o->fulfillment_status,
                    'created_at' => $o->created_at,
                ]),
        ];
    }
}