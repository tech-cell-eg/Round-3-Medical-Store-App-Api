<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    //
<<<<<<< Updated upstream
    User::factory()->count(10)->create([
      'password' => bcrypt('1234'),
       // Set a default password for all users
    ]);
=======


    User::factory()->count(10)->create();
>>>>>>> Stashed changes
  }
}
