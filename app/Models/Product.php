<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'quantity',
        'active_ingred',
        'manufacture',
        'expiry_date',
        'user_id'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];


    public function media(): MorphMany
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


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
