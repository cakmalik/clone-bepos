<?php

namespace App\Http\Controllers;
use App\Models\ProductStock;
use Illuminate\Auth\Events\Validated;
use App\Models\Product;

use Illuminate\Http\Request;

class SettingProductStock extends Controller
{
    public function index()
    {
        $productStock = ProductStock::get();
        return view('pages.setting.product_stock.index', compact('productStock'));
    }

    public function updateAllStok(Request $request)
    {
        $validated = $request->validate([
            'minimum_stock' => 'required',
        ]);

        try {
            Product::where('outlet_id', getOutletActive()->id)->update([
                'minimum_stock' => $validated['minimum_stock']
            ]);
           
            return redirect()->back()->with('success', 'Berhasil mengubah stok minimum');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal mengubah stok minimum');
        }
    }
}
