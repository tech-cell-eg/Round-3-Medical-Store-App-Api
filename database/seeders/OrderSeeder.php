<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $orderCount = rand(0, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $address = $user->addresses->random();
                $products = Product::inRandomOrder()->limit(rand(1, 5))->get();

                $subtotal = $products->sum(function($product) {
                    return $product->price * rand(1, 3);
                });

                $shipping = rand(5, 20);
                $total = $subtotal + $shipping;

                $order = Order::create([
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                    'payment_method' => ['credit_card', 'paypal', 'bank_transfer'][rand(0, 2)],
                    'status' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled'][rand(0, 4)],
                ]);

                foreach ($products as $product) {
                    $quantity = rand(1, 3);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'discount' => 0
                    ]);
                }
            }
        }
    }
}
