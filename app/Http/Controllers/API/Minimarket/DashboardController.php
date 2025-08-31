<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Http\Controllers\Controller;
use App\Models\SalesDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        switch ($request->type) {
            case 'daily':
                $typeLabel = 'Hari ini';
                $start_date = Carbon::now()->startOfDay();
                $end_date = Carbon::now()->endOfDay();
                break;

            case 'weekly':
                $typeLabel = 'Minggu ini';
                $start_date = Carbon::now()->subWeek();
                $end_date = Carbon::now()->endOfDay();
                break;

            default:
                //this month
                $typeLabel = 'Bulan ini';
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
        }

        $query = DB::table('sales')
            // ->join('payment_methods as pm', 'sales.payment_method_id', '=', 'pm.id')
            ->select('id', 'sale_date', 'final_amount', 'payment_method_id')
            ->whereBetween('sales.sale_date', [$start_date, $end_date])
            ->get();



        $query_details = SalesDetail::whereBetween('created_at', [$start_date, $end_date])->get();
        $success = true;
        $message = 'Success';
        $data = [
            'amount' => readable_number($query->sum('final_amount')),
            'jml_transaksi' => number_format($query->count()),
            'profit' => readable_number($query_details->sum('profit')),
            // 'statistic' => $this->queryStatistic(),
            'popular_products' => $this->popularProducts(),
            'popular_payment_method' => $this->popularPaymentMethod(),
            'popular_categories' => $this->popularCategories(),
            'popular_customers' => $this->popularCustomers(),
            'popular_outlets' => $this->popularOutlets(),
            'type' => $request->type,
            'type_label' => $typeLabel,
        ];

        return responseAPI($success,  $message, $data);
    }

    public function chartData()
    {
        return $this->queryStatistic();
    }

    public function queryStatistic()
    {
        // Mendapatkan tanggal 6 bulan sebelum tanggal sekarang
        $end_date = Carbon::now();
        $start_date = $end_date->copy()->subMonths(6)->startOfMonth();

        // Query untuk mendapatkan final amount setiap bulan selama 6 bulan terakhir
        $monthly_final_amount = DB::table('sales')
            ->select(
                DB::raw('DATE_FORMAT(sale_date, "%Y-%m") as month'),
                DB::raw('SUM(final_amount) as total_final_amount')
            )
            ->whereBetween('sale_date', [$start_date, $end_date])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        //    respon api
        $statisticData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'backgroundColor' => '#6777ef',
                    'data' => []
                ]
            ]
        ];

        // Looping untuk mengisi data labels dan data datasets

        foreach ($monthly_final_amount as $record) {
            $statisticData['labels'][] = $record->month;
            $statisticData['datasets'][0]['data'][] = $record->total_final_amount;
        }
        return $statisticData;
    }


    public function popularProducts()
    {
        $query = DB::table('sales_details')
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sales_details.qty) as total'))
            ->groupBy('sales_details.product_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $data = [];
        foreach ($query as $record) {
            $data[] = [
                'name' => $record->name,
                'total' => $record->total
            ];
        }

        return $data;
    }

    public function popularCustomers()
    {
        $query = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('COUNT(sales.customer_id) as total'))
            ->groupBy('sales.customer_id')
            ->orderBy('total', 'desc')
            ->limit(2)
            ->get();

        $data = [];
        foreach ($query as $record) {
            $data[] = [
                'name' => $record->name,
                'total' => $record->total
            ];
        }

        return $data;
    }

    public function popularCategories()
    {
        $query = DB::table('sales_details')
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->select('product_categories.name', DB::raw('SUM(sales_details.qty) as total'))
            ->groupBy('products.product_category_id')
            ->orderBy('total', 'desc')
            ->limit(2)
            ->get();

        $data = [];
        foreach ($query as $record) {
            $data[] = [
                'name' => $record->name,
                'total' => $record->total
            ];
        }

        return $data;
    }

    public function popularPaymentMethod()
    {
        $query = DB::table('sales')
            ->join('payment_methods as pm', 'sales.payment_method_id', '=', 'pm.id')
            ->select('pm.name', DB::raw('COUNT(sales.payment_method_id) as total'))
            ->groupBy('sales.payment_method_id')
            ->orderBy('total', 'desc')
            ->limit(2)
            ->get();

        $data = [];
        foreach ($query as $record) {
            $data[] = [
                'name' => $record->name,
                'total' => $record->total
            ];
        }

        return $data;
    }

    public function popularOutlets()
    {
        $query = DB::table('sales')
            ->join('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->select('outlets.name', DB::raw('COUNT(sales.outlet_id) as total'))
            ->groupBy('sales.outlet_id')
            ->orderBy('total', 'desc')
            ->limit(2)
            ->get();

        $data = [];
        foreach ($query as $record) {
            $data[] = [
                'name' => $record->name,
                'total' => $record->total
            ];
        }

        return $data;
    }
}
