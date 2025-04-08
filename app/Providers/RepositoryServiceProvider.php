<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\RepositoryInterface\CategoryInterface;
use App\Repositories\CategoryRepository;
use App\RepositoryInterface\ProductInterface;
use App\Repositories\ProductRepository;

class RepositoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
    $this->app->bind(CategoryInterface::class, CategoryRepository::class);
    $this->app->bind(ProductInterface::class, ProductRepository::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
  }
}
