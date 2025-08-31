<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPrice;
use App\Models\Product;

class ProductPriceController extends Controller
{
    public function index()
    {
        $productPrices = ProductPrice::with('product')->whereHas('product', function ($query) {
            $query->where('outlet_id', getOutletActive()->id);
        })->get();

        $products = Product::where('outlet_id', getOutletActive()->id)->whereNotIn('id', function ($query) {
            $query->select('product_id')->from('product_prices');
        })->get();

        return view('pages.product.product_price.index', compact('productPrices', 'products'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'type' => 'required',
            'price' => 'required',
        ]);

        try {
            $productPrice = new ProductPrice;
            $productPrice->product_id = $request->product_id;
            $productPrice->price = str_replace('.', '', $request->price);
            $productPrice->type = $request->type;
            $productPrice->save();

            return redirect()->route('productPrice.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('productPrice.index')->with('error', 'Data gagal ditambahkan' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $productPrice = ProductPrice::find($id);
        $productPrice->delete();

        return redirect()->route('productPrice.index')->with('success', 'Data berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required',
            'price' => 'required',
        ]);

        try {
            $productPrice = ProductPrice::find($id);
            $productPrice->price = str_replace('.', '', $request->price);
            $productPrice->type = $request->type;
            $productPrice->save();

            return redirect()->route('productPrice.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('productPrice.index')->with('error', 'Data gagal diubah' . $th->getMessage());
        }
    }
}
