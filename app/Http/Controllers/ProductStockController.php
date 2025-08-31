<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\ProductStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ProductStockController extends Controller
{
    public function index(Request $request)
    {
//        $productStock = ProductStock::with('product')->whereHas('product', function ($query) {
//            $query->where('outlet_id', getOutletActive()->id);
//        })->get();{

        if ($request->ajax()) {
            $myInventoriesId = Auth::user()->inventories->pluck('id');
            $type_product = $request->type_product;
            $productStock = ProductStock::with('product')
                ->where(function ($query) use ($myInventoriesId) {
                    $query->where('outlet_id', getOutletActive()->id)
                        ->orWhereIn('inventory_id', $myInventoriesId);
                })
                ->when($type_product, function ($query) use ($type_product) {
                    return $query->whereHas('product', function ($query) use ($type_product) {
                        $query->where('type_product', $type_product);
                    });
                })
                ->get();

            return DataTables::of($productStock)
                ->addIndexColumn()
                ->make(true);
        }

        $type_products = ['product', 'material'];

        return view('pages.product.product_stock.index', compact('type_products'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'stock' => 'required',
        ]);

        try {
            $productStock = new ProductStock;
            $productStock->outlet_id = getOutletActive()->id;
            $productStock->product_id = $request->product_id;
            $productStock->stock_current = $request->stock;
            $productStock->save();

            return redirect()->route('productStock.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('productStock.index')->with('error', 'Data gagal ditambahkan' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $productStock = ProductStock::find($id);
        $productStock->delete();

        return redirect()->route('productStock.index')->with('success', 'Data berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required',
        ]);

        try {
            $productStock = ProductStock::find($id);
            $productStock->stock_current = $request->stock;
            $productStock->save();

            return redirect()->route('productStock.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('productStock.index')->with('error', 'Data gagal diubah' . $th->getMessage());
        }
    }

    public function editAllStock(Request $request)
    {
        $products = Product::with(['productStock' => function ($query) {
            return $query->where('outlet_id', getOutletActive()->id);
        }])->where('type_product', 'product')
            ->get();

        return view('pages.product.product_stock.edit_all_stock', compact('products'));
    }

    public function updateAllStock(Request $request)
    {
        $validated = $request->validate([
            'items' => 'array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                ProductStock::updateOrCreate(
                    ['outlet_id' => getOutletActive()->id, 'product_id' => $item['product_id']],
                    ['stock_current' => $item['qty']]
                );
            }
            DB::commit();

            return redirect()->route('productStock.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Data gagal diubah' . $th->getMessage());
        }
    }
}
