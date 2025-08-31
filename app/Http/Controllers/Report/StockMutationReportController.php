<?php

namespace App\Http\Controllers\Report;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Http\Controllers\Controller;
use App\Models\StockMutationInventoryOutlet;

class StockMutationReportController extends Controller
{
    // Menampilkan halaman laporan dengan filter
    public function index()
    {
        $inventories = Inventory::all();
        $products = Product::all();
        $outlets = Outlet::all();

        return view('pages.report.stock_mutation.index', compact('inventories', 'products', 'outlets'));
    }

    public function print(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $inventory_source_id = $request->query('inventory_source_id');
        $inventory_destination_id = $request->query('inventory_destination_id');
        $outlet_source_id = $request->query('outlet_source_id');
        $outlet_destination_id = $request->query('outlet_destination_id');
        $product_id = $request->query('product_id');
        $mutation_type = $request->query('mutation_type');

        // Pastikan hanya memeriksa inventory_destination_id jika ada
        if ($mutation_type === 'gudangKeOutlet') {
            $mutations = StockMutationInventoryOutlet::with([
                'inventorySource',
                'OutletDestination',
                'items.product',
                'approvedBy',
                'receivedBy',
                'creator',
            ])
                ->whereIn('inventory_source_id', getUserInventory())
                ->when($inventory_source_id, function ($query) use ($inventory_source_id) {
                    $query->where('inventory_source_id', $inventory_source_id);
                })
                ->when($outlet_destination_id, function ($query) use ($outlet_destination_id) {
                    $query->where('outlet_destination_id', $outlet_destination_id);
                })
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('date', '<=', $end_date);
                })
                ->when($product_id, function ($query) use ($product_id) {
                    $query->whereHas('items', function ($query) use ($product_id) {
                        $query->where('product_id', $product_id);
                    });
                })
                ->get();
        } else if ($mutation_type === 'gudangKeGudang') {
            // Penanganan untuk gudangKeGudang
            $mutations = StockMutation::with([
                'inventorySource',
                'inventoryDestination',
                'items.product',
                'approvedBy',
                'receivedBy',
                'creator',
            ])
                ->whereIn('inventory_source_id', getUserInventory())
                ->when($inventory_source_id, function ($query) use ($inventory_source_id) {
                    $query->where('inventory_source_id', $inventory_source_id);
                })
                ->when($inventory_destination_id, function ($query) use ($inventory_destination_id) {
                    $query->where('inventory_destination_id', $inventory_destination_id);
                })
                ->where('mutation_category', 'inventory_to_inventory')
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('date', '<=', $end_date);
                })
                ->when($product_id, function ($query) use ($product_id) {
                    $query->whereHas('items', function ($query) use ($product_id) {
                        $query->where('product_id', $product_id);
                    });
                })
                ->get();
        } else if ($mutation_type === 'outletKeOutlet') {
            $mutations = StockMutation::with([
                'OutletSource',
                'OutletDestination',
                'items.product',
                'approvedBy',
                'receivedBy',
                'creator',
            ])
                ->when($outlet_source_id, function ($query) use ($outlet_source_id) {
                    $query->where('outlet_source_id', $outlet_source_id);
                })
                ->when($outlet_destination_id, function ($query) use ($outlet_destination_id) {
                    $query->where('outlet_destination_id', $outlet_destination_id);
                })
                ->where('mutation_category', 'outlet_to_outlet')
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('date', '<=', $end_date);
                })
                ->when($product_id, function ($query) use ($product_id) {
                    $query->whereHas('items', function ($query) use ($product_id) {
                        $query->where('product_id', $product_id);
                    });
                })
                ->get();
        }

        return view('pages.report.stock_mutation.print', compact('mutations'));
    }
}
