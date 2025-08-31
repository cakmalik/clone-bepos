<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserOutlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
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

            if (!password_verify($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            // dd($user->currentAccessToken());
            // $expirationTime = Carbon::now()->addDay();
            // $expirationTime = Carbon::now()->addSeconds(4);
            // $user->currentAccessToken()->update([
            //     'expired_at' => $expirationTime
            // ]);

            $cariOutletActive = Outlet::where('id', UserOutlet::where('user_id', $user->id)->first()->outlet_id)->first();
            $user = [
                'id' => $user->id,
                'outlet_id' => $cariOutletActive->id,
                'name' => $user->users_name,
                'image' => asset('storage/images/' . $user->image),
                'email' => $user->email,
                'username' => $user->username,
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'token_type' => 'Bearer',
                'token' => $token,
                'user' => $user,
                // 'expired_at' => $expirationTime
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function loginWithPin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'pin' => 'required|numeric|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('id', auth()->user()->id)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if (Crypt::decryptString($user->pin) != $request->pin) {
                return response()->json([
                    'success' => false,
                    'message' => 'pin salah',
                    'data' => []
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'success',
                'data' => []
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        dd($request->json()->all());

        
    }
}
