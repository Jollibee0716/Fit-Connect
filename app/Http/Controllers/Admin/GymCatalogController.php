<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class GymCatalogController extends Controller
{
    public function index(Request $request, $gym)
    {
        // Allow Admin only.
        abort_unless(
            $request->user() &&
            strtolower((string) $request->user()->role) === 'admin',
            403,
            'Admin access required.'
        );

        // Verify that the gyms table exists.
        abort_unless(
            Schema::hasTable('gyms'),
            500,
            'Gyms table does not exist.'
        );

        // Find the selected gym.
        $gymRecord = DB::table('gyms')
            ->where('id', $gym)
            ->first();

        abort_unless(
            $gymRecord,
            404,
            'Gym not found.'
        );

        $products = [];
        $coaches = [];

        // PRODUCTS
        if (Schema::hasTable('products')) {
            $query = DB::table('products');

            if (Schema::hasColumn('products', 'gym_id')) {

                // Products directly belong to a gym.
                $query->where('gym_id', $gymRecord->id);

            } elseif (
                Schema::hasColumn('products', 'gym_owner_id')
            ) {

                // Products belong to a gym owner.
                $query->where(
                    'gym_owner_id',
                    $gymRecord->gym_owner_id
                );

            } elseif (
                Schema::hasColumn('products', 'user_id') &&
                Schema::hasTable('gym_owners') &&
                Schema::hasColumn('gym_owners', 'user_id')
            ) {

                // Products reference a user account.
                $ownerUserId = DB::table('gym_owners')
                    ->where('id', $gymRecord->gym_owner_id)
                    ->value('user_id');

                abort_unless(
                    $ownerUserId,
                    409,
                    'Gym owner user account not found.'
                );

                $query->where('user_id', $ownerUserId);

            } else {

                // Avoid displaying products from other gyms.
                return response()->json([
                    'message' =>
                        'Product ownership relationship is not configured.',
                    'products' => [],
                    'coaches' => [],
                ], 409);
            }

            $products = $query->get()->map(function ($p) {
                $image = $p->image ?? null;

                return [
                    'id' => $p->id,
                    'name' => $p->name ?? 'Unnamed Product',
                    'price' => (float) ($p->price ?? 0),
                    'stock' => (int) ($p->stock ?? 0),
                    'image_url' => $image
                        ? Storage::disk('public')->url($image)
                        : null,
                ];
            })->values()->all();
        }

        // COACHES / TRAINERS: read only the selected gym's trainers.
        if (Schema::hasTable('trainers')) {
            $coaches = DB::table('trainers')
                ->where('gym_id', $gymRecord->id)
                ->orderBy('name')
                ->get(['id', 'name', 'specialty', 'phone'])
                ->map(fn ($trainer) => [
                    'id' => (int) $trainer->id,
                    'name' => $trainer->name,
                    'specialty' => $trainer->specialty,
                    'phone' => $trainer->phone,
                ])
                ->values()
                ->all();
        }

        return response()->json([
            'products' => $products,
            'coaches' => $coaches,
        ]);
    }
}
