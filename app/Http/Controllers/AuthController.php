<?php
namespace App\Http\Controllers;

use App\Models\PhoneVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function sendOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users',
        ]);

        try {
            $otp       = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = Carbon::now()->addMinutes(10);

            PhoneVerificationToken::updateOrCreate(
                ['phone' => $request->phone],
                ['token' => $otp, 'expires_at' => $expiresAt]
            );


            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data'    => [
                    'phone'      => $request->phone,
                    'expires_in' => 600,
                    'otp'        => $otp,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string|size:6',
        ]);

        try {
            $verification = PhoneVerificationToken::where([
                'phone' => $request->phone,
                'token' => $request->otp,
            ])->first();

            if (! $verification || $verification->expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP',
                ], 422);
            }

            $user = User::where('phone', $request->phone)->first();

            if ($user) {
                $user->markPhoneAsVerified();

                $token = $user->createToken('phone-auth')->plainTextToken;

                $verification->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Phone number verified successfully',
                    'data'    => [
                        'verified'     => true,
                        'user_exists'  => true,
                        'access_token' => $token,
                        'user'         => $user,
                    ],
                ]);
            }

            $verification->delete();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'data'    => [
                    'verified'    => true,
                    'user_exists' => false,
                    'phone'       => $request->phone,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed',
            ], 500);
        }
    }

    public function signup(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users',
            'name'  => 'required|string|max:255',
        ]);

        try {
            $user = User::create([
                'phone'             => $request->phone,
                'name'              => $request->name,
                'phone_verified_at' => now(),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data'    => [
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => $user,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
            ], 500);
        }
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            if (User::where('phone', $request->phone)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number already registered',
                ], 422);
            }

            return $this->sendOTP($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP',
            ], 500);
        }
    }
}
