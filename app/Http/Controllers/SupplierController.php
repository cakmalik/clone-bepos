<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('user')->get();
        return view('pages.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $autoCodeSupplier = autoCode('suppliers', 'code', 'SUP', 4);
        return view('pages.supplier.create', compact('autoCodeSupplier'));
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        return view('pages.supplier.show', ['title' => 'Detail ' . $supplier->name, 'supplier' => $supplier]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'desc' => 'required',
        ], [], [
            'code' => 'Kode Supplier',
            'name' => 'Nama Supplier',
            'phone' => 'Nomor Telp',
            'address' => 'Alamat',
            'desc' => 'Deskripsi'
        ]);

        try {
            $supplier = new Supplier;
            $supplier->name = $request->name;
            $supplier->slug = Str::of($request->name)->slug('-');
            $supplier->code = $request->code;
            $supplier->date = Carbon::now();
            $supplier->phone = $request->phone;
            $supplier->address = $request->address;
            $supplier->desc = $request->desc;
            $supplier->user_id = getUserIdLogin();
            $supplier->save();


            DB::commit();
            return redirect('/supplier')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }



    public function edit($id)
    {
        $supplier = Supplier::find($id);
        return view('pages.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'desc' => 'required',
        ], [], [
            'code' => 'Kode Supplier',
            'name' => 'Nama Supplier',
            'phone' => 'Nomor Telp',
            'address' => 'Alamat',
            'desc' => 'Deskripsi'
        ]);

        try {

            Supplier::where('id', $id)->update([
                'code' => $request->code,
                'name' => $request->name,
                'slug' => Str::of($request->name)->slug('-'),
                'phone' => $request->phone,
                'address' => $request->address,
                'desc' => $request->desc,

            ]);

            DB::commit();
            return redirect('/supplier')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Supplier::destroy($id);
            DB::commit();
            return response()->json([
                'message' => 'Sukses di Hapus',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal di Hapus',
            ], 422);
        }
    }
}
