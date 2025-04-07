<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function signup(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users',
            'name' => 'required|string|max:255',
        ]);

        try {
            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
            ]);

            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = Carbon::now()->addMinutes(10);

            PhoneVerificationToken::updateOrCreate(
                ['phone' => $request->phone],
                ['token' => $otp, 'expires_at' => $expiresAt]
            );


            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify your phone number.',
                'data' => [
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'otp' => $otp,
                    'expires_in' => 600,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = Carbon::now()->addMinutes(10);

            PhoneVerificationToken::updateOrCreate(
                ['phone' => $request->phone],
                ['token' => $otp, 'expires_at' => $expiresAt]
            );


            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'phone' => $request->phone,
                    'expires_in' => 600,
                    'otp' => $otp,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        try {
            $verification = PhoneVerificationToken::where([
                'phone' => $request->phone,
                'token' => $request->otp,
            ])->first();

            if (!$verification || $verification->expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP',
                ], 422);
            }

            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $user->markPhoneAsVerified();

            $token = $user->createToken('auth-token')->plainTextToken;

            $verification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function resendOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            if ($user->hasVerifiedPhone()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number already verified',
                ], 422);
            }

            return $this->sendOTP($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return $this->sendOTP($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
