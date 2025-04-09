<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory()->count(10)->create();
        }

        foreach ($users as $user) {
            UserAddress::factory()
                ->count(rand(1, 3))
                ->create([
                    'user_id' => $user->id,
                    'is_default' => false
                ]);

            UserAddress::factory()
                ->create([
                    'user_id' => $user->id,
                    'is_default' => true,
                    'label' => 'Primary Address'
                ]);
        }


    }
}
