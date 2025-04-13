<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  //
  protected $fillable = [
    'user_id',
    'message',
    'is_read',
  ];
  protected $table = 'notifications';

  public function notifiable()
  {
    return $this->morphedByMany(User::class, 'notifiable');
  }
}
