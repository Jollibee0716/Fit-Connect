<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    protected $fillable = [
        // Existing order information
        'gym_id',
        'user_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'total',
        'method',
        'reference',
        'status',
        'paid_at',
        'completed_at',

        // Pickup and Delivery
        'fulfillment_method',
        'fulfillment_status',
        'delivery_name',
        'delivery_phone',
        'delivery_address',
        'delivery_notes',

        // Fulfillment timestamps
        'ready_for_pickup_at',
        'picked_up_at',
        'out_for_delivery_at',
        'delivered_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'ready_for_pickup_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Member who placed the order
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Product being ordered
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Automatically generated receipt number
    public function getReceiptAttribute()
    {
        return 'FC-' . str_pad(
            $this->id,
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}
