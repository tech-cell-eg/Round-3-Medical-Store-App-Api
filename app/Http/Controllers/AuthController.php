<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
  use ApiResponse;

  public function signup(SignupRequest $request)
  {
    try {
      DB::beginTransaction();

      $user = User::create([
        'email'    => $request->email,
        'name'     => $request->name,
        'password' => Hash::make($request->password),
        'phone'    => $request->phone,
      ]);
      $otp = $this->generateAndStoreOtp($request->email);

      DB::commit();

      return $this->successResponse([
        'user_id'    => $user->id,
        'email'      => $request->email,
        'otp'        => $otp,
        'expires_in' => 600,
      ], 'Registration successful. Please verify your email address.', 201);
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->errorResponse('Database error during registration', 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->errorResponse('Registration failed: ' . $e->getMessage(), 500);
    }
  }

  public function login(LoginRequest $request)
  {
    try {
      if (! Auth::attempt($request->only('email', 'password'))) {
        return $this->errorResponse('Invalid login credentials', 401);
      }
      $user  = User::where('email', $request->email)->firstOrFail();
      $token = $user->createToken('auth-token')->plainTextToken;

      return $this->successResponse([
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'user'         => $user,
      ], 'Login successful');
    } catch (QueryException $e) {
      return $this->errorResponse('Database error during login', 500);
    } catch (\Exception $e) {
      return $this->errorResponse('Login failed: ' . $e->getMessage(), 500);
    }
  }

  public function sendOTP(OtpRequest $request)
  {
    try {
      $user = User::where('email', $request->email)->first();

      if (! $user) {
        return $this->errorResponse('User not found', 404);
      }

      $otp = $this->generateAndStoreOtp($request->email);

      return $this->successResponse([
        'email'      => $request->email,
        'otp'        => $otp,
        'expires_in' => 600,
      ], 'OTP sent successfully');
    } catch (QueryException $e) {
      return $this->errorResponse('Database error sending OTP', 500);
    } catch (\Exception $e) {
      return $this->errorResponse('Failed to send OTP: ' . $e->getMessage(), 500);
    }
  }
  public function resendOTP(OtpRequest $request)
  {
    try {
      $user = User::where('email', $request->email)->first();

      if (! $user) {
        return $this->errorResponse('User not found', 404);
      }

      if ($user->hasVerifiedEmail()) {
        return $this->errorResponse('Email already verified', 400);
      }

      EmailVerificationToken::where('email', $request->email)->delete();

      $otp = $this->generateAndStoreOtp($request->email);

      return $this->successResponse([
        'email'      => $request->email,
        'otp'        => $otp,
        'expires_in' => 600,
      ], 'OTP resent successfully');
    } catch (QueryException $e) {
      return $this->errorResponse('Database error resending OTP', 500);
    } catch (\Exception $e) {
      return $this->errorResponse('Failed to resend OTP: ' . $e->getMessage(), 500);
    }
  }

  public function verifyOTP(VerifyOtpRequest $request)
  {
    try {
      DB::beginTransaction();

      $verification = EmailVerificationToken::where([
        'email' => $request->email,
        'token' => $request->otp,
      ])->first();

      if (! $verification || $verification->expires_at < now()) {
        return $this->errorResponse('Invalid or expired OTP', 422);
      }

      $user = User::where('email', $request->email)->first();
      if (! $user) {
        return $this->errorResponse('User not found', 404);
      }

      if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
      }

      $token = $user->createToken('auth-token')->plainTextToken;

      $verification->delete();

      DB::commit();

      return $this->successResponse([
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'user'         => $user,
      ], 'Email verified successfully');
    } catch (QueryException $e) {
      DB::rollBack();
      return $this->errorResponse('Database error during OTP verification', 500);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->errorResponse('OTP verification failed: ' . $e->getMessage(), 500);
    }
  }
  public function logout(Request $request)
  {
    try {
      $request->user()->currentAccessToken()->delete();

      return $this->successResponse(
        null,
        'Successfully logged out'
      );
    } catch (\Exception $e) {
      return $this->errorResponse(
        'Logout failed: ' . $e->getMessage(),
        500
      );
    }
  }
  protected function generateAndStoreOtp($email)
  {
    try {
      $otp       = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
      $expiresAt = Carbon::now()->addMinutes(10);

      $token = EmailVerificationToken::updateOrCreate(
        ['email' => $email],
        [
          'token'      => $otp,
          'expires_at' => $expiresAt,
        ]
      );

      return $otp;
    } catch (QueryException $e) {
      throw new \Exception('Failed to generate OTP due to database error');
    }
  }
}
