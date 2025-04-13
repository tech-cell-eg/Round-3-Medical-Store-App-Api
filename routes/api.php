<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApi;
use App\Http\Controllers\Api\ProductApi;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');
Route::get('categories/top', [CategoryApi::class, 'topCategory']);
Route::resource('categories', CategoryApi::class);
Route::get('categories/{categoryId}/products', [ProductApi::class, 'getProductsByCategory']);
Route::get('products/{productId}', [ProductApi::class, 'getProductDetails']);
Route::resource('products', ProductApi::class);
