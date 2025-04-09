<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryApi;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApi;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [AuthController::class, 'sendOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/resend-otp', [AuthController::class, 'resendOTP']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::put('/update/{cartItem}', [CartController::class, 'updateCartItem']);
        Route::delete('/remove/{cartItem}', [CartController::class, 'removeFromCart']);
        Route::delete('/clear', [CartController::class, 'clearCart']);
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::put('/{address}', [AddressController::class, 'update']);
        Route::delete('/{address}', [AddressController::class, 'destroy']);
        Route::post('/{address}/set-default', [AddressController::class, 'setDefault']);
    });

    Route::prefix('checkout')->group(function () {
        Route::get('/summary', [CheckoutController::class, 'summary']);
        Route::post('/place-order', [CheckoutController::class, 'placeOrder']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('categories/top', [CategoryApi::class, 'topCategory']);
Route::resource('categories', CategoryApi::class);
Route::get('category/{categoryId}/products', [ProductApi::class, 'getProductsByCategory']);
Route::get('product/{productId}/details', [ProductApi::class, 'getProductDetails']);
Route::resource('products', ProductApi::class);
