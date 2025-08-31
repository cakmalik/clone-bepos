<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Models\Outlet;
use App\Models\Inventory;
use App\Models\ProductUnit;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use App\Models\ProductCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\StockValueReport as Model;

class StockValueReportController extends Controller
{
    public function indexStockGudang(Request $request)
    {
        if ($request->ajax()) {

            if ($request->history_date == date('Y-m-d') || $request->history_date == null || $request->history_date == 'undefined') {
                $productStock = $this->queryStockCurrentGudang($request);
            } else {
                $productStock = $this->queryStockByDateGudang($request);
            }

            $productStock->map(function ($row) {
                $row->stock_current = numberGroupComma($row->stock_current) . ' ' . $row->unit_symbol;
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

        if (auth()->user()->role->role_name == ('SUPERADMIN')) {
            $inventory = Inventory::all();
        } else {
            $inventory = Inventory::whereIn('id', getUserInventory())->get();
        }

        $company = ProfilCompany::where('status', 'active')->first();

        return view('pages.report.stock_gudang.index', [
            'inventory' => $inventory,
            'company'   => $company,
        ]);
    }

    public function printStockGudang(Request $request)
    {
        $inventory = Inventory::find($request->inventory_id);

        if ($request->history_date == date('Y-m-d') || $request->history_date == null || $request->history_date == 'undefined') {
            $stocks = $this->queryStockCurrentGudang($request);
        } else {
            $stocks = $this->queryStockByDateGudang($request);
        }

        $stocks->map(function ($row) {
            $row->stock_current = numberGroupComma($row->stock_current);
            return $row;
        });

        $company = ProfilCompany::where('status', 'active')->first();

        $data = [
            'stock'     => $stocks,
            'company'   => $company,
            'inventory' => $inventory ? $inventory->name : '',
            'stockDate' => dateStandar(date('Y-m-d')),
        ];

        return view('pages.report.stock_gudang.print', $data);
    }

    private function queryStockCurrentGudang($request)
    {

        return ProductStock::query()
            ->join('products as p', 'product_stocks.product_id', 'p.id')
            ->join('product_units as pu', 'p.product_unit_id', 'pu.id')
            ->join('product_categories as pc', 'p.product_category_id', 'pc.id')
            ->select(
                'product_stocks.stock_current',
                'p.barcode as product_code',
                'p.name as product_name',
                'pu.name as product_unit',
                'pu.symbol as unit_symbol',
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
                    $query->where('inventory_id', $request->inventory_id);
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
            ->orderBy('p.code')
            ->get();
    }

    private function queryStockByDateGudang(Request $request)
    {

        $date = Carbon::parse($request->history_date);
        $date = $date->addDay();

        $historyDate = startOfDay($date);

        $subqueryBeforeDate = DB::table('product_stock_histories')
            ->select('product_id', DB::raw('MAX(history_date) as max_history'))
            ->where([['history_date', '<=', $historyDate], ['inventory_id', $request->inventory_id]])
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
                'p.barcode as product_code',
                'p.name as product_name',
                'stock_after as stock_current',
                'pu.name as product_unit',
                'pu.unit_symbol as unit_symbol',
                'pc.name as product_category',
                'p.product_unit_id as product_unit_id',
            )
            ->where('inventory_id', $request->inventory_id);

        return $latestStocks->orderBy('product_code')->get()->filter(function ($stock) use ($request) {
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

    public function export(Request $request)
    {
        $query = Model::when($request->inv_id, fn($query) => $query->where('inventory_id', $request->inv_id))
            ->when($request->out_id, fn($query) => $query->where('outlet_id', $request->out_id))
            ->when($request->date, fn($query) => $query->where('report_date', $request->date))
            ->when($request->searchTerm, function ($query) use ($request) {
                return $query->whereHas('product', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->searchTerm . '%');
                });
            })
            ->when($request->product_category_id, function ($query) use ($request) {
                $query->where('product_category_id', $request->product_category_id);
            });

        $total_stock_value     = (clone $query)->sum('stock_value');
        $total_potential_value = (clone $query)->sum('potential_value');

        $data = $query->orderBy('report_date', 'desc')->get();

        $pdf = Pdf::loadView('export.report.stock_value', [
            'data'                  => $data,
            'total_stock_value'     => $total_stock_value,
            'total_potential_value' => $total_potential_value,
            'inventory'             => $request->inv_id ? Inventory::find($request->inv_id)?->name : 'Semua',
            'outlet'                => $request->out_id ? Outlet::find($request->out_id)?->name : 'Semua',
            'date'                  => $request->date ? Carbon::parse($request->date)->translatedFormat('d F Y') : '',
            'search'                => $request->searchTerm ? $request->searchTerm : '-',
            'product_category'      => $request->product_category_id ? ProductCategory::find($request->product_category_id)?->name : '-',
        ])->setPaper('a4', 'landscape');
        return $pdf->download();
    }
}
