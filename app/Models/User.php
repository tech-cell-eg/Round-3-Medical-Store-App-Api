<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< Updated upstream
=======
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;
>>>>>>> Stashed changes


class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
<<<<<<< Updated upstream
  use HasFactory, Notifiable;
=======
  use HasFactory, Notifiable, HasApiTokens;
>>>>>>> Stashed changes

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
<<<<<<< Updated upstream
=======

>>>>>>> Stashed changes
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
<<<<<<< Updated upstream
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
=======
    'remember_token',
  ];


>>>>>>> Stashed changes
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
<<<<<<< Updated upstream
      'password' => 'hashed',
    ];
  }
=======
      'password'          => 'hashed',
    ];
  }


>>>>>>> Stashed changes
  public function products()
  {
    return $this->belongsToMany(Product::class)->withPivot('quantity', 'price')->withTimestamps();
  }
<<<<<<< Updated upstream
=======

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


>>>>>>> Stashed changes
  public function reviews()
  {
    return $this->hasMany(Review::class);
  }
  public function notifiable()
  {
    return $this->morphToMany(Notification::class, 'notifiable');
  }
}
