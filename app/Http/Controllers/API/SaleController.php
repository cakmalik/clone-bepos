<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SellingPrice;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use GuzzleHttp\Psr7\Response;
use App\Models\CashierMachine;
use App\Models\ProductCategory;
use App\Services\TripayService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SalesDetail;
use App\Models\UserOutlet;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            $now = Carbon::today()->startOfDay();
            $tx['daily'] = Sales::whereBetween('sale_date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
                ->where('user_id', auth()->user()->id)
                ->sum('final_amount');
            $tx['weekly'] = Sales::whereBetween('sale_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->where('user_id', auth()->user()->id)
                ->sum('final_amount');
            $tx['monthly'] = Sales::whereBetween('sale_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->where('user_id', auth()->user()->id)
                ->sum('final_amount');
            $sales = DB::table('sales')
                ->join('sales_details as sd', 'sd.sales_id', '=', 'sales.id')
                ->join('products as p', 'p.id', '=', 'sd.product_id')
                ->when($request->filter_by, function ($query, $filter_by) use ($now) {
                    if ($filter_by === 'today') {
                        $query->whereBetween('sales.sale_date', [
                            Carbon::now()->startOfDay(),
                            Carbon::now()->endOfDay()
                        ]);
                    } elseif ($filter_by === 'month') {
                        $query->whereBetween('sales.sale_date', [
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth()
                        ]);
                    }
                })
                ->select('p.name', DB::raw('SUM(sd.qty) as total_qty'))
                ->groupBy('p.name')
                ->orderByDesc('total_qty')
                ->get();
            $tx['top'] = $sales;
            $tx['total_qty'] =  $sales->sum('total_qty');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'data' => $tx
        ], 200);
    }

    public function products(Request $request, $selling_price_id)
    {
        $outlet_id = auth()->user()->outlets?->first()->id;
        $category_id = $request->query('category_id');
        $per_page = $request->query('per_page') ?? 10;

        $products = DB::table('products')
            // ->join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
            ->join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
            ->join('product_prices', 'product_prices.product_id', '=', 'products.id')
            ->join('selling_prices', 'product_prices.selling_price_id', '=', 'selling_prices.id')
            ->where('products.deleted_at', null)
            // ->where('product_stocks.stock_current', '>', 0)
            ->where('selling_prices.id', '=', $selling_price_id)
            ->where('product_stocks.outlet_id', $outlet_id)
            ->when($category_id, function ($query, $category_id) {
                $query->where('products.product_category_id', $category_id);
            })
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('products.name', 'LIKE', "%{$search}%")
                        ->orWhere('products.code', 'LIKE', "%{$search}%");
                });
            })
            ->select('products.*', 'product_prices.price', 'product_stocks.stock_current')
            ->paginate($per_page)
            ->withQueryString();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function categories()
    {
        $categories = ProductCategory::all();
        return response()->json([
            'message' => 'success',
            'data' => $categories
        ], 200);
    }

    public function sellingPrice()
    {
        $pricelist = SellingPrice::get(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'data' => $pricelist
        ], 200);
    }

    public function outlets(Request $request)
    {
        $outlets = Outlet::whereHas('userOutlets', function ($q) use ($request) {
            $q->where('user_id', $request->user_id);
        })->with('cashierMachine')->get()->map(function ($outlet) {
            $outlet->type = $outlet->type;
            return $outlet;
        });

        return response()->json([
            'message' => 'success',
            'data' => $outlets
        ], 200);
    }

    public function paymentMethod()
    {
        return response()->json([
            'status' => 'success',
            'data' => PaymentMethod::all(['id', 'name'])
        ], 200);
    }

    public function getChannels()
    {
        $t = new TripayService;
        return response()->json([
            'status' => 'success',
            'data' => $t->getPaymentsChannels()
        ], 200);
    }
}
