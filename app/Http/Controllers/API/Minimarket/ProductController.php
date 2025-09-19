<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Models\Product;
use App\Models\UserOutlet;
use App\Models\ProductUnit;
use Illuminate\Support\Str;
use App\Models\ProductPrice;
use App\Models\TieredPrices;
use Illuminate\Http\Request;
use App\Models\ProductBundle;
use App\Models\ProductDiscount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{
    // public function index(Request $request)
    // {
    //     // $cacheKey = 'product_cache:' . md5(serialize($request->all()));

    //     $outlet_id = auth()->user()->outlets?->first()->id;

    //     $category_id = $request->query('category_id');
    //     $per_page = $request->query('per_page') ?? 50;
    //     $selling_price_id = $request->query('selling_price_id') ?? 1;
    //     $search = $request->search;

    //     $products = DB::table('products')
    //         ->join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
    //         ->join('product_prices', 'product_prices.product_id', '=', 'products.id')
    //         ->where('products.deleted_at', null)
    //         ->where('product_stocks.outlet_id', $outlet_id)
    //         ->where('product_prices.type', 'utama')
    //         ->when($search, function ($query, $search) {
    //             $query->where(function ($query) use ($search) {
    //                 $query
    //                     ->where('products.barcode', $search)
    //                     ->orWhere('products.name', 'LIKE', "%{$search}%")
    //                     ->orWhere('products.code', 'LIKE', "{$search}%");
    //             });
    //         })
    //         ->when($category_id, function ($query, $category_id) {
    //             $query->where('products.product_category_id', $category_id);
    //         })
    //         ->select('products.id', 'products.barcode', 'products.code', 'products.name', 'products.capital_price', 'products.desc', 'product_prices.price', 'product_stocks.stock_current')
    //         ->limit($request->query('limit') ?? 10)
    //         ->get();

    //     $products = $products->map(function ($product) {
    //         $product->discount_collection = ProductDiscount::where('product_id', $product->id)
    //             ->where('start_date', '<=', now())
    //             ->where('expired_date', '>=', now())
    //             ->first(['amount', 'discount_type']);

    //         $product->tiered_prices = TieredPrices::where('product_id', $product->id)->get(['min_qty', 'max_qty', 'price']);

    //         return $product;
    //     });

    //     if ($products->count() > 0 && $products[0]->barcode == $request->search) {
    //         $type = 'barcode';
    //     } else {
    //         $type = 'name';
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'status' => 'success',
    //         'data' => $products,
    //         'count' => $products->count(),
    //         'type' => $type,
    //     ]);
    // }

    public function getProductByBarcode($code)
    {
        $outletId = Auth::user()?->userOutlets?->first()?->outlet_id;

        $products = DB::table('products')
            ->join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
            ->join('product_prices', 'product_prices.product_id', '=', 'products.id')
            ->join('product_units', 'product_units.id', '=', 'products.product_unit_id')
            ->where('products.deleted_at', null)
            ->where('product_prices.type', 'utama')
            ->where('products.barcode', $code)
            ->where('product_stocks.outlet_id', $outletId)
            ->select(
                'products.id',
                'products.barcode',
                'products.product_unit_id',
                'products.code',
                'products.name',
                'products.capital_price',
                'products.desc',
                'products.is_support_qty_decimal',
                'products.is_bundle',
                'products.is_main_stock',
                'product_prices.price',
                'product_stocks.stock_current',
                'product_units.symbol as unit_symbol'

            )
            ->limit(1)
            ->get();


        $products = $products->map(function ($product) {
            $product->discount_collection = ProductDiscount::where('product_id', $product->id)
                ->where('start_date', '<=', now())
                ->where('expired_date', '>=', now())
                ->first(['amount', 'discount_type']);

            $product->tiered_prices = TieredPrices::query()
                ->where('product_id', $product->id)
                ->get(['min_qty', 'max_qty', 'price']);

            $tieredPrice = TieredPrices::query()
                ->where('product_id', $product->id)
                ->where('outlet_id', getOutletActive()->id)
                ->first();

            $product->outlet_price = $tieredPrice ? $tieredPrice->price : null;

            $units                           = $this->__getUnitsForProduct($product->product_unit_id, $product->product_unit_id);
            $product->unit_collection        = $units;
            $product->is_support_qty_decimal = $product->is_support_qty_decimal ? true : false;

            $variantPrices = ProductPrice::where('product_id', $product->id)
                ->with('sellingPrice:id,name')
                ->get(['id', 'product_id', 'price', 'type', 'selling_price_id']);

            if ($variantPrices->count() > 1) {
                $product->variant_prices = $variantPrices->map(function ($variant) {
                    return [
                        'name' => optional($variant->sellingPrice)->name,
                        'price' => $variant->price,
                        'type' => $variant->type,
                    ];
                });
            } elseif ($variantPrices->count() === 1 && $variantPrices->first()->type !== 'utama') {
                $product->variant_prices = $variantPrices->map(function ($variant) {
                    return [
                        'name' => optional($variant->sellingPrice)->name,
                        'price' => $variant->price,
                        'type' => $variant->type,
                    ];
                });
            } else {
                $product->variant_prices = [];
            }

            return $product;
        });


        return response()->json([
            'success' => true,
            'status'  => 'success',
            'data'    => $products,
            'count'   => $products->count(),
            'type'    => 'barcode',
        ]);
    }

    public function index(Request $request)
    {
        // $cacheKey = 'product_cache:' . md5(serialize($request->all()));

        $user = $request->user();

        $outlet    = UserOutlet::where('user_id', $user->id)->first();
        $outlet_id = $outlet->outlet_id;


        $category_id = $request->query('category_id');
        $per_page = $request->query('per_page') ?? 50;
        $selling_price_id = $request->query('selling_price_id') ?? 1;
        $search           = $request->search;

        $products = DB::table('products')
            ->join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
            ->join('product_prices', 'product_prices.product_id', '=', 'products.id')
            ->join('product_units', 'product_units.id', '=', 'products.product_unit_id')
            ->where('products.deleted_at', null)
            ->where('product_stocks.outlet_id', $outlet_id)
            ->where('product_prices.type', 'utama')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('products.barcode', $search)
                        ->orWhere('products.name', 'LIKE', "%{$search}%")
                        ->orWhere('products.code', 'LIKE', "{$search}%");
                });
            })
            ->when($category_id, function ($query, $category_id) {
                $query->where('products.product_category_id', $category_id);
            })
            ->select(
                'products.id',
                'products.barcode',
                'products.product_unit_id',
                'products.code',
                'products.name',
                'products.capital_price',
                'products.desc',
                'products.is_support_qty_decimal',
                'products.is_bundle',
                'products.is_main_stock',
                'product_prices.price',
                'product_stocks.stock_current',
                'product_units.symbol as unit_symbol'
            )
            ->limit($request->query('limit') ?? 10)
            ->get()->map(function ($row) {
                $row->stock_current = formatDecimal($row->stock_current);

                return $row;
            });

        $products = $products->map(function ($product) {
            $product->name                = Str::upper($product->name);
            $product->discount_collection = ProductDiscount::where('product_id', $product->id)
                ->where('start_date', '<=', now())
                ->where('expired_date', '>=', now())
                ->first(['amount', 'discount_type']);

            $outletId = getOutletActive()->id;
            $tieredPrices = TieredPrices::query()
                ->where('product_id', $product->id)
                ->where('outlet_id', $outletId)
                ->get(['outlet_id', 'min_qty', 'max_qty', 'price']);

            if ($tieredPrices->isEmpty()) {
                $tieredPrices = TieredPrices::query()
                    ->where('product_id', $product->id)
                    ->whereNull('outlet_id')
                    ->get(['outlet_id', 'min_qty', 'max_qty', 'price']);
            }

            $product->tiered_prices = $tieredPrices;

            $product->customer_prices = ProductPrice::query()
                ->where('product_id', $product->id)
                ->whereNotNull('customer_category_id')
                ->get(['customer_category_id', 'price']);

            if ($product->is_bundle) {
                $product->bundle_items = ProductBundle::where('product_bundle_id', $product->id)
                    ->join('products', 'products.id', '=', 'product_bundle_items.product_id')
                    ->leftJoin('product_units', 'product_units.id', '=', 'products.product_unit_id')
                    ->get([
                        'product_bundle_items.id as product_bundle_item_id',
                        'product_bundle_items.product_id',
                        'products.name as product_name',
                        'product_units.name as unit_name',
                        'product_bundle_items.qty'
                    ]);
            }

            // dapetin units
            $units                           = $this->__getUnitsForProduct($product->product_unit_id, $product->product_unit_id);
            $product->unit_collection        = $units;
            $product->is_support_qty_decimal = $product->is_support_qty_decimal ? true : false;
            $product->unit_symbol            = $product->unit_symbol;

            $variantPrices = ProductPrice::where('product_id', $product->id)
                ->with('sellingPrice:id,name')
                ->get(['id', 'product_id', 'price', 'type', 'selling_price_id']);

            if ($variantPrices->count() > 1) {
                $product->variant_prices = $variantPrices->map(function ($variant) {
                    return [
                        'id'    => $variant->id,
                        'name'  => optional($variant->sellingPrice)->name,
                        'price' => $variant->price,
                        'type'  => $variant->type,
                    ];
                });
            } elseif ($variantPrices->count() === 1 && $variantPrices->first()->type !== 'utama') {
                $product->variant_prices = $variantPrices->map(function ($variant) {
                    return [
                        'id'    => $variant->id,
                        'name'  => optional($variant->sellingPrice)->name,
                        'price' => $variant->price,
                        'type'  => $variant->type,
                    ];
                });
            } else {
                $product->variant_prices = [];
            }

            return $product;
        });

        if ($products->count() > 0 && $products[0]->barcode == $request->search) {
            $type = 'barcode';
        } else {
            $type = 'name';
        }

        return response()->json([
            'success' => true,
            'status'  => 'success',
            'data'    => $products,
            'count'   => $products->count(),
            'type'    => $type,
        ]);
    }

    private function __getUnitsForProduct($product_unit_id, $active_product_unit_id)
    {
        $units = ProductUnit::where('base_unit_id', $product_unit_id)
            ->orWhere('id', $product_unit_id)
            ->get(['base_unit_id', 'id', 'name', 'symbol', 'conversion_rate'])
            ->map(function ($row) use ($active_product_unit_id) {
                $row->selected = $row->id == $active_product_unit_id ? true : false;
                $row->symbol   = $row->symbol;
                return $row;
            });

        return $units;
    }

    public function categories()
    {
        $categories = Product::select('category')->distinct()->get();
        return response()->json(
            [
                'status' => 'success',
                'data'   => $categories,
            ],
            200,
        );
    }

    public function tierPrices()
    {
        $tierPrice = TieredPrices::all('product_id', 'min_qty', 'max_qty', 'price');
        return response()->json([
            'success' => true,
            'status'  => 'success',
            'data'    => $tierPrice,
        ]);
    }

    public function discounts()
    {
        $res = ProductDiscount::where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->get(['amount', 'discount_type', 'product_id', 'start_date', 'expired_date']);

        return responseAPI(true, 'berhasil get discounts', $res);
    }
}
