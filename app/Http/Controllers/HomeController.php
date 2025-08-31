<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\User;
use App\Models\Sales;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Permission;
use App\Models\UserOutlet;
use App\Models\PriceChange;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userOutets = UserOutlet::where('user_id', auth()->id())->pluck('outlet_id')->toArray();

        $jumlahSupplier = Supplier::count();
        $jumlahCustomer = Customer::count();
        $jumlahOutlet = Outlet::count();
        $jumlahUser = User::count();
        $jumlahProduk = Product::count();
        $jumlahKategoriProduk = ProductCategory::count();

        $jumlahPembelianBerhasil = Purchase::where('purchase_type', 'Reception')->where('purchase_status', 'Finish')->count();
        $jumlahPembelianBatal = Purchase::where('purchase_type', 'Reception')->where('purchase_status', 'Cancel')->count();

        $draft_pr = Purchase::query()
            ->where([['purchase_type', 'Purchase Requisition'], ['purchase_status', 'Draft']])->count();

        $draft_po = Purchase::query()
            ->where([['purchase_type', 'Purchase Order'], ['purchase_status', 'Draft']])->count();

        $draft_receipt = Purchase::query()
            ->where([['purchase_type', 'Receiption'], ['purchase_status', 'Draft']])->count();

        $draft_retur = Purchase::query()
            ->where([['purchase_type', 'Purchae Retur'], ['purchase_status', 'Draft']])->count();

        $total_pr = $this->__getTotalPr();
        $total_po_not_received = $this->__getTotalPoNotReceivedYet();
        $total_po_not_invoiced = $this->__getTotalPoNotInvoiced();

        $draft_sales = $this->__getSales();
        $successSales = DB::table('sales')->where('status', 'success')->count('id');

        //SALES CONTROLLER
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $months = [];
        $current = $sixMonthsAgo->copy();
        while ($current <= now()) {
            $months[] = $current->format('Y-m'); // Format: 2025-01, 2025-02, dst.
            $current->addMonth();
        }

        $totalPendapatanOmzet = [];
        $month6BulanTerakhir = [];

        foreach ($months as $month) {
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();

            // Omzet
            $totalOmzet = DB::table('sales_details as sd')
                ->join('sales as s', 'sd.sales_id', '=', 's.id')
                ->whereBetween('s.sale_date', [$start, $end])
                ->whereIn('s.outlet_id', $userOutets)
                ->where('s.status', 'success')
                ->where('s.is_retur', 0)
                ->sum(DB::raw('sd.final_price * sd.qty'));

            // Retur
            $totalRetur = DB::table('sales_details as sd')
                ->join('sales as s', 'sd.sales_id', '=', 's.id')
                ->whereBetween('s.sale_date', [$start, $end])
                ->whereIn('s.outlet_id', $userOutets)
                ->where('s.status', 'retur')
                ->where('s.is_retur', 1)
                ->sum(DB::raw('sd.final_price * sd.qty_retur'));

            // Diskon
            $totalDiskon = DB::table('sales')
                ->whereBetween('sale_date', [$start, $end])
                ->whereIn('outlet_id', $userOutets)
                ->where('status', 'success')
                ->where('is_retur', 0)
                ->sum('discount_amount');

            $netIncome = $totalOmzet - $totalRetur - $totalDiskon;

            $totalPendapatanOmzet[] = $netIncome;
            $month6BulanTerakhir[] = Carbon::parse($month)->translatedFormat('F'); // Nama bulan
        }

        // Balikkan agar dari bulan terlama ke terbaru
        $totalPendapatanOmzet = array_reverse($totalPendapatanOmzet);
        $month6BulanTerakhir = array_reverse($month6BulanTerakhir);

        //PRODUK TERLARIS
        // Query untuk mendapatkan 10 produk terlaris
        $topProducts = DB::table('sales_details')
            ->join('sales', 'sales.id', '=', 'sales_details.sales_id')
            ->where('sales.sale_date', '>=', $sixMonthsAgo)
            ->whereIn('sales.outlet_id', $userOutets)
            ->groupBy('sales_details.product_name')
            ->orderBy('sales_details.qty', 'desc')
            ->limit(10)
            ->get();

        // top customers
        $tcus = $this->__popularCustomers();

        // Mengubah data menjadi format yang diperlukan oleh Highcharts
        $topProductChartData = $topProducts->map(function ($item) {
            return [$item->product_name, (int) $item->qty];
        });

        $priceChange = PriceChange::query()
            ->join('products', 'product_id', 'products.id')
            ->with('user')
            ->whereBetween('date', [startOfDay(date('Y-m-d')), endOfDay(date('Y-m-d'))])
            ->whereIn('products.outlet_id', $userOutets)
            ->get();

        $settingPriceChange = Setting::where('name', 'price_change')->first();

        return view('pages.dashboard.index', compact(
            'jumlahSupplier',
            'jumlahCustomer',
            'jumlahOutlet',
            'jumlahUser',
            'jumlahProduk',
            'jumlahKategoriProduk',
            'jumlahPembelianBerhasil',
            'jumlahPembelianBatal',
            'draft_pr',
            'draft_po',
            'draft_receipt',
            'draft_retur',
            'total_pr',
            'total_po_not_received',
            'total_po_not_invoiced',
            'draft_sales',
            'totalPendapatanOmzet',
            'month6BulanTerakhir',
            'topProductChartData',
            'topProducts',
            'tcus',
            'priceChange',
            'settingPriceChange',
            'successSales',
        ));
    }

    public function getDataDashboard(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->endOfDay();
    
        // Total pendapatan kotor dari penjualan sukses (tidak termasuk retur)
        $totalOmzet = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            ->sum(DB::raw('sd.final_price * sd.qty'));

        // Total omzet dari retur (pengembalian barang)
        $totalOmzetRetur = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->where('s.status', 'retur')
            ->where('s.is_retur', 1)
            ->sum(DB::raw('sd.final_price * sd.qty_retur'));

        // Total diskon dari penjualan sukses (diambil dari tabel sales)
        $totalDiskon = DB::table('sales')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'success')
            ->where('is_retur', 0)
            ->sum('discount_amount');

        // Net Income = Pendapatan - Diskon - Omzet Retur
        $netIncome = $totalOmzet - $totalOmzetRetur - $totalDiskon;

        // Total laba dari penjualan sukses (final_price - capital_price)
        $totalProfit = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            ->sum(DB::raw('(sd.final_price - p.capital_price) * sd.qty'));

        // Total laba yang hilang karena retur
        $totalProfitReturn = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->where('s.status', 'retur')
            ->where('s.is_retur', 1)
            ->sum(DB::raw('(sd.final_price - p.capital_price) * sd.qty_retur'));

        // Net Profit
        $netProfit = $totalProfit - $totalProfitReturn;

        $totalTransactions = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            ->whereNull('s.deleted_at')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->distinct()
            ->count('s.id');

        $totalProductSold = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            ->whereNull('s.deleted_at')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->sum('sd.qty');


        // Buat subquery sales sebagai dasar
        $salesSub = DB::table('sales')
            ->select('id', 'status', 'is_retur', 'created_at', 'deleted_at', 'discount_amount', 'customer_id', 'user_id', 'payment_method_id')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$startDate, $endDate]);
    
        // Produk terlaris
        $topProduct = DB::table('sales_details as sd')
            ->joinSub($salesSub, 's', fn($join) => $join->on('sd.sales_id', '=', 's.id'))
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->select('p.name as product_name', DB::raw('COUNT(sd.id) as transaction_count'))
            ->where('s.status', 'success')
            ->where('s.is_retur', false)
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('transaction_count')
            ->limit(10)
            ->get();
    
        // Pelanggan teratas
        $topCustomers = DB::table(DB::raw("({$salesSub->toSql()}) as s"))
            ->mergeBindings($salesSub)
            ->join('customers as c', 's.customer_id', '=', 'c.id')
            ->select('c.name as customer_name', DB::raw('COUNT(s.id) as transaction_count'))
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('transaction_count')
            ->limit(10)
            ->get();
    
        // Kategori teratas
        $topCategories = DB::table('sales_details as sd')
            ->joinSub($salesSub, 's', fn($join) => $join->on('sd.sales_id', '=', 's.id'))
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('product_categories as pc', 'p.product_category_id', '=', 'pc.id')
            ->select('pc.name as category_name', DB::raw('COUNT(DISTINCT s.id) as transaction_count'))
            ->where('s.status', 'success')
            ->where('s.is_retur', false) 
            ->groupBy('pc.id', 'pc.name')
            ->orderByDesc('transaction_count')
            ->limit(5)
            ->get();
    
        // Kasir teratas
        $topCashiers = DB::table(DB::raw("({$salesSub->toSql()}) as s"))
            ->mergeBindings($salesSub)
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('roles as r', 'u.role_id', '=', 'r.id')
            ->where('r.role_name', 'KASIR')
            ->select('u.users_name as cashier_name', DB::raw('COUNT(s.id) as transaction_count'))
            ->where('s.status', 'success')
            ->groupBy('u.id', 'u.users_name')
            ->orderByDesc('transaction_count')
            ->limit(5)
            ->get();
    
        // Metode pembayaran teratas
        $topPaymentMethods = DB::table(DB::raw("({$salesSub->toSql()}) as s"))
            ->mergeBindings($salesSub)
            ->join('payment_methods as pm', 's.payment_method_id', '=', 'pm.id')
            ->select('pm.name as payment_method', DB::raw('COUNT(s.id) as transaction_count'))
            ->where('s.status', 'success')
            ->where('s.is_retur', false)
            ->groupBy('pm.id', 'pm.name')
            ->orderByDesc('transaction_count')
            ->limit(5)
            ->get();

        // Produk tidak laku (paling sedikit terjual)
        $stockSub = DB::table('product_stocks')
            ->select('product_id', DB::raw('SUM(stock_current) as remaining_stock'))
            ->groupBy('product_id');

        $leastSoldProducts = DB::table('products as p')
            ->leftJoin('sales_details as sd', 'p.id', '=', 'sd.product_id')
            ->leftJoinSub($salesSub, 's', fn($join) => $join->on('sd.sales_id', '=', 's.id'))
            ->leftJoin('product_units as u', 'p.product_unit_id', '=', 'u.id') // Join ke satuan
            ->leftJoinSub($stockSub, 'ps', 'p.id', '=', 'ps.product_id')       // Join sub stok
            ->select(
                'p.id',
                'p.barcode',
                'p.name',
                'u.symbol as unit_name',
                DB::raw('COALESCE(SUM(CASE WHEN s.status = "success" AND s.is_retur = 0 THEN sd.qty ELSE 0 END), 0) as qty_terjual'),
                DB::raw('MAX(CASE WHEN s.status = "success" AND s.is_retur = 0 THEN s.created_at ELSE NULL END) as terakhir_terjual'),
                DB::raw('COALESCE(ps.remaining_stock, 0) as remaining_stock')
            )
            ->groupBy('p.id', 'p.barcode', 'p.name', 'u.symbol', 'ps.remaining_stock')
            ->orderBy('qty_terjual', 'asc')
            ->orderBy('terakhir_terjual', 'asc')
            ->limit(5)
            ->get();
    
        return response()->json([
            'netIncome' => $netIncome,
            'netProfit' => $netProfit,
            'totalTransactions' => $totalTransactions,
            'totalProductSold' => $totalProductSold,
            'topProduct' => $topProduct,
            'topCustomers' => $topCustomers,
            'topCategories' => $topCategories,
            'topCashiers' => $topCashiers,
            'topPaymentMethods' => $topPaymentMethods,
            'leastSoldProducts' => $leastSoldProducts
        ]);
    }

    public function topProduct()
    {
        return view('pages.dashboard.top-product-details');
    }

    public function topProductData(Request $request)
    {
        $salesSub = $this->__getValidSalesSubQuery($request);

        $stockSub = DB::table('product_stocks')
            ->select('product_id', DB::raw('SUM(stock_current) as remaining_stock'))
            ->groupBy('product_id');

        $topProduct = DB::table('sales_details as sd')
            ->joinSub($salesSub, 's', fn($join) => $join->on('sd.sales_id', '=', 's.id'))
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('product_units as u', 'p.product_unit_id', '=', 'u.id')
            ->leftJoinSub($stockSub, 'ps', fn($join) => $join->on('p.id', '=', 'ps.product_id'))
            ->select(
                'p.barcode',
                'p.name as product_name',
                'u.symbol as unit_name',

                // Hitung jumlah transaksi unik (tidak perlu dikurangi retur)
                DB::raw('COUNT(DISTINCT CASE WHEN s.is_retur = 0 THEN s.id ELSE NULL END) as transaction_count'),

                // Total qty dikurangi retur
                DB::raw('
                    COALESCE(SUM(CASE WHEN s.is_retur = 0 THEN sd.qty ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN s.is_retur = 1 THEN sd.qty ELSE 0 END), 0)
                    as total_qty
                '),

                // Total sales dikurangi retur
                DB::raw('
                    COALESCE(SUM(CASE WHEN s.is_retur = 0 THEN sd.qty * sd.price ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN s.is_retur = 1 THEN sd.qty * sd.price ELSE 0 END), 0)
                    as total_sales
                '),

                // Penjualan terakhir bukan retur
                DB::raw('MAX(CASE WHEN s.is_retur = 0 THEN s.created_at ELSE NULL END) as last_sold'),

                DB::raw('COALESCE(ps.remaining_stock, 0) as remaining_stock')
            )
            ->where('s.status', 'success')
            ->groupBy('p.id', 'p.name', 'p.barcode', 'u.symbol', 'ps.remaining_stock')
            ->orderByDesc('total_qty')
            ->limit(5000)
            ->get();

        return response()->json([
            'topProduct' => $topProduct
        ]);
    }


    public function topCategory()
    {
        return view('pages.dashboard.top-category-details');
    }

    public function topCategoryData(Request $request)
    {
        $salesSub = $this->__getValidSalesSubQuery($request);

        $topCategories = DB::table('sales_details as sd')
            ->joinSub($salesSub, 's', fn($join) => $join->on('sd.sales_id', '=', 's.id'))
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('product_units as unit', 'p.product_unit_id', '=', 'unit.id')
            ->join('product_categories as pc', 'p.product_category_id', '=', 'pc.id')
            ->select(
                'pc.name as category_name',
                DB::raw("SUM(CASE WHEN s.is_retur = 0 THEN sd.qty ELSE -sd.qty END) as total_qty"),
                DB::raw("SUM(CASE WHEN s.is_retur = 0 THEN sd.qty * sd.price ELSE -sd.qty * sd.price END) as total_sales"),
                DB::raw("COUNT(DISTINCT CASE WHEN s.is_retur = 0 THEN s.id ELSE NULL END) as transaction_count"),
                DB::raw('MAX(s.created_at) as last_sold'),
                DB::raw('COUNT(DISTINCT p.id) as product_count'),
                DB::raw('unit.symbol as unit_symbol')
            )
            ->where('s.status', 'success')
            ->groupBy('pc.id', 'pc.name')
            ->orderByDesc('total_sales')
            ->limit(5000)
            ->get();

        return response()->json([
            'topCategories' => $topCategories
        ]);
    }

    public function topCustomer()
    {
        return view('pages.dashboard.top-customer-details');
    }

    public function topCustomerData(Request $request)
    {
        $salesSub = $this->__getValidSalesSubQuery($request);

        $topCustomers = DB::table('sales as s')
            ->joinSub($salesSub, 'valid_sales', fn($join) => $join->on('s.id', '=', 'valid_sales.id'))
            ->join('customers as c', 's.customer_id', '=', 'c.id')
            ->join('sales_details as sd', 's.id', '=', 'sd.sales_id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('product_units as u', 'p.product_unit_id', '=', 'u.id')
            ->select(
                'c.name as customer_name',
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN 1 WHEN s.is_retur = 1 THEN -1 ELSE 0 END) as transaction_count'),
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN sd.qty ELSE -sd.qty END) as total_qty'),
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN sd.qty * sd.price ELSE -sd.qty * sd.price END) as total_amount'),
                DB::raw('MAX(s.created_at) as last_transaction'),
                DB::raw('u.symbol as unit_symbol')
            )
            ->whereIn('s.status', ['success', 'retur']) 
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('total_amount')
            ->limit(5000)
            ->get();

        return response()->json([
            'topCustomers' => $topCustomers
        ]);
    }

    public function topCashier()
    {
        return view('pages.dashboard.top-cashier-details');
    }

    public function topCashierData(Request $request)
    {
        $salesSub = $this->__getValidSalesSubQuery($request);

        $cashierDetails = DB::table('sales as s')
            ->joinSub($salesSub, 'valid_sales', fn($join) => $join->on('s.id', '=', 'valid_sales.id'))
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('roles as r', 'u.role_id', '=', 'r.id')
            ->join('sales_details as sd', 's.id', '=', 'sd.sales_id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('product_units as unit', 'p.product_unit_id', '=', 'unit.id')
            ->select(
                'u.users_name as cashier_name',
                DB::raw('COUNT(DISTINCT s.id) as transaction_count'),
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN sd.qty ELSE -sd.qty END) as total_qty'),
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN sd.qty * sd.price ELSE -sd.qty * sd.price END) as total_sales'),
                DB::raw('MAX(s.created_at) as last_transaction'),
                DB::raw('unit.symbol as unit_symbol')
            )
            ->where('s.status', 'success')
            ->where('r.role_name', 'KASIR') 
            ->groupBy('u.id', 'u.users_name')
            ->orderByDesc('transaction_count') 
            ->limit(10)
            ->get();

        return response()->json([
            'cashierDetails' => $cashierDetails
        ]);
    }


    public function topPaymentMethod()
    {
        return view('pages.dashboard.top-payment-details');
    }

    public function topPaymentMethodData(Request $request)
    {
        $salesSub = $this->__getValidSalesSubQuery($request);

        $paymentDetails = DB::table('sales as s')
            ->joinSub($salesSub, 'valid_sales', fn($join) => $join->on('s.id', '=', 'valid_sales.id'))
            ->join('payment_methods as pm', 's.payment_method_id', '=', 'pm.id')
            ->join('sales_details as sd', 's.id', '=', 'sd.sales_id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('product_units as unit', 'p.product_unit_id', '=', 'unit.id')
            ->select(
                'pm.name as payment_method',
                // Hitung transaksi yang bukan retur
                DB::raw('COUNT(DISTINCT CASE WHEN s.is_retur = 0 THEN s.id ELSE NULL END) as transaction_count'),

                // Hitung total_qty = qty non-retur - qty retur
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN sd.qty ELSE -sd.qty END) as total_qty'),

                // Hitung total_sales = sales non-retur - sales retur
                DB::raw('SUM(CASE WHEN s.is_retur = 0 THEN sd.qty * sd.price ELSE -sd.qty * sd.price END) as total_sales'),

                DB::raw('MAX(s.created_at) as last_transaction'),
                DB::raw('unit.symbol as unit_symbol')
            )
            ->where('s.status', 'success')
            ->groupBy('pm.id', 'pm.name')
            ->orderByDesc('total_sales')
            ->get();

        return response()->json([
            'paymentDetails' => $paymentDetails
        ]);
    }

    public function leastSoldProduct()
    {
        return view('pages.dashboard.least-sold-product-details');
    }

    public function leastSoldProductData(Request $request)
    {
        $salesSub = $this->__getValidSalesSubQuery($request);

        // Subquery: total stok asli dari tabel product_stocks
        $stockSub = DB::table('product_stocks')
            ->select('product_id', DB::raw('SUM(stock_current) as remaining_stock'))
            ->groupBy('product_id');

        $leastSoldProducts = DB::table('products as p')
            ->leftJoin('sales_details as sd', 'p.id', '=', 'sd.product_id')
            ->leftJoinSub($salesSub, 's', function ($join) {
                $join->on('sd.sales_id', '=', 's.id');
            })
            ->leftJoin('product_categories as pc', 'p.product_category_id', '=', 'pc.id')
            ->leftJoin('product_units as u', 'p.product_unit_id', '=', 'u.id')
            ->leftJoin('product_prices as pp', function ($join) {
                $join->on('p.id', '=', 'pp.product_id')
                    ->where('pp.type', '=', 'utama')
                    ->whereNull('pp.deleted_at');
            })
            ->leftJoinSub($stockSub, 'ps', 'p.id', '=', 'ps.product_id')
            ->select(
                'p.id',
                'p.name as product_name',
                'pc.name as category_name',
                'u.symbol as unit_name',
                'pp.price as sell_price',
                // Hitung total_sold dikurangi retur
                DB::raw('
                    COALESCE(SUM(CASE WHEN s.status = "success" AND s.is_retur = 0 THEN sd.qty ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN s.status = "success" AND s.is_retur = 1 THEN sd.qty ELSE 0 END), 0)
                    as total_sold
                '),
                // Tanggal penjualan terakhir (bukan retur)
                DB::raw('MAX(CASE WHEN s.status = "success" AND s.is_retur = 0 THEN s.created_at ELSE NULL END) as last_sold'),
                // Stok hanya dari tabel product_stocks
                DB::raw('COALESCE(ps.remaining_stock, 0) as remaining_stock')
            )
            ->groupBy('p.id', 'p.name', 'pc.name', 'u.symbol', 'pp.price', 'ps.remaining_stock')
            ->orderBy('total_sold', 'asc')
            ->orderBy('remaining_stock', 'desc')
            ->limit(1000)
            ->get();

        return response()->json([
            'leastSoldProducts' => $leastSoldProducts
        ]);
    }

    public function outOfStock()
    {
        $userRole = Auth::user()->role->role_name;

        $query = Product::join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
            ->join('outlets', 'outlets.id', '=', 'product_stocks.outlet_id')
            ->join('product_units', 'product_units.id', '=', 'products.product_unit_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->whereColumn('product_stocks.stock_current', '<=', 'products.minimum_stock');

        if ($userRole !== 'SUPERADMIN') {
            $query->where('product_stocks.outlet_id', getOutletActive()->id);
        }

        $stockMinimum = $query->limit(5000)->get([
            'products.barcode',
            'products.name',
            'product_categories.name as category_name',
            'products.minimum_stock',
            'product_stocks.stock_current',
            'outlets.name as outlet_name',
            'product_units.symbol as unit_name'
        ]);

        return view('pages.dashboard.out-of-stock-details', compact('stockMinimum'));
    }


    protected function __getValidSalesSubQuery(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->endOfDay();

        return DB::table('sales')
            ->select(
                'id',
                'status',
                'is_retur',
                'created_at',
                'deleted_at',
                'discount_amount',
                'customer_id',
                'user_id',
                'payment_method_id'
            )
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    private function __getTotalPr()
    {
        $query = DB::table('purchases as p')
            ->whereRaw('p.id IN (
                SELECT CONCAT(purchase_id)
                FROM purchase_details
                LEFT JOIN purchases ON purchase_id=p.id
                WHERE purchase_po_id IS NULL
            )')
            ->select(
                DB::raw('COUNT(p.id) as total_data'),
                DB::raw('MIN(purchase_date) as start'),
                DB::raw('MAX(purchase_date) as end'),
            )->groupBy('p.id')->first();

        return $query;
    }

    private function __getTotalPoNotReceivedYet()
    {
        $query = DB::table('purchases as p')
            ->where([['p.purchase_status', 'Finish'], ['p.purchase_type', 'Purchase Order'], ['p.ref_code', NULL]])
            ->whereRaw('p.id IN (
                SELECT CONCAT(purchase_id)
                FROM purchase_details
                LEFT JOIN purchases ON purchase_id=p.id
                WHERE status = "Purchase Order"
            )')
            ->select(
                DB::raw('COUNT(p.id) as total_data'),
                DB::raw('MIN(purchase_date) as start'),
                DB::raw('MAX(purchase_date) as end'),
            )->groupBy('p.id')->first();

        return $query;
    }

    private function __getTotalPoNotInvoiced()
    {
        $query = DB::table('purchases as p')
            ->where([['p.purchase_status', 'Finish'], ['p.purchase_type', 'Purchase Order'], ['p.purchase_invoice_id', NULL]])
            ->select(
                DB::raw('COUNT(p.id) as total_data'),
                DB::raw('MIN(purchase_date) as start'),
                DB::raw('MAX(purchase_date) as end'),
            )->groupBy('p.id')->first();

        return $query;
    }

    private function __getSales()
    {
        $draft = Sales::whereRaw('date(sale_date) = curdate()')->where('status', 'draft')->count();
        return $draft;
    }

    public function __popularCustomers()
    {
        $query = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('COUNT(sales.id) as total'), 'customers.address')
            ->whereIn('sales.outlet_id', getUserOutlet())
            ->groupBy('sales.customer_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();


        $data = [];
        foreach ($query as $record) {
            $data[] = [
                'name' => $record->name,
                'address' => $record->address,
                'total' => $record->total
            ];
        }

        return $data;
    }
}
