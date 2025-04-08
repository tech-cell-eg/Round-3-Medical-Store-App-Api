<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'active_ingredient',
        'manufacturer',
        'expiry_date'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFeaturedImageAttribute()
    {
        return $this->media()->first();
    }

    public function getCurrentPriceAttribute()
    {
        return $this->price;
    }
}
