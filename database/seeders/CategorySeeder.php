<?php

namespace Database\Seeders;

use App\Models\Category;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
<<<<<<< Updated upstream
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Medicines', 'description' => 'Various types of medicines'],
            ['name' => 'Supplements', 'description' => 'Vitamins and dietary supplements'],
            ['name' => 'Personal Care', 'description' => 'Personal hygiene products'],
            ['name' => 'Medical Devices', 'description' => 'Healthcare equipment and devices'],
            ['name' => 'Baby Care', 'description' => 'Products for infant care'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
=======
  /**
   * Run the database seeds.
   */
  public function run(): void
  {


    Category::factory()->count(10)->create();
  }
>>>>>>> Stashed changes
}
