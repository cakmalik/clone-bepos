<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\ProductStock;
use App\Models\ProfilCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutletConsolidationController extends Controller
{
    public function stockOutletConsolidation()
    {
        $inventory = Outlet::whereHas('product_stock')->addSelect([
            'stock' => ProductStock::selectRaw('sum(stock_current) as qty_total')
                ->whereColumn('outlet_id', 'outlets.id')
                ->groupBy('outlet_id')
        ])->get();

        $inventory_id = [];

        foreach ($inventory as $inv) {
            if ($inv->stock > 0) {
                $inventory_id [] = $inv->id;
            }
        }


        $data = [
            'title'         => 'Laporan Stok Konsolidasi',
            'inventory'     => $inventory,
            'inventory_id'  => $inventory_id
        ];


        return view('pages.report.stock_outlet_consolidation.index', $data);
    }

    public function stockOutletConsolidationData(Request $request)
    {

        $inventoryId = explode(",", $request->inventory);
        $inventory = Inventory::whereIn('id', $inventoryId)->get();

        if ($request->recap_date == date('Y-m-d')) {
            $products  = $this->queryCurrentStockOutlet($inventoryId);
        } else {
            $products  = $this->queryCertainStockOutlet($request->recap_date, $inventoryId);
        }

        // dd($products->toArray());

        $productMap = [];

        foreach ($products as $prod) {
            $productArray = $this->searchArrayMulti($productMap, 'product_code', $prod->product_code);

            if (empty($productArray)) {
                $productMap [] = [
                    'product_code'                => $prod->product_code,
                    'product_name'                => $prod->product_name,
                    'qty_'.$prod->outlet_id    => $prod->stock_current,
                    'inventory_id'                => $prod->outlet_id
                ];
            } else {

               $searchIndex = '';
               foreach ($productMap as $key => $row) {
                    if ($row['product_code'] == $prod->product_code) {
                        $searchIndex = $key;
                        $productMap[$searchIndex]['qty_'.$prod->outlet_id] = $prod->stock_current;
                    }
               }
            }

        }

        $company = ProfilCompany::where('status','active')->first();

        $data = [
            'title'                   => 'Data Laporan Stok Konsolidasi - '.dateStandar($request->recap_date),
            'company'                 => $company,
            'selected_inventory'      => $inventory,
            'products'                => $productMap
        ];

        return view('pages.report.stock_gudang_consolidation.print', $data);
    }

    private function queryCurrentStockOutlet($inventoryId)
    {
        return DB::table('product_stocks as ps')
            ->leftJoin('products as p', 'ps.product_id', 'p.id')
            ->select(
                'p.name as product_name',
                'p.code as product_code',
                'ps.stock_current',
                'ps.outlet_id',
            )
            ->whereIn('ps.outlet_id', $inventoryId)
            ->groupBy('ps.product_id', 'ps.stock_current', 'p.name', 'ps.outlet_id', 'p.code')
            ->orderBy('product_code')
            ->get();

    }

    private function queryCertainStockOutlet($date, $inventoryId)
    {
        return DB::table('product_stock_dailies as ps')
        ->leftJoin('products as p', 'ps.product_id', 'p.id')
        ->select(
            'p.name as product_name',
            'p.code as product_code',
            'ps.qty as stock_current',
            'ps.outlet_id'
        )->where([
            ['ps.recap_date', '>=', startOfDay($date)],
            ['ps.recap_date', '<=', endOfDay($date)]
        ])->whereIn('ps.outlet_id', $inventoryId)
        ->groupBy('ps.product_id', 'ps.qty', 'p.name', 'ps.outlet_id', 'p.code')
        ->orderBy('product_code')
        ->get();

    }

    private function searchArrayMulti($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->searchArrayMulti($subarray, $key, $value));
            }
        }

        return $results;
    }
}
