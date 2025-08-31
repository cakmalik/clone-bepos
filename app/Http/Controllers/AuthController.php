<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username'  => 'required',
            'password'  => 'required',
        ]);


        $credentials = $request->only('username', 'password');
        $rememberMe  = (!empty($request->remember_me)) ? TRUE : FALSE;

        $profilCompany = ProfilCompany::first();

        if (Auth::attempt($credentials, $rememberMe)) {
            if (Auth::user()->isDeveloper()) {
                $companyExist = !!ProfilCompany::count();
                $productExist = Product::count();
                if ($companyExist && $productExist) {
                    return redirect('/');
                } else {
                    return redirect()->route('developer.complete-seed');
                }
            }

            if (!$profilCompany || $profilCompany->status != 'active') {
                return redirect('/login')->with('error', 'Akun anda tidak aktif');
            }

            if (isTrialExpired($profilCompany)) {
                $profilCompany->status = 'inactive';
                $profilCompany->save();

                return redirect('/login')->with('error', 'Trial periode sudah berakhir. Hubungi tim sales untuk informasi lebih lanjut.');
            }

            $inventory = myInventoryId();
            if (!$inventory) {
                return redirect('/login')->with('error', 'Akun belum terdaftar di gudang / outlet!');
            }

            return redirect()->intended()->withSuccess('Berhasil Masuk!');
        }

        return redirect('/login')->with('error', 'Login Gagal, username atau password tidak sesuai.');
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('pages.users.profile');
    }

    public function changeProfile(Request $request)
    {
        $id = Auth::user()->id;
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        try {
            DB::beginTransaction();
            $requestData = $request->only([
                'name',
                'username',
                'email'
            ]);
            User::where('id', $id)->update($requestData);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $id = Auth::user()->id;
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8|alpha_dash|different:old_password',
        ]);
        try {
            $user = User::find($id);
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->withInput()->with('error', 'Password Lama tidak valid.');
            }

            DB::beginTransaction();
            User::where('id', $id)->update([
                'password' => Hash::make($request->password)
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function profile_users(Request $request)
    {
        $users = User::find($request->id);
        $data = $request->validate([
            'users_image' => 'image|file|max:2048|mimes:jpeg,png,jpg',
            'username' => 'max:255',
            'email' => 'max:255'
        ]);


        if ($request->has('username') or $request->has('email')) {

            if ($request->file('users_image')) {
                $users = User::find($request->id);
                $image_path = public_path("storage/" . $users->image);
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
                $data['users_image'] = $request->file('users_image')->store('foto');
            }
            User::where('id', $request->id)->update($data);

            return redirect()->back()->with('success', 'Sukses diupdate!');
        }
    }
    public function profile_password(Request $request)
    {
        $data = $request->validate([
            'password' => 'required'
        ]);

        if (Hash::check($request->password_old, Auth()->user()->password)) {

            $data['password'] = bcrypt($request->password);
            User::where('id', $request->id)->update($data);

            return redirect()->back()->withSuccess('Sukses diupdate!');
        } else {
            return redirect()->back()->withWarning('Password lama tidak sama!');
        }
    }
}
