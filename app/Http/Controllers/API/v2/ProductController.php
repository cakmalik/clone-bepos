<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\SellingPrice;

class ProductController extends Controller
{
    public function changeProductStock($id, $qty)
    {
        ProductStock::where('product_id', $id)->decrement('stock_current', $qty);
    }

    public function addProductStock($id, $qty)
    {
        ProductStock::where('product_id', $id)->increment('stock_current', $qty);
    }

    // public function index(Request $request)
    // {
    //     try {
    //         $query = Product::with(['productCategory', 'productUnit', 'productStock', 'productPrice' => function ($query) {
    //             // Tambahkan pengecekan jika parameter selling_price ada
    //             if (request()->has('selling_price')) {
    //                 // Ambil nilai selling_price dari parameter query
    //                 $sellingPriceId = request()->input('selling_price');
            
    //                 // Jika selling_price tidak kosong, cari produk berdasarkan selling_price ID
    //                 if (!empty($sellingPriceId)) {
    //                     $query->where('selling_price_id', $sellingPriceId);
    //                 } else {
    //                     // Jika selling_price kosong, cari produk dengan tipe 'utama'
    //                     $query->where('type', 'utama');
    //                 }
    //             } else {
    //                 // Jika parameter selling_price tidak ada, cari produk dengan tipe 'utama'
    //                 $query->where('type', 'utama');
    //             }
    //         }])
    //         ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
    //         ->where('products.outlet_id', getOutletActive()->id)
    //         // ->where('product_stocks.stock_current', '>', 1)
    //         ->select('products.*')
    //         ->orderBy('products.name', 'asc');
    
    //         // Cek apakah ada parameter 'name' yang dikirimkan
    //         if ($request->has('name')) {
    //             $name = $request->input('name');
    //             // Filter produk berdasarkan nama yang mengandung kata 'name'
    //             $query->where('products.name', 'LIKE', '%' . $name . '%');
    //         }
    
    //         // Cek apakah ada parameter 'category' yang dikirimkan
    //         if ($request->has('category')) {
    //             $categoryName = $request->input('category');
    //             // Filter produk berdasarkan kategori
    //             $query->whereHas('productCategory', function ($q) use ($categoryName) {
    //                 $q->where('name', $categoryName);
    //             });
    //         }
    
    //         $products = $query->get();
    
    //         $data = [];
    //         foreach ($products as $product) {
    //             $sellingPrice = $product->productPrice->first();
    //             $sellingPriceValue = $sellingPrice ? $sellingPrice['price'] : null;
    
    //             $stockData = $product->productStock->first();
    //             $stockCurrent = $stockData && $stockData['outlet_id'] === getOutletActive()->id ? $stockData['stock_current'] : 0;
    
    //             $data[] = [
    //                 'id' => $product->id,
    //                 'name' => $product->name,
    //                 'category' => $product->productCategory->name,
    //                 'image' => asset('storage/images/' . $product->image),
    //                 'price' => $sellingPriceValue,
    //                 'stock' => $stockCurrent,
    //             ];
    //         }
    
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'success',
    //             'data' => $data
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $th->getMessage(),
    //             'data' => []
    //         ], 500);
    //     }
    // }

    public function index(Request $request)
{
    try {
        $query = Product::with(['productCategory', 'productUnit', 'productStock', 'productPrice' => function ($query) {
            // Tambahkan pengecekan jika parameter selling_price ada
            if (request()->has('selling_price')) {
                // Ambil nilai selling_price dari parameter query
                $sellingPriceId = request()->input('selling_price');

                // Jika selling_price tidak kosong, cari produk berdasarkan selling_price ID
                if (!empty($sellingPriceId)) {
                    $query->where('selling_price_id', $sellingPriceId);
                } else {
                    // Jika selling_price kosong, cari produk dengan tipe 'utama'
                    $query->where('type', 'utama');
                }
            } else {
                // Jika parameter selling_price tidak ada, cari produk dengan tipe 'utama'
                $query->where('type', 'utama');
            }

            // Tambahan: Tambahkan kondisi untuk mengabaikan selling_price dengan nilai NULL
            $query->whereNotNull('product_prices.price');
        }])
        ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
        ->where('products.outlet_id', getOutletActive()->id)
        ->select('products.*')
        ->orderBy('products.name', 'asc');

        // Cek apakah ada parameter 'name' yang dikirimkan
        if ($request->has('name')) {
            $name = $request->input('name');
            // Filter produk berdasarkan nama yang mengandung kata 'name'
            $query->where('products.name', 'LIKE', '%' . $name . '%');
        }

        // Cek apakah ada parameter 'category' yang dikirimkan
        if ($request->has('category')) {
            $categoryName = $request->input('category');
            // Filter produk berdasarkan kategori
            $query->whereHas('productCategory', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        $products = $query->get();

        $data = [];
        foreach ($products as $product) {
            $sellingPrice = $product->productPrice->first();

            // Tambahan: Periksa apakah selling_price memiliki nilai sebelum menambahkannya ke data
            if ($sellingPrice && !is_null($sellingPrice->price)) {
                $sellingPriceValue = $sellingPrice->price;
            } else {
                // Jika selling_price kosong atau NULL, lewati produk ini
                continue;
            }

            $stockData = $product->productStock->first();
            $stockCurrent = $stockData && $stockData->outlet_id === getOutletActive()->id ? $stockData->stock_current : 0;

            $data[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->productCategory->name,
                'image' => asset('storage/images/' . $product->image),
                'price' => $sellingPriceValue,
                'stock' => $stockCurrent,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data ?? null
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage(),
            'data' => null
        ], 500);
    }
}

    
    

    public function categories(Request $request)
    {
        $query = Product::with(['productCategory', 'productUnit', 'productStock', 'productPrice' => function ($query) {
            $query->where('type', 'utama');
        }])
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('products.outlet_id', getOutletActive()->id)
            // ->where('product_stocks.stock_current', '>', 1)
            ->select('products.*');
    
        // Cek apakah ada parameter 'name' yang dikirimkan
        if ($request->has('name')) {
            $name = $request->input('name');
            // Filter kategori berdasarkan nama yang sesuai dengan parameter 'name'
            $query->whereHas('productCategory', function ($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%');
            });
        }
    
        $products = $query->get();
    
        $categoryNames = $products->pluck('productCategory.name')->unique();
    
        $data = [];
        foreach ($categoryNames as $categoryName) {
            $data[] = [
                'name' => $categoryName,
            ];
        }
    
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function productSellingPrice()
    {
        try {
            
            $sellingPrice = SellingPrice::get();
            $data = [];
            foreach ($sellingPrice as $price) {
                $data[] = [
                    'id' => $price->id,
                    'name' => $price->name,
                ];
            }
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
    
}
