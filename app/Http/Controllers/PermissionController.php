<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Outlet;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{

    public function index()
    {
        $getPermission = DB::table('roles')
            ->select('roles.id', 'roles.role_name', DB::raw('count(permissions.role_id) as jumlah_menu'))
            ->leftJoin('permissions', 'roles.id', '=', 'permissions.role_id')
            ->groupBy('roles.id')
            ->get();

        return view('pages.permission.index', compact('getPermission'));
    }


    public function create()
    {

        $roles = Role::whereNotIn('id', function ($query) {
            $query->select('role_id')->from('permissions');
        })->get();
        $menus = Menu::all();
        return view('pages.permission.create', compact('roles', 'menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
            'menu_permission' => 'required',
        ]);

        //input to database permission
        try {
            DB::beginTransaction();

            $role_id = $request->role_id;
            $cariOutletActive = Outlet::where('is_main', 1)->first();
            foreach ($request->menu_permission as $menu) {
                $permission = new Permission();
                $permission->outlet_id = $cariOutletActive->id;
                $permission->role_id = $role_id;
                $permission->menu_id = $menu;
                $permission->save();
            }

            DB::commit();
            return redirect()->route('permission.index')->with('success', 'Permission berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('permission.create')->with('error', 'Permission gagal ditambahkan');
        }
    }

    public function edit($id)
    {
        $role       = Role::findOrFail($id);
        $access    = Menu::LeftJoin('permissions as p', 'menus.id', '=', 'p.menu_id')
            ->select('menus.id', 'menus.menu_name', 'p.role_id')
            ->where('p.role_id', $id)
            ->get();

        $access = Menu::orderBy('menu_name', 'asc')->get();

        foreach ($access as $row) {
            $is_access = false;

            $check = Permission::where([['menu_id', $row->id], ['role_id', $id]])->first();
            if ($check) {
                $is_access = true;
            }

            $access_map[] = array(
                'menu_id' => $row->id,
                'menu_name' => $row->menu_name,
                'is_access' => $is_access,
            );
        }

        $data = array(
            'role' => $role->role_name,
            'role_id' => $role->id,
            'access_map' => $access_map,
        );

        return view('pages.permission.edit', compact('data'));
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Menu tidak boleh kosong');
        }

        // if ($id == 1) {
        //     return redirect()->route('permission.index')->with('error', 'Role Super Admin tidak bisa diubah');
        // }

        DB::beginTransaction();

        try {

            $cek_role = Permission::where('role_id', $id)->get();

            if ($cek_role) {
                foreach ($request->menu_id as $menu) {
                    $exist = Permission::where([['role_id', $id], ['menu_id', $menu]])->first();
                    if (!$exist) {
                        $permission = new Permission();
                        $permission->outlet_id = getOutletActive()->id;
                        $permission->role_id = $id;
                        $permission->menu_id = $menu;
                        $permission->save();
                    }
                }

                Permission::where('role_id', $id)->whereNotIn('menu_id', $request->menu_id)->delete();
            } else {
                foreach ($request->menu_id as $menu) {
                    $permission = new Permission();
                    $permission->outlet_id = getOutletActive()->id;
                    $permission->role_id = $id;
                    $permission->menu_id = $menu;
                    $permission->save();
                }
            }

            DB::commit();
            return redirect()->route('permission.index')->with('success', 'Permission berhasil diubah');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal Merubah permission');
        }
    }
}
