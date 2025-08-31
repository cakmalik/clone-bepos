<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $dataProductUnit = ProductUnit::orderBy('created_at', 'desc')->get();
    
        return view('pages.product.product_unit.index', compact('dataProductUnit'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'name' => 'required',
            'desc' => 'nullable',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'desc' => $request->desc,
                'outlet_id' => getOutletActive()->id,
                'user_id' => getUserIdLogin()
            ];

            ProductUnit::create($data);
            DB::commit();
            return redirect()->route('productUnit.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Data gagal ditambahkan. ');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //  
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            //update productUnit
            $productUnit = ProductUnit::where('id', $id)->first();
            $productUnit->name = $request->name;
            $productUnit->desc = $request->desc;
            $productUnit->save();
            DB::commit();
            return redirect()->route('productUnit.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('productUnit.index')->with('warning', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            //delete productUnit
            $productUnit = ProductUnit::where('id', $id)->first();
            if ($productUnit->products()->count() > 0) {
                return redirect()->route('productUnit.index')->with('error', 'Satuan tidak dapat dihapus, telah digunakan');
            }

            $productUnit->delete();
            DB::commit();
            return redirect()->route('productUnit.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('productUnit.index')->with('success', 'Data gagal dihapus');
        }
    }
}
