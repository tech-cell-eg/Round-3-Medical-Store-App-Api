<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $products = Product::all();

        foreach ($products as $product) {
            $reviewCount = rand(0, 5);

            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'user_id' => $users->random()->id,
                    'product_id' => $product->id,
                    'rating' => rand(1, 5),
                    'comment' => $this->generateRandomComment()
                ]);
            }
        }
    }

    private function generateRandomComment()
    {
        $comments = [
            'Great product, works as expected!',
            'Very effective, would recommend.',
            'Average quality, could be better.',
            'Not satisfied with the results.',
            'Excellent! Exceeded my expectations.',
            'Fast delivery and good packaging.',
            'Did not work for me as advertised.',
            'Good value for money.',
            'Will buy again for sure!',
            'Not what I expected.'
        ];

        return $comments[array_rand($comments)];
    }
}
