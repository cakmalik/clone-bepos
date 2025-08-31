<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function profile()
    {
        try {
            $user = User::find(auth()->user()->id);

            $data = [
                'id' => $user->id,
                'name' => $user->users_name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'pin' => Crypt::decryptString($user->pin),
                'image' => asset('storage/images/' . $user->users_image),
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'pin' => 'required|numeric|digits:6',
            'phone' => 'required|numeric|digits_between:10,13',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $user = User::find($id);
            if ($request->hasFile('image')) {
                // Upload file baru
                $file = $request->file('image')->store('storage');
                $user->users_image = $file;
            }
    
            $user->users_name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->pin = Crypt::encryptString($request->pin);
            $user->save();
    
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user.',
                'data' => []
            ], 500);
        }
    }
}
