<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('notifications')->insert([
      'id'           => Str::uuid(),
      'type'         => 'App\Notifications\TestNotification',
      'notifiable_type' => 'App\Models\User',
      'notifiable_id'   => 1, // غيّرها لـ id حقيقي موجود في جدول users
      'data'         => json_encode(['message' => 'This is a test notification']),
      'read_at'      => null,
      'created_at'   => Carbon::now(),
      'updated_at'   => Carbon::now(),
    ]);
  }
}
