<?php

namespace App\Http\Controllers;


use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\UserOutlet;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function index()
    {
        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            $getUsers = User::with('role', 'outlets') 
                ->select('users.*')
                ->get();
        } else {
            $getUsers = User::with('role', 'outlets') 
                ->select('users.*')
                ->join('user_outlets', 'users.id', '=', 'user_outlets.user_id')
                ->whereIn('user_outlets.outlet_id', getUserOutlet()) 
                ->where('role_id', '!=', 1)
                ->get();
        }

        return view('pages.users.index', compact('getUsers'));
    }

    public function create()
    {
        // $cariOutletActive = Outlet::where('is_main', 1)->first();
        $roles = Role::where('role_name', '!=', 'DEVELOPER')->get();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required',
            'username' => 'required|unique:users,username',
            'pin' => 'nullable|numeric|digits:6', 
        ]);
    
        $checkusername = User::where('username', $request->username)->first();
    
        if (auth()->user()->role->role_name != 'SUPERADMIN') {
            if ($request->role == 'SUPERADMIN') {
                return redirect()->back()->with('error', 'Anda tidak dapat menambah user dengan role SUPERADMIN');
            }
        }
    
        try {
            DB::beginTransaction();
    
            $user = new User();
            $user->users_name = $request->nama;
            $user->email = $request->email;
            $user->password = Hash::make($request->password); 
            $user->role_id = $request->role;
            $user->users_image = $request->user_image ? $request->user_image->store('storage') : null;
            $user->username = $request->username;
    
            if ($request->pin) {
                $user->pin = Crypt::encryptString($request->pin);
            }
    
            if ($checkusername) {
                return redirect()->back()->with('error', 'Username sudah ada');
            } else {
                $user->save();
                
                $outletLogged = auth()->user()->outlets->first();  
    
                $userOutlet = new UserOutlet();
                $userOutlet->user_id = $user->id;
                $userOutlet->outlet_id = $outletLogged->id;  
                $userOutlet->save();
    
                DB::commit();
                
                return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'User gagal ditambahkan');
        }
    }
    

    public function edit($id)
    {
        $users = User::find($id);
    
        if (!$users) {
            return redirect()->route('users.index')->with('error', 'Data Tidak Ditemukan');
        }
    
        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            $roles = Role::where('outlet_id', getOutletActive()->id)
                ->where('role_name', '!=', 'SUPERADMIN') 
                ->get();
    
            return view('pages.users.edit', compact('users', 'roles'));
        }
    
        $outletLogged = auth()->user()->outlets->first(); 
        $usersOutlet = $users->outlets->first(); 
    
        if ($outletLogged->id !== $usersOutlet->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak memiliki akses');
        }
    
        $roles = Role::where('outlet_id', getOutletActive()->id)
            ->where('role_name', '!=', 'SUPERADMIN') 
            ->get();
    
        return view('pages.users.edit', compact('users', 'roles'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required',
            'role' => 'required',
            'username' => 'required',
            'pin' => 'nullable|numeric|digits:6',
            'change_pin' => 'nullable|boolean'
        ]);
    
        try {
            DB::beginTransaction();
            
            $user = User::find($id);
            
            if (auth()->user()->role->role_name != 'SUPERADMIN' && $request->role == 'SUPERADMIN') {
                return redirect()->back()->with('error', 'Anda tidak dapat memilih role SUPERADMIN');
            }
    
            $user->users_name = $request->nama;
            $user->email = $request->email;
            
            $user->role_id = $request->role;
    
            if ($request->user_image) {
                $user->users_image = $request->user_image->store('storage');
            }
    
            if ($request->change_pin && $request->pin) {
                $user->pin = Crypt::encryptString($request->pin);
            }
    
            $user->username = $request->username;
            $user->save();
    
            DB::commit();
    
            return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('users.edit', ['id' => $id])->with('error', 'User gagal diperbarui');
        }
    }
    

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {


            if ($user->role_id == 1 || $user->id == Auth::id()) {
                return redirect()->route('users.index')->with('error', 'User tidak bisa dihapus');
            }

            if ($user->stockOpnames->isNotEmpty() || $user->sales->isNotEmpty() || $user->purchase->isNotEmpty()) {
                return redirect()->route('users.index')->with('error', 'User tidak bisa dihapus, karena sudah terikat data lain!');
            }


            $user->delete();

            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');

        } catch (\Throwable $th) {
            return redirect()->route('users.index')->with('error', 'User gagal dihapus');
           
        }

    }

    public function getUserInventories($id)
    {
        $users = User::where('id', $id)->with('inventories')->first();
        $userInventories = array_map(function ($inventory) {
            return $inventory['id'];
        }, $users->inventories->toArray());
        $inventories = Inventory::all();
        return view('pages.users.add-inventory', compact('users', 'inventories', 'userInventories'));
    }

    public function updateUserInventories(Request $request, $id)
    {
        $validated = $request->validate([
            'inventories_id' => 'array',
            'inventories_id.*' => 'exists:inventories,id',
        ]);

        $user = User::find($id);
        $user->inventories()->sync($validated['inventories_id'] ?? []);
        return redirect()->route('users.inventory', [$id])->with('success', 'Data berhasil diubah!');
    }

    public function getUserOutlet($id)
    {
        $users = User::where('id', $id)->with('userOutlets')->first();
        $userOutlet = $users->userOutlets->pluck('outlet_id')->toArray();
        $outlets = Outlet::all();
        return view('pages.users.add-outlet', compact('users', 'outlets', 'userOutlet'));
    }

    public function updateUserOutlet(Request $request, $id)
    {
        $validated = $request->validate([
            'outlet_id' => 'array',
            'outlet_id.*' => 'exists:outlets,id',
        ]);

        $user = User::find($id);
        $user->outlets()->sync($validated['outlet_id'] ?? []);
        return redirect()->route('users.outlet', [$id])->with('success', 'Data berhasil diubah!');
    }
}
