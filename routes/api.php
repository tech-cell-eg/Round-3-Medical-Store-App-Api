<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {

    Route::post('/send-otp', [AuthController::class, 'sendOTP']);

    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);

    Route::post('/signup', [AuthController::class, 'signup']);

    Route::post('/resend-otp', [AuthController::class, 'resendOTP']);

});
