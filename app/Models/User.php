<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'remember_token',
  ];


  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password'          => 'hashed',
    ];
  }


  public function products()
  {
    return $this->belongsToMany(Product::class)->withPivot('quantity', 'price')->withTimestamps();
  }

  public function cartItems()
  {
    return $this->hasMany(CartItem::class);
  }

  public function addresses()
  {
    return $this->hasMany(UserAddress::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  public function getDefaultAddressAttribute()
  {
    return $this->addresses()->where('is_default', true)->first();
  }


  public function reviews()
  {
    return $this->hasMany(Review::class);
  }
  public function notifiable()
  {
    return $this->morphToMany(Notification::class, 'notifiable');
  }
}
