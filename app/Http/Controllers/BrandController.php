<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index()
    {
        $brand = Brand::All();
        return view('pages.brand.index', ['brand' => $brand]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'name' => 'required'
            ], [], [
                'name' => 'Nama Brand'
            ]);
            Brand::create($data);

            DB::commit();
            return redirect('/brand')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal di Simpan!');
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'name' => 'required'
            ], [], [
                'name' => 'Nama Brand'
            ]);
            Brand::where('id', $id)->update($data);

            DB::commit();
            return redirect('/brand')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal di Update!');
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Brand::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'Sukses di Hapus !'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message'  => 'Gagal di Hapus!'
            ], 422);
        }
    }
}
