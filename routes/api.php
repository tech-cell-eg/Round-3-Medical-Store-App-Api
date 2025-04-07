<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApi;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');
Route::get('/categories', [CategoryApi::class, 'index']);
