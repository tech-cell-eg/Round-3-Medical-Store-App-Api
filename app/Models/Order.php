<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'subtotal',
        'item_discount',
        'coupon_discount',
        'shipping',
        'total',
        'payment_method',
        'status',
        'coupon_code'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'item_discount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedStatusAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}
