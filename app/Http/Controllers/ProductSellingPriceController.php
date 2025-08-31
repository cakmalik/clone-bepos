<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellingPrice;

class ProductSellingPriceController extends Controller
{
    public function index()
    {
        $dataSellingPrice = SellingPrice::all();
        return view('pages.product.product_selling_price.index', compact('dataSellingPrice'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:selling_prices,name|regex:/^[a-zA-Z0-9\s]+$/'
        ]);

        try {
            SellingPrice::create([
                'name' => $request->name,
            ]);
            return redirect()->route('productSellingPrice.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('productSellingPrice.index')->with('error', 'Data gagal ditambahkan' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $price = SellingPrice::with('productPrice')->where('id', $id)->first();

        try {

            if($price->status == 'active'|| $price->productPrice->count() > 0){
                return redirect()->route('productSellingPrice.index')->with('error', 'Data gagal dihapus, sedang digunakan!');
            }

            $price->delete();
            
            return redirect()->route('productSellingPrice.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->route('productSellingPrice.index')->with('error', 'Data gagal dihapus');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:selling_prices,name,' . $id . '|regex:/^[a-zA-Z0-9\s]+$/'
        ]);

        try {
            SellingPrice::find($id)->update([
                'name' => $request->name,
            ]);
            return redirect()->route('productSellingPrice.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('productSellingPrice.index')->with('error', 'Data gagal diubah' . $th->getMessage());
        }
    }
}
