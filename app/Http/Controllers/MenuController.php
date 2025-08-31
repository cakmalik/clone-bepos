<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;


class MenuController extends Controller
{
    public function create()
    {
        $menus = Menu::get();
        return view('pages.menu.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $menu = new Menu();
            $menu->menu_name = $request->nama_menu;
            if ($request->is_parent == "on") {
                $menu->is_parent = 1;
                $menu->parent_menu_id = null;
            } else {
                $menu->is_parent = 0;
                $menu->parent_menu_id = $request->parent;
            }
            $menu->save();
            DB::commit();
            return redirect()->route('menu.create')->with('success', 'Berhasil Menambahkan Menu');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('menu.create')->with('error', 'Menu gagal ditambahkan');
        }
    }
}
