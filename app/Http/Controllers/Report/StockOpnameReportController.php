<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class StockOpnameReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role->role_name === 'SUPERADMIN') {
            $inventories = Inventory::all();
            $outlet = Outlet::all();
        } else {
            $inventories = Inventory::whereIn('id', getUserInventory())->get();
            $outlet = Outlet::whereIn('id', getUserOutlet())->get();
        }

        return view('pages.report.stock_opname.index', compact('inventories', 'outlet'));
    }

    public function print(Request $request)
    {
        try {
            $user = auth()->user();
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $inventory_id = $request->query('inventory_id');
            $outlet_id = $request->query('outlet_id');
        
            $query = StockOpname::where('status', 'selesai')
                ->whereHas('stockOpnameDetails')
                ->with('inventory', 'outlet', 'user', 'stockOpnameDetails.product.productCategory')
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('so_date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('so_date', '<=', $end_date);
                })
                ->when($inventory_id, function ($query) use ($inventory_id) {
                    $query->where('inventory_id', $inventory_id);
                })
                ->when($outlet_id, function ($query) use ($outlet_id) {
                    $query->where('outlet_id', $outlet_id);
                });
        
            if ($user->role->role_name !== 'SUPERADMIN') {
                $query->where(function($query) {
                    $query->whereIn('outlet_id', getUserOutlet())
                        ->orWhereIn('inventory_id', getUserInventory());
                });
            }
        
            $opnames = $query->get();
        
            return view('pages.report.stock_opname.print', compact(
                'opnames',
                'start_date',
                'end_date',
                'inventory_id',
                'outlet_id'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
}
