<?php
namespace App\Repositories;
use App\RepositoryInterface\CategoryInterface;
use App\Models\Product;
use App\RepositoryInterface\ProductInterface;
class ProductRepository implements ProductInterface
{
  public function index()
  {
    return Product::all();
  }

  public function getProductById($id)
  {
    return Product::find($id);
  }

  public function createProduct($data)
  {
    return Product::create($data);
  }

  public function updateProduct($id, $data)
  {
    $product = Product::find($id);
    if ($product) {
      $product->update($data);
      return $product;
    }
    return null;
  }
  

  public function deleteProduct($id)
  {
    $product = Product::find($id);
    if ($product) {
      $product->delete();
      return true;
    }
    return false;
  }
  public function getProductsByCategory($categoryId)
  {
    return Product::where('category_id', $categoryId)->get();
  }
  public function getProductDetails($productId)
  {
    return Product::with(['category', 'reviews.user'])->find($productId);
  }
}