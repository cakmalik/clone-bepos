<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\Outlet;
use App\Models\Permission;
use App\Models\UserOutlet;

class RoleController extends Controller
{


    public function create(Request $request)
    {
        $request->validate([
            'name_role' => 'required',
        ]);

        if (Role::where('role_name', $request->name_role)->where('outlet_id', getOutletActive()->id)->first()) {
            return redirect()->route('role.index')->with('error', 'Role sudah ada');
        }

        try {
            DB::beginTransaction();

            $outlet_id_main = getOutletActive();

            $role = new Role();
            $role->outlet_id = $outlet_id_main->id;
            $role->role_name = $request->name_role;
            $role->save();
            DB::commit();
            return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('role.index')->with('error', 'Role gagal ditambahkan');
        }
    }

    public function index()
    {
        $getRole = Role::all();
        return view('pages.role.index', compact('getRole'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_role' => 'required',
        ]);

        if (Role::where('role_name', $request->name_role)->where('outlet_id', getOutletActive()->id)->first() && $request->name_role != Role::find($id)->role_name) {
            return redirect()->route('role.index')->with('error', 'Role sudah ada');
        }

        try {
            DB::beginTransaction();

            $role = Role::find($id);
            $role->role_name = $request->name_role;
            $role->save();
            DB::commit();
            return redirect()->route('role.index')->with('success', 'Role berhasil diubah');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('role.index')->with('error', 'Role gagal diubah');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            //delete role and delete permission role
            $role = Role::find($id);
            $role->delete();

            $permission = Permission::where('role_id', $id)->get();

            foreach ($permission as $key => $value) {
                $value->delete();
            }

            DB::commit();
            return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('role.index')->with('error', 'Role gagal dihapus');
        }
    }
}
