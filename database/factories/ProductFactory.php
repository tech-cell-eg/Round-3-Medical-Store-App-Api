<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->unique()->words(2, true),
      'description' => $this->faker->sentence(),
      'price' => $this->faker->randomFloat(2, 1, 1000),
      'category_id' => Category::inRandomOrder()->first()->id,
      // Assuming you have 10 categories
      'active_ingred' => $this->faker->word(),
      'manufacture' => $this->faker->company(),
      'expiry_date' => $this->faker->dateTimeBetween('now', '+2 years'),
      'user_id' => User::first()->id,
    ];
  }
}
