<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ProductCategory;
use App\Exports\SalesDataExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
    public $start_date;
    public $end_date;
    public $outlet;
    public $status;
    public $summary;
    public $users;
    public $selectedUser;
    public $cashier;
    public $selectedCashier;
    public $selectedOutlet;


    public function overviewSales()
    {
        return view('pages.sales.report.overview');
    }
    public function reportSales(Request $request)
    {
        $user = auth()->user();

        $selectedOutlet = $request->input('outlet_id');

        if ($user->role->role_name === 'SUPERADMIN') {

            $userSales = DB::table('sales')
                ->distinct()
                ->join('users', 'sales.user_id', '=', 'users.id')
                ->select('sales.user_id', 'users.users_name')
                ->get();

            $outlets = Outlet::orderBy('name')->get();

        } else {
            $userSales = DB::table('sales')
                ->distinct()
                ->join('users', 'sales.user_id', '=', 'users.id')
                ->select('sales.user_id', 'users.users_name')
                ->where('sales.outlet_id', getUserOutlet())
                ->get();

            $outlets = Outlet::whereIn('id', getUserOutlet())->orderBy('name')->get();
        }

        $office = profileCompany();
        $customers = Customer::orderBy('name')->get();
        $productCategory = ProductCategory::query()
            ->where('is_parent_category', true)
            ->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $data = [
            'office'    => $office,
            'outlet'    => $outlets,
            'users'     => $userSales,
            'customers' => $customers,
            'category'  => $productCategory,
            'payments'  => PaymentMethod::orderBy('name')->get(),
            'suppliers' => $suppliers,
            'products'  => $products,
            'selectedOutlet' => $selectedOutlet ?? null,
        ];

        return view('pages.sales.report', $data);
    }

    
    public function generateExcel(Request $request)
    {
        try {
            $data = $this->dataconvertExcel($request)->toArray();

            $fileName = $this->generateFileName($request);

            return Excel::download(new SalesDataExport($data, $request->report_type), $fileName);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function dataconvertExcel(Request $request)
    {
        switch ($request->report_type) {
            case 'DETAIL':
                $data = $this->byDetail($request);
                break;
            case 'SUMMARY':
                $data = $this->bySummary($request);
                break;
            case 'USER':
                $data = $this->querySummaryUser($request);
                break;
            case 'CUSTOMER':
                $data = $this->bySummaryCustomer($request);
                break;
            case 'CATEGORY':
                $data = $this->bySummaryCategoryExcel($request);
                break;
            case 'SIMPLE':
                $data = $this->simple($request);
                break;
            default:
                $data = $this->byDetail($request);
                break;
        }
        return $data;
    }
    public function generateFileName(Request $request)
    {
        $date = now()->format('YmdHis');

        switch ($request->report_type) {
            case 'DETAIL':
                $fileName = "detail_report_{$date}.xlsx";
                break;
            case 'SUMMARY':
                $fileName = "summary_report_{$date}.xlsx";
                break;
            case 'USER':
                $fileName = "user_report_{$date}.xlsx";
                break;
            case 'CUSTOMER':
                $fileName = "customer_report_{$date}.xlsx";
                break;
            case 'CATEGORY':
                $fileName = "category_report_{$date}.xlsx";
                break;
            case 'SIMPLE':
                $fileName = "simple_report_{$date}.xlsx";
                break;
            default:
                $fileName = "default_report_{$date}.xlsx";
                break;
        }

        return $fileName;
    }

    public function dataSales(Request $request)
    {
        switch ($request->report_type) {
            case 'SUMMARY':
                $data = $this->bySummary($request);
                $view = 'pages.sales.report.summary';
                break;

            case 'USER':

                if ($request->user_id != '') {
                    $data = $this->bySummarySpesificUser($request);
                    $view = 'pages.sales.report.user';
                } else {
                    $data = $this->bySummaryAllUser($request);
                    $view = 'pages.sales.report.user-summary';
                }
                break;

            case 'CUSTOMER':

                $data = $this->bySummaryCustomer($request);
                $view = 'pages.sales.report.customer';
                break;

            case 'CUSTOMER_REGION':

                $data = $this->byCustomerRegion($request);
                $view = 'pages.sales.report.customer-region';
                break;

            case 'CATEGORY':

                if ($request->product_category_id != '') {
                    $data = $this->bySummarySpesificCategory($request);
                    $view = 'pages.sales.report.summary-category-spesific';

                } else {
                    $data = $this->bySummaryCategory($request);
                    $view = 'pages.sales.report.summary-category';
                }

                break;

            case 'SIMPLE':
                $data = $this->simple($request);
                $view = 'pages.sales.report.simple';
                break;

            case 'SUPPLIER':
                $data = $this->bySummarySupplier($request);
                $view = 'pages.sales.report.supplier';
                break;

            case 'PRODUCT':
                if ($request->product_id != '') {
                    $data = $this->bySummarySpecificProduct($request);
                    $view = 'pages.sales.report.summary-product-specific';
                } else {
                    $data = $this->bySummaryProduct($request);
                    $view = 'pages.sales.report.summary-product';
                }
                break;

            default:
                $data = $this->byDetail($request);
                $view = 'pages.sales.report.detail';
                break;
        }

        $data = [
            'data' => $data,
            'company' => profileCompany(),
        ];

        return view($view, $data);
    }

    private function byDetail($request)
    {
        return Sales::query()
            ->with(
                'salesDetails',
                'customer',
                'outlet',
                'user',
            )
            ->where('status', 'success')
            ->whereNot('is_retur', 1)
            // ->whereIn('sales.outlet_id', getUserOutlet()) ini digunakan sebelum implementasi multioutlet
            ->where(function ($query) use ($request) {
                if ($request->start_date != '' && $request->end_date != '') {
                    $query->whereBetween('sale_date', [
                        Carbon::parse($request->start_date)->startOfDay(),
                        Carbon::parse($request->end_date)->endOfDay()
                    ]);
                } else {
                    $query->whereBetween('sale_date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
                }

                if ($request->outlet_id != '' && $request->outlet_id != '-') {
                    $query->where('outlet_id', $request->outlet_id);
                }
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->get();
    }

    private function bySummary($request)
    {
        return $this->querySummaryTrans($request);
    }

    private function bySummarySpesificUser($request)
    {
        return $this->querySummaryUser($request);
    }

    private function bySummaryAllUser($request)
    {
        $summary = DB::table('cashflow_close')
            ->select(
                'u.users_name',
                DB::raw('SUM(income_amount) as income_amount'),
                DB::raw('SUM(capital_amount) as capital_amount'),
                DB::raw('SUM(real_amount) as real_amount'),
                DB::raw('SUM(difference) as difference'),
            )
            ->join('users as u', 'user_id', 'u.id')
            ->where([
                ['date', '>=', startOfDay($request->start_date)],
                ['date', '<=', endOfDay($request->end_date)],
            ])
            // ->whereIn('cashflow_close.outlet_id', getUserOutlet())
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('cashflow_close.outlet_id', $request->outlet_id);
            })
            ->groupBy('u.users_name')
            ->get();

        return [
            'summary' => $summary,
            'start_date' => Carbon::parse($request->start_date)->translatedFormat('d F Y'),
            'end_date' => Carbon::parse($request->end_date)->translatedFormat('d F Y'),
        ];
    }


    private function bySummaryCustomer($request)
    {
        return $this->querySummaryCustomer($request);
    }

    private function querySummaryCustomer($request)
    {
        return SalesDetail::join('sales', 'sales_details.sales_id', 'sales.id')
            ->leftJoin('customers', 'sales.customer_id', 'customers.id')
            ->join('users', 'sales.user_id', 'users.id')
            ->join('payment_methods', 'sales.payment_method_id', 'payment_methods.id')
            ->select(
                'sales.sale_code',
                'sales.sale_date',
                'sales.user_id',
                'sales.customer_id',
                'users.username as user',
                'customers.code as customer_code',
                'customers.name as customer',
                'product_name',
                'price',
                'sales_details.discount',
                'sales_details.final_price',
                'sales_details.qty',
                'sales_details.subtotal',
                'final_amount',
                'payment_methods.name as payment_method'
            )
            ->where('sales.status', 'success')
            // ->whereIn('sales.outlet_id', getUserOutlet())
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('sales.outlet_id', $request->outlet_id);
            })
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('sales.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('sales.payment_method_id', $request->payment_method_id);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                $query->where('sales.customer_id', $request->customer_id);
            })
            ->get();
    }


    private function querySummaryTrans($request)
    {
        return DB::table('sales_details as sd')
            ->join('sales as sl', 'sd.sales_id', 'sl.id')
            ->leftJoin('customers as c', 'sl.customer_id', 'c.id')
            ->join('users as u', 'sl.user_id', 'u.id')
            ->join('payment_methods as pm', 'sl.payment_method_id', 'pm.id')
            ->select(
                'sl.sale_code',
                'sl.sale_date',
                'sl.user_id',
                'sl.customer_id',
                'sd.is_retur',
                'u.username as user',
                'c.code as customer_code',
                'c.name as customer',
                'product_name',
                'price',
                'sd.discount',
                'sd.final_price',
                'qty',
                'sd.subtotal',
                'final_amount',
                'pm.name as payment_method',

            )
            ->where('sl.status', 'success')
            // ->whereIn('sl.outlet_id', getUserOutlet())
            ->whereNot('sl.is_retur', 1)
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('sl.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('sl.outlet_id', $request->outlet_id);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->get();
    }

    private function querySimpleSales($request)
    {
        return DB::table('sales as sl')
            ->leftJoin('customers as c', 'sl.customer_id', 'c.id')
            ->join('users as u', 'sl.user_id', 'u.id')
            ->join('payment_methods as pm', 'sl.payment_method_id', 'pm.id')
            ->select(
                'sl.sale_code',
                'sl.sale_date',
                'sl.user_id',
                'sl.customer_id',
                'u.username as user',
                'c.code as customer_code',
                'c.name as customer',
                'final_amount',
                'pm.name as payment_method',
            )
            ->where('sl.status', 'success')
            // ->whereIn('sl.outlet_id', getUserOutlet())
            ->whereNot('sl.is_retur', 1)
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('sl.outlet_id', $request->outlet_id);
            })
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('sl.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->get();
    }

    private function querySummaryUser($request)
    {
        return DB::table('sales_details as sd')
            ->join('sales as sl', 'sd.sales_id', 'sl.id')
            ->join('products as p', 'sd.product_id', 'p.id')
            ->join('users as u', 'sl.user_id', 'u.id')
            ->join('outlets as o', 'sl.outlet_id', 'o.id')
            ->select(
                'sl.id as sales_id',
                'sl.sale_code as code', 
                'sl.sale_date as transaction_date', 
                'p.name as product_name',
                'sd.qty as qty',
                'p.capital_price as buy_price',
                'sd.price as price',
                'sd.discount as discount',
                'sd.subtotal as subtotal',
                'sd.profit as profit',
                'sd.subtotal as final_subtotal',
                'u.users_name as user_name',
            )
            ->where('sl.status', 'success')
            // ->whereIn('sl.outlet_id', getUserOutlet())
            ->whereNot('sl.is_retur', 1)
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('sl.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->user_id, function ($query) use ($request) {
                $query->where('sl.user_id', $request->user_id);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->get();
    }

    private function bySummaryCategory($request)
    {
        $data = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', 's.id')
            ->join('products as p', 'sd.product_id', 'p.id')
            ->join('product_categories as pc', 'p.product_category_id', 'pc.id')
            // ->where('pc.is_parent_category', 1)
            ->select(
                'pc.name as product_category',
                'p.product_category_id',
                DB::raw('SUM(qty) as qty'),
                DB::raw('SUM(p.capital_price*qty) as subtotal_hpp'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(sd.profit) as final_profit')
            )
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('s.outlet_id', $request->outlet_id);
            })
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->groupBy('pc.name', 'p.product_category_id')
            ->orderBy('pc.name')
            ->get();

        if ($request->start_date === $request->end_date) {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y');
        } else {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y') .
                ' - ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        }
        return [$data, $label];
    }


    private function bySummarySpesificCategory($request)
    {
        $data = DB::table('sales_details as sd')
            ->select(
                'p.name',
                'pc.name as product_category',
                DB::raw('SUM(qty) as qty'),
                DB::raw('SUM(p.capital_price*qty) as subtotal_hpp'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(sd.profit) as final_profit')
            )
            ->join('sales as s', 'sd.sales_id', 's.id')
            ->join('products as p', 'sd.product_id', 'p.id')
            ->join('product_categories as pc', 'p.product_category_id', 'pc.id')
            ->where(function($query) use($request) {
                $query->where('pc.id', $request->product_category_id);
                $query->orWhere('pc.parent_id', $request->product_category_id);
            })
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('s.outlet_id', $request->outlet_id);
            })
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->groupBy('p.name')
            ->get();

        if ($request->start_date === $request->end_date) {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y');
        } else {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y') .
                ' - ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        }
        return [$data, $label];
    }



    private function bySummaryCategoryExcel($request)
    {
        return DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', 's.id')
            ->join('products as p', 'sd.product_id', 'p.id')
            ->join('product_categories as pc', 'p.product_category_id', 'pc.id')
            ->where('pc.is_parent_category', 1)
            ->select(
                'pc.name as product_category',
                'p.product_category_id',
                DB::raw('SUM(qty) as qty'),
                DB::raw('SUM(p.capital_price*qty) as subtotal_hpp'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(sd.profit) as final_profit')
            )
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->groupBy('pc.name', 'p.product_category_id')->get();
    }


    public function queryVoid($request)
    {
        $user = auth()->user();
    
        $query = SalesDetail::with('user', 'outlet')
            ->join('sales', 'sales_id', '=', 'sales.id')
            ->where('sales_details.status', 'void')
            ->where(function ($query) use ($request) {
                $query->whereDate('sales.created_at', '>=', $request->start_date)
                      ->whereDate('sales.created_at', '<=', $request->end_date);
            });
    
        if ($user->role->role_name === 'SUPERADMIN') {
            return $query;
        }
    
        // $query->whereIn('sales.outlet_id', getUserOutlet());
    
        return $query;
    }

    public function reportVoid()
    {
        return view('pages.sales.report.void');
    }

    public function reportVoidPrint(Request $request)
    {
        return view('pages.sales.report.void-print', [
            'data' => $this->queryVoid($request)->get(),
            'company' => profileCompany(),
            'start_date' => Carbon::parse($request->start_date)->translatedFormat('d F Y'),
            'end_date' => Carbon::parse($request->end_date)->translatedFormat('d F Y'),
        ]);
    }

    public function simple($request)
    {
        return $this->querySimpleSales($request);
    }

    private function bySummarySupplier($request)
    {
        $data = DB::table('sales_details as sd')
            ->select(
                'p.name',
                'su.name as supplier_name',
                'su.id',
                DB::raw('SUM(qty) as qty'),
                DB::raw('SUM(p.capital_price*qty) as subtotal_hpp'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(sd.profit) as final_profit')
            )
            ->join('sales as s', 'sd.sales_id', 's.id')
            ->join('products as p', 'sd.product_id', 'p.id')
            ->join('product_suppliers as ps', 'p.id', 'ps.product_id')
            ->join('suppliers as su', 'ps.supplier_id', 'su.id')
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('s.outlet_id', $request->outlet_id);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->when($request->supplier_id, function ($query) use ($request) {
                $query->where('su.id', $request->supplier_id);
            })
            ->groupBy('p.name', 'su.name', 'su.id')
            ->orderBy('su.name')
            ->orderBy('p.name')
            ->get();

        if ($request->start_date === $request->end_date) {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y');
        } else {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y') .
                ' - ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        }
        return [$data, $label];
    }

    private function byCustomerRegion($request)
    {
        $data = DB::table('sales as s')
            ->select(
                'iv.name as village_name',
                'id.name as district_name',
                'ic.name as city_name',
                DB::raw('COUNT(s.id) as sales_count'),
                DB::raw('COUNT(DISTINCT c.id) as customer_count'),
                DB::raw('SUM(s.final_amount) as sales_total')
            )
            ->leftJoin('customers as c', 's.customer_id', 'c.id')
            ->leftJoin('indonesia_villages as iv', 'c.village_code', 'iv.code')
            ->leftJoin('indonesia_districts as id', 'c.district_code', 'id.code')
            ->leftJoin('indonesia_cities as ic', 'c.city_code', 'ic.code')
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('s.outlet_id', $request->outlet_id);
            })
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->payment_method_id, function ($query) use ($request) {
                $query->where('payment_method_id', $request->payment_method_id);
            })
            ->groupBy('iv.name', 'id.name', 'ic.name')
            ->orderBy('iv.name')
            ->get();


        if ($request->start_date === $request->end_date) {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y');
        } else {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y') .
                ' - ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        }

        return [$data, $label];

    }

    private function bySummaryProduct($request)
    {
        $data = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('outlets as o', 's.outlet_id', '=', 'o.id')
            ->select(
                'o.id as outlet_id',
                'o.name as outlet_name',
                'p.name as product_name',
                DB::raw('SUM(sd.qty) as total_quantity'),
                DB::raw('SUM(sd.price * sd.qty) as total_sales')
            )
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('s.outlet_id', $request->outlet_id);
            })
            ->groupBy('o.id', 'o.name', 'p.name')
            ->orderBy('o.name', 'asc')
            ->orderBy('total_sales', 'desc')
            ->get();
    
            $groupedData = $data->groupBy('outlet_id')->map(function ($group) {
            $outletName = $group->first()->outlet_name;
            $products = $group->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'total_quantity' => $item->total_quantity,
                    'total_sales' => $item->total_sales
                ];
            });
            return compact('outletName', 'products');
        });
    
        return $groupedData;
    }

    private function bySummarySpecificProduct($request)
    {
        $data = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('outlets as o', 's.outlet_id', '=', 'o.id')
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'o.name as outlet_name',
                DB::raw('SUM(sd.qty) as total_quantity'),
                DB::raw('SUM(sd.price * sd.qty) as total_sales'),
                DB::raw('SUM(sd.profit) as total_profit'),
                DB::raw('GROUP_CONCAT(JSON_OBJECT(
                    "sale_date", s.sale_date,
                    "sale_code", s.sale_code,
                    "qty", sd.qty,
                    "price", sd.price,
                    "subtotal", sd.subtotal,
                    "profit", sd.profit
                )) as sales_details')
            )
            ->where('s.status', 'success')
            ->where('s.is_retur', 0)
            // ->whereIn('s.outlet_id', getUserOutlet())
            ->when($request->start_date != '' && $request->end_date != '', function ($query) use ($request) {
                $query->whereBetween('s.sale_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            })
            ->when($request->outlet_id, function ($query) use ($request) {
                $query->where('s.outlet_id', $request->outlet_id);
            })
            ->when($request->product_id, function ($query) use ($request) {
                $query->where('p.id', $request->product_id);
            })
            ->groupBy('p.id', 'p.name', 'o.name')
            ->orderBy('total_sales', 'desc')
            ->get();
    
        foreach ($data as $row) {
            $row->salesDetails = json_decode('[' . $row->sales_details . ']');
        }
    
        if ($request->start_date === $request->end_date) {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y');
        } else {
            $label = Carbon::parse($request->start_date)->translatedFormat('d M Y') .
                ' - ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        }
        
    
        return [$data, $label];
    }

    public function getCashierByOutlet(Request $request) 
    {
        $outletId = $request->input('outlet_id');
    
        $cashier = DB::table('sales') 
            ->distinct()
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select('sales.user_id', 'users.users_name')
            ->where('sales.outlet_id', $outletId)
            ->get();
    
        return response()->json($cashier); 
    }

    public function getProductByOutlet(Request $request)
    {
        $outletId = $request->input('outlet_id');
    
        $product = DB::table('sales_details') 
            ->distinct()
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->select('sales_details.product_id', 'products.name')
            ->where('sales_details.outlet_id', $outletId)
            ->get();
    
        return response()->json($product);
    }
    
}
