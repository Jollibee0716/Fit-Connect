<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\ProductInventory;

class PortalController extends Controller
{
    // List registered gyms
    public function gyms(Request $request)
    {
        $query = DB::table('gyms')
            ->join('gym_owners', 'gym_owners.id', '=', 'gyms.gym_owner_id')
            ->join('users', 'users.id', '=', 'gym_owners.user_id')
            ->where('users.role', 'gym_owner')
            ->select(
                'gyms.id',
                'gyms.name',
                'gyms.address',
                'gyms.phone',
                'gyms.about',
                'gyms.hours'
            );

        if ($request->filled('search')) {
            $term = trim((string) $request->query('search'));

            $query->where(function ($q) use ($term) {
                $q->where('gyms.name', 'like', '%' . $term . '%')
                    ->orWhere('gyms.address', 'like', '%' . $term . '%')
                    ->orWhere('gyms.about', 'like', '%' . $term . '%');
            });
        }

        return response()->json([
            'gyms' => $query
                ->orderBy('gyms.name')
                ->limit(100)
                ->get()
        ]);
    }

    // Save selected gym to member session
    public function selectGym(Request $request)
    {
        $data = $request->validate([
            'gym_id' => 'required|integer|exists:gyms,id'
        ]);

        $gymId = (int) $data['gym_id'];

        $request->session()->put(
            'fitconnect_selected_gym_id',
            $gymId
        );

        return response()->json([
            'ok' => true,
            'gym_id' => $gymId
        ]);
    }

    // Selected gym and active membership
    public function overview(Request $request)
    {
        $gymId = (int) $request->session()
            ->get('fitconnect_selected_gym_id', 0);

        $gym = $gymId
            ? DB::table('gyms')->where('id', $gymId)->first()
            : null;

        $payment = null;

        if ($gym) {
            $payment = DB::table('payments')
                ->where('gym_id', $gymId)
                ->where('user_id', $request->user()->id)
                ->where('type', 'membership')
                ->where('status', 'accepted')
                ->whereDate('due_date', '>=', today())
                ->orderByDesc('due_date')
                ->first();
        }

        return response()->json([
            'gym' => $gym,
            'membership' => $payment
                ? [
                    'active' => true,
                    'plan' => $payment->plan,
                    'due_date' => $payment->due_date
                ]
                : ['active' => false]
        ]);
    }

    // Membership plans and products of selected gym
    public function catalog(Request $request)
    {
        $gymId = (int) $request->session()
            ->get('fitconnect_selected_gym_id', 0);

        abort_unless(
            $gymId &&
            DB::table('gyms')->where('id', $gymId)->exists(),
            422,
            'Select a gym first.'
        );

        $plans = DB::table('membership_plans')
            ->where('gym_id', $gymId)
            ->where('is_active', 1)
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'days', 'tier']);

        $products = DB::table('products')
            ->where('gym_id', $gymId)
            ->where('is_active', 1)
            ->whereRaw('stock > reserved_stock')
            ->orderBy('name')
            ->get([
                'id', 'name', 'description',
                'price', 'stock', 'reserved_stock'
            ])
            ->map(function ($p) {
                $p->stock = max(0, (int) $p->stock - (int) $p->reserved_stock);
                unset($p->reserved_stock);
                return $p;
            });

        return response()->json([
            'plans' => $plans,
            'products' => $products
        ]);
    }

    // Member's product orders
    public function orders(Request $request)
    {
        $orders = DB::table('product_orders')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    // Member's membership payment history
    public function payments(Request $request)
    {
        $payments = DB::table('payments')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json([
            'payments' => $payments
        ]);
    }

    // Place a product order
    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:10',
            'method' => 'required|in:GCash,Maya,Cash,Xendit',
            'reference' => 'nullable|string|max:60',
            'fulfillment_method' => 'required|in:pickup,delivery',
            'delivery_name' =>
                'required_if:fulfillment_method,delivery|nullable|string|max:120',
            'delivery_phone' =>
                'required_if:fulfillment_method,delivery|nullable|string|max:30',
            'delivery_address' =>
                'required_if:fulfillment_method,delivery|nullable|string|max:500',
            'delivery_notes' => 'nullable|string|max:500'
        ]);

        $gymId = (int) $request->session()
            ->get('fitconnect_selected_gym_id', 0);

        abort_unless($gymId, 422, 'Select a gym first.');

        $orderId = DB::transaction(function () use (
            $data,
            $gymId,
            $request
        ) {
            $product = DB::table('products')
                ->where('id', $data['product_id'])
                ->where('gym_id', $gymId)
                ->where('is_active', 1)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $product,
                404,
                'Product not found in selected gym.'
            );

            ProductInventory::reserve((int) $product->id, (int) $data['quantity']);

            return DB::table('product_orders')
                ->insertGetId([
                    'gym_id' => $gymId,
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $data['quantity'],
                    'total' => $product->price * $data['quantity'],
                    'method' => $data['method'],
                    'reference' => $data['reference'] ?? null,
                    'status' => 'pending',
                    'fulfillment_method' =>
                        $data['fulfillment_method'],
                    'fulfillment_status' => 'awaiting_preparation',
                    'delivery_name' =>
                        $data['fulfillment_method'] === 'delivery'
                            ? $data['delivery_name'] : null,
                    'delivery_phone' =>
                        $data['fulfillment_method'] === 'delivery'
                            ? $data['delivery_phone'] : null,
                    'delivery_address' =>
                        $data['fulfillment_method'] === 'delivery'
                            ? $data['delivery_address'] : null,
                    'delivery_notes' =>
                        $data['fulfillment_method'] === 'delivery'
                            ? ($data['delivery_notes'] ?? null) : null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        });

        return response()->json([
            'ok' => true,
            'order_id' => $orderId
        ], 201);
    }
}