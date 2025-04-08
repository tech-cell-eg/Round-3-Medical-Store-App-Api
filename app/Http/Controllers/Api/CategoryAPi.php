<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\RepositoryInterface\CategoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryApi extends Controller
{
  /**
   * Display a listing of the resource.
   */
  protected $categoryRepository;
  public function __Construct(CategoryInterface $categoryRepository)
  {
    $this->categoryRepository = $categoryRepository;
  }

  public function index()
  {
    //
    $categories = $this->categoryRepository->index();
    return CategoryResource::collection($categories);
  }

  public function topCategory()
  {
    $categories = $this->categoryRepository->topCategory();
    return CategoryResource::collection($categories);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
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
   * Show the form for editing the specified resource.
   */
  public function edit(Product $product)
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
