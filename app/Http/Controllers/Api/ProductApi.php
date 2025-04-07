<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\RepositoryInterface\ProductInterface;

class ProductApi extends Controller
{
  /**
   * Display a listing of the resource.
   */
  protected $productRepository;
  public function __Construct(ProductInterface $productRepository)
  {
    $this->productRepository = $productRepository;
  }
  public function index()
  {
    $products = $this->productRepository->index();
    
    return ProductResource::collection($products);

    //


  }
  Public function getProductsByCategory($categoryId)
  {
    $products = $this->productRepository->getProductsByCategory($categoryId);
    return ProductResource::collection($products);
  }
  

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Product $product)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Product $product)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Product $product)
  {
    //
  }
}
