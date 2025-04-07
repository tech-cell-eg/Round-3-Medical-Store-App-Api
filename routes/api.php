<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApi;
use App\Http\Controllers\Api\ProductApi;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');
Route::resource('categories', CategoryApi::class);
Route::resource('products', ProductApi::class);
Route::get('category/{categoryId}/products', [ProductApi::class, 'getProductsByCategory']);
