<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;

use App\Models\ProductStock;
use App\Models\ProductUnit;
use App\Models\ProfilCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOutletReportController extends Controller
{
    //OUTLET STOCK
    public function indexStockOutlet(Request $request)
    {
        if ($request->ajax()) {
            $productStock = $this->queryStockCurrentOutlet($request);

            $productStock->map(function ($row) {
                $row->stock_current = floatval($row->stock_current) . ' ' . $row->product_unit_symbol;
                $units = ProductUnit::where('base_unit_id', $row->product_unit_id)->get();
                $row->product_unit_preview = '';
                foreach ($units as $unit) {
                    $conversionQty = floatval($row->stock_current) * $unit->conversion_rate;
                    $row->product_unit_preview .= $conversionQty . ' ' . $unit->name . ' | ';
                }
                return $row;
            });

            return DataTables::of($productStock)
                ->addIndexColumn()
                ->make(true);
        }

        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            $inventory = Outlet::all();
        } else {
            $inventory = Outlet::whereIn('id', getUserOutlet())->get();
        }

        $company = ProfilCompany::where('status', 'active')->first();

        return view('pages.report.stock_outlet.index',
            [
                'inventory' => $inventory,
                'company'   => $company
            ]
        );
    }

    public function printStockOutlet(Request $request)
    {
        $inventory = Outlet::find($request->inventory_id);

        // if ($request->history_date == date('Y-m-d')) {
        $stocks = $this->queryStockCurrentOutlet($request);
        // } else {
        //     $stocks = $this->queryStockByDateOutlet($request);
        // }


        $stocks->map(function ($row) {
            $row->stock_current = numberGroupComma($row->stock_current);
            return $row;
        });

        $company = ProfilCompany::where('status', 'active')->first();

        $data = [
            'stock'     => $stocks,
            'company'   => $company,
            'inventory' => $inventory ? $inventory->name : '',
            'stockDate' => dateStandar(date('Y-m-d'))
        ];

        return view('pages.report.stock_outlet.print', $data);
    }

    private function queryStockCurrentOutlet($request)
    {

        return ProductStock::query()
            ->join('products as p', 'product_stocks.product_id', 'p.id')
            ->join('product_units as pu', 'p.product_unit_id', 'pu.id')
            ->join('product_categories as pc', 'p.product_category_id', 'pc.id')
            ->select(
                'product_stocks.stock_current',
                'p.barcode as barcode',
                'p.name as product_name',
                'pu.name as product_unit',
                'pu.symbol as product_unit_symbol',
                'pc.name as product_category',
                'p.product_unit_id as product_unit_id',

            )
            ->where(function ($query) use ($request) {
                if ($request->product != '') {
                    $query->where('p.name', 'LIKE', '%' . $request->product . '%');
                    $query->orWhere('p.barcode', 'LIKE', '%' . $request->product . '%');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->inventory_id != '') {
                    $query->where('product_stocks.outlet_id', $request->inventory_id);
                }

                if ($request->qty != '-') {

                    if ($request->qty == 0) {
                        $query->where('stock_current', '<', 1);
                    } else {
                        $query->where('stock_current', '>', 0);
                    }
                }

                $query->where('p.deleted_at', null);
            })
            ->limit(1000)
            ->orderBy('p.name')
            ->get();
    }

    private function queryStockByDateOutlet(Request $request)
    {

        $date = Carbon::parse($request->history_date);
        $date = $date->addDay();


        $historyDate = startOfDay($date);

        $subqueryBeforeDate = DB::table('product_stock_histories')
            ->select('product_id', DB::raw('MAX(history_date) as max_history'))
            ->where([['history_date', '<=', $historyDate], ['outlet_id', $request->inventory_id]])
            ->groupBy('product_id');

        $latestStocks = DB::table('product_stock_histories as psh')
            ->joinSub($subqueryBeforeDate, 'max_dates', function ($join) {
                $join->on('psh.product_id', '=', 'max_dates.product_id')
                    ->on('psh.history_date', '=', 'max_dates.max_history');
            })
            ->join('products as p', 'psh.product_id', 'p.id')
            ->join('product_units as pu', 'p.product_unit_id', 'pu.id')
            ->join('product_categories as pc', 'p.product_category_id', 'pc.id')
            ->select(
                'p.barcode as barcode',
                'p.name as product_name',
                'stock_after as stock_current',
                'pu.name as product_unit',
                'pc.name as product_category'
            )
            ->where('psh.outlet_id', $request->inventory_id);

        return $latestStocks->orderBy('barcode')->get()->filter(function ($stock) use ($request) {
            if ($request->qty != '-') {

                if ($request->qty == 0) {
                    return $stock->stock_current < 1;
                } else {
                    return $stock->stock_current > 0;
                }
            }

            return $stock;
        });
    }
}
