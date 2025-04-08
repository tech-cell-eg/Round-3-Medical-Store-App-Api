<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => \App\Models\User::factory(),
            'product_id' => \App\Models\Product::factory(),
            'rating'     => $this->faker->numberBetween(1, 5),
            'comment'    => $this->faker->sentence(10),
            'created_at' => now(),
            'updated_at' => now(),
            // 'deleted_at' => null,
            
            //
        ];
    }
}
