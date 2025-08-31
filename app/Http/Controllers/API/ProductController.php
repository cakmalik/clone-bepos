<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\TieredPrices;
use Illuminate\Http\Request;
use App\Imports\ImportProduct;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use App\Imports\ImportTieredPrice;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Imports\ImportProductStock;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function changeProductStock($id, $qty)
    {
        ProductStock::where('product_id', $id)->decrement('stock_current', $qty);
    }
    public function productStockForReminder()
    {
        $userRole = Auth::user()->role->role_name;

        if ($userRole === 'SUPERADMIN') {
            $q = Product::join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
                ->join('outlets', 'outlets.id', '=', 'product_stocks.outlet_id')
                ->where('product_stocks.stock_current', '<=', 'products.minimum_stock')
                ->get(['products.name', 'products.minimum_stock', 'products.code', 'product_stocks.stock_current', 'outlets.name as outlet_name']);
        } else {
            $q = Product::join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
                ->where('product_stocks.outlet_id', getOutletActive()->id)
                ->where('product_stocks.stock_current', '<=', 'products.minimum_stock')
                ->get(['products.name', 'products.minimum_stock', 'products.code', 'product_stocks.stock_current']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $q
        ], 200);
    }


    public function searchProduct(Request $request)
    {
        $products = Product::select('id', 'name', 'product_unit_id')
            ->with('productUnit')
            // ->where('outlet_id', getOutletActive()->id)
            ->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('barcode', 'LIKE', '%' . $request->q . '%');
            })
            ->limit(50)
            ->orderBy('name')
            ->get()->map(function ($row) {
                $row->text = $row->name;
                $row->unit = $row->productUnit?->name ?? '-';

                return $row;
            });

        // dd($products);

        return response()->json($products);
    }

    public function importProduct(Request $request)
    {

        set_time_limit(1000);

        $validator = Validator::make($request->all(), [
            'file'      => 'required',
            'fresh'     => 'required',
            'outlet_id' => 'required',
        ]);

        if ($validator->fails()) {
            $jsonData = [
                'code'      => 422,
                'success'   => false,
                'message'   => $validator->errors()->first()
            ];

            return response()->json($jsonData);
        }

        if ($request->fresh == 'YES') {
            Schema::disableForeignKeyConstraints();

            ProductStockHistory::truncate();
            ProductSupplier::truncate();
            Supplier::truncate();
            ProductStock::truncate();
            TieredPrices::truncate();
            ProductPrice::truncate();
            Product::truncate();
            ProductCategory::truncate();

            Schema::enableForeignKeyConstraints();
        }

        try {

            if ($request->fresh == 'YES') {
                Supplier::create([
                    'user_id'   => 1,
                    'code'      => 'SUP0001',
                    'name'      =>  'CV SMART JAYA',
                    'slug'      => 'smart-jaya',
                    'date'      => Carbon::now(),
                    'phone'     => '-',
                    'address'   => 'Jawa Tengah',
                    'desc'      => 'Supplier default'
                ]);

                ProductCategory::create([
                    'outlet_id' => 1,
                    'code'      => '0000',
                    'name'      => 'Uncategorized',
                    'slug'      => 'uncategorized',
                    'is_parent_category' => true,
                    'desc'  => 'Tidak dikategorikan'
                ]);
            }

            // //make sure file is uploaded and file exists
            // if (!$request->hasFile('file') || !$request->file('file')->exists()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'File not found'
            //     ], 422);
            // }
            Log::info('atasnya');
            Excel::import(new ImportProduct($request->outlet_id), $request->file('file')->store('tmp'));

            $jsonData = [
                'success'   => true,
                'message'   => "Produk berhasil diimport !"
            ];

            return response()->json($jsonData, 200);
        } catch (\Throwable $th) {
            $jsonData = [
                'success'   => false,
                'message'   => 'Upss. Kesalahan di server !' . $th->getMessage()
            ];

            return response()->json($jsonData, 422);
        }
    }


    public function importTieredPrice(Request $request)
    {
        set_time_limit(300);

        $validator = Validator::make($request->all(), [
            'file'      => 'required',
        ]);

        if ($validator->fails()) {
            $jsonData = [
                'code'      => 422,
                'success'   => false,
                'message'   => $validator->errors()->first()
            ];

            return response()->json($jsonData);
        }

        Excel::import(new ImportTieredPrice, $request->file('file')->store('tmp'));


        return response()->json('Harga bertingkat berhasil diimport !');
    }


    public function importStock(Request $request)
    {
        set_time_limit(300);

        $validator = Validator::make($request->all(), [
            'file'      => 'required',
            'outlet_id' => 'required'
        ]);

        if ($validator->fails()) {
            $jsonData = [
                'code'      => 422,
                'success'   => false,
                'message'   => $validator->errors()->first()
            ];

            return response()->json($jsonData);
        }

        Excel::import(new ImportProductStock($request->outlet_id), $request->file('file')->store('tmp'));

        return response()->json('Stok berhasil diimport !');

    }
}
