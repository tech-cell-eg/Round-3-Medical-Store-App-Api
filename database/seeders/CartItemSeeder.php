<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $productCount = rand(0, 5);
            $products = Product::inRandomOrder()->limit($productCount)->get();

            foreach ($products as $product) {
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price
                ]);
            }
        }
    }
}
