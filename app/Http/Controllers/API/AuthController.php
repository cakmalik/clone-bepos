<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'success' => false
            ]);
        }

        try {
            $user = User::where('email', $request->email)
                ->orWhere('username', $request->email)
                ->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Pengguna tidak ditemukan',
                    'success' => false,
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password atau Username salah',
                    'success' => false,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'success',
                'token_type' => 'Bearer',
                'token' => $token,
                'user' => $user,

                'success' => true
                // 'expired_at' => $expirationTime
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function loginWithPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pin' => 'required|numeric|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if ($user->decryptedPin() != $request->pin) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'success',
                'token_type' => 'Bearer',
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
