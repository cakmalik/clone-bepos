<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\JournalTransaction;
use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductStockHistory;
use Yajra\DataTables\DataTables;

class ProductStockHistoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $productId   = $request->product_id;
            $outletId    = $request->outlet_id;
            $inventoryId = $request->inventory_id;
            $startDate   = $request->startDate;
            $endDate     = $request->endDate;

            $productStockHistory = ProductStockHistory::query()
                ->select(
                    'product_stock_histories.*',
                    'o.name as outlet_name',
                    'i.name as inventory_name',
                    'users.users_name as username'
                )
                ->leftJoin('outlets as o', 'product_stock_histories.outlet_id', 'o.id')
                ->leftJoin('inventories as i', 'product_stock_histories.inventory_id', 'i.id')
                ->leftJoin('users', 'product_stock_histories.user_id', 'users.id')
                ->with('product')
                ->where('product_id', $productId)
                ->where(function ($query) use ($startDate, $endDate, $outletId, $inventoryId) {

                    if ($inventoryId != '') {
                        $query->where('product_stock_histories.inventory_id', $inventoryId);
                    }

                    if ($outletId != '') {
                        $query->where('product_stock_histories.outlet_id', $outletId);
                    }

                    $query->whereDate('history_date', '>=', $startDate)
                        ->whereDate('history_date', '<=', $endDate);
                })
                ->orderBy('history_date')
                ->get()->map(function ($row) {
                    $row->inventory = $row->outlet_name ?? $row->inventory_name;
                    $row->user      = $row->username;

                    return $row;
                });

            return DataTables::of($productStockHistory)
                ->addIndexColumn()
                ->make(true);
        }


        if (auth()->user()->role->role_name == ('SUPERADMIN')) {
            $outlets = Outlet::all();
            $inventory = Inventory::all();
        } else {
            $outlets = Outlet::whereIn('id', getUserOutlet())->get();
            $inventory = Inventory::whereIn('id', getUserInventory())->get();
        }

        return view('pages.product.product_history.index', compact('outlets', 'inventory'));
    }
}
