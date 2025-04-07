<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  /** @use HasFactory<\Database\Factories\ProductsFactory> */
  use HasFactory;
  protected $fillable = [
    'name',
    'description',
    'price',
    'category_id',
    'active_ingred',
    'manufacture',
    'expiry_date',
    'user_id',
    'quantity',

  ];
  public function category()
  {
    return $this->belongsTo(Category::class);
  }
  public function user()
  {
    return $this->belongsToMany(User::class)->withPivot('quantity', 'price')->withTimestamps();
  }
}
