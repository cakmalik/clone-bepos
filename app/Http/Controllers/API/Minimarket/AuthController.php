<?php

namespace App\Http\Controllers\API\Minimarket;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfilCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            return responseAPI(false, 'Validation error', $validator->errors());
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
    
            if (!Hash::check($request->password, $user->password) || !password_verify($request->password, $user->password)) {
                return responseAPI(false, 'Invalid credentials', null);
            }
    
            $profilCompany = ProfilCompany::first();
            
            if ($profilCompany && isTrialExpired($profilCompany)) {
                $profilCompany->status = 'inactive';
                $profilCompany->save();
    
                return response()->json([
                    'message' => 'Trial periode sudah berakhir, akun Anda dinonaktifkan.',
                    'success' => false
                ]);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            $baseURL = config('app.url');
            $logo = $baseURL . '/storage/images/' . $profilCompany->image;
    
            return response()->json([
                'success' => true,
                'token' => $token,
                'role' => $user->role['role_name'],
                'logo' => $logo
            ], 200);
    
        } catch (\Exception $e) {
            return responseAPI(false, 'Something went wrong', $e->getMessage());
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

    public function user()
    {
        $user = Auth::user();
        $selectedColumn = (['id', 'users_name', 'users_image']);
        $user = User::select($selectedColumn)->find($user->id);
        $user->outlets?->first();
        return responseAPI(true, 'success', $user);
    }
}
