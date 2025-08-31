<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;

use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StockMutationInventoryOutlet;
use App\Models\StockMutationInventoryOutletItem;
use Carbon\Carbon;

class StockMutationInventoryOutletController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $type = $request->type;
            $status = $request->status;

            $myInventoriesId = array_map(function ($item) {
                return $item['id'];
            }, collect(Auth::user()->inventories)->toArray());

            $mutations = StockMutationInventoryOutlet::with(['inventorySource', 'OutletDestination'])
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('date', '<=', $endDate);
                })
                ->when($type == 'incoming', function ($query) use ($myInventoriesId) {
                    return $query->whereIn('outlet_destination_id', $myInventoriesId);
                })
                ->when($type == 'outgoing', function ($query) use ($myInventoriesId) {
                    return $query->whereIn('inventory_source_id', $myInventoriesId);
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->orderBy('date', 'desc')
                ->get();

            return DataTables::of($mutations)->addIndexColumn()->make(true);
        }
        return view('pages.stock_mutation_inventory_outlet.index');
    }

    public function indexV2()
    {
        return view('pages.stock_mutation_inventory_outlet.v2.index');
    }

    public function create(Request $request)
    {
        $myInventories = Auth::user()->inventories;
        $inventories = Inventory::all();
        $outlets = Outlet::all();

        return view('pages.stock_mutation_inventory_outlet.create', compact('myInventories', 'inventories', 'outlets'));
    }

    public function getProducts(Request $request)
    {
        $productsId = $request->query('products_id') ? explode(',', $request->query('products_id')) : [];

        $items = ProductStock::leftJoin('purchase_details', function ($join) {
            $join->on('product_stocks.product_id', '=', 'purchase_details.product_id')->whereRaw('purchase_details.id = (select max(id) from purchase_details where product_id = product_stocks.product_id)');
        })
            ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->where([['product_stocks.inventory_id', $request->query('inventory_id')], ['product_stocks.stock_current', '>', 0]])
            ->where(function ($query) use ($productsId) {
                if (!empty($productsId)) {
                    $query->whereIn('product_stocks.product_id', $productsId);
                }
            })
            ->whereHas('product')
            ->with(['product.productUnit'])
            ->select('product_stocks.*', 'purchases.code as purchase_code', 'purchase_details.accepted_qty')
            ->orderBy('product_stocks.stock_current', 'desc')
            ->orderBy('purchase_date', 'desc')
            ->get();

        return response()->json($items);
    }

    public function getProductsPo(Request $request)
    {
        $productsId = $request->query('products_id') ? explode(',', $request->query('products_id')) : [];
        $items = PurchaseDetail::where('inventory_id', $request->query('inventory_id'))
            ->whereHas('product', function ($query) use ($productsId) {
                if (!empty($productsId)) {
                    $query->whereIn('id', $productsId);
                }
            })
            ->with(['product.productUnit'])
            ->where('accepted_qty', '>', 0)
            ->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_source_id' => 'required|exists:product_stocks,inventory_id',
            'outlet_destination_id' => 'required|exists:outlets,id',
            'items' => 'array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $prefix = 'PG-' . date('y') . '-' . date('m');

            $mutation = StockMutationInventoryOutlet::create([
                'code' => autoCode('stock_mutation_inventory_outlets', 'code', $prefix . '-', 4),
                'date' => date('Y-m-d H:i'),
                'inventory_source_id' => $validated['inventory_source_id'],
                'outlet_destination_id' => $validated['outlet_destination_id'],
                'creator_user_id' => Auth::user()->id,
                'status' => 'done',
            ]);

            $outlet = Outlet::findOrFail($validated['outlet_destination_id']);
            $inventory = Inventory::findOrFail($validated['inventory_source_id']);

            foreach ($validated['items'] as $item) {

                $sourceProduct = ProductStock::query()
                    ->where('inventory_id', $validated['inventory_source_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($sourceProduct) {
                    $stockBefore = $sourceProduct->stock_current;
                    $sourceProduct->stock_current -= $item['qty'];
                    $sourceProduct->save();
                } else {
                    return redirect()->back()->with('error', 'Item stok yang dipilih tidak ditemukan !');
                }

                ProductStockHistory::create([
                    'outlet_id' => null,
                    'user_id' => Auth::id(),
                    'product_id' => $item['product_id'],
                    'inventory_id' => $validated['inventory_source_id'],
                    'document_number' => $mutation->code,
                    'history_date' => Carbon::now(),
                    'stock_change' => $item['qty'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $sourceProduct->stock_current,
                    'action_type' => 'minus',
                    'desc' => 'Mutasi ke outlet ' . $outlet->name,
                ]);

                $outletProduct = ProductStock::query()
                    ->where('outlet_id', $validated['outlet_destination_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($outletProduct) {
                    $stockBefore = $outletProduct->stock_current;
                    $outletProduct->stock_current += $item['qty'];
                    $outletProduct->save();
                } else {
                    $stockBefore = 0;
                    $outletProduct = ProductStock::create([
                        'outlet_id' => $validated['outlet_destination_id'],
                        'inventory_id' => null,
                        'product_id' => $item['product_id'],
                        'stock_current' => $item['qty'],
                    ]);
                }

                ProductStockHistory::create([
                    'outlet_id' => $validated['outlet_destination_id'],
                    'user_id' => Auth::id(),
                    'product_id' => $item['product_id'],
                    'inventory_id' => null,
                    'document_number' => $mutation->code,
                    'history_date' => Carbon::now(),
                    'stock_change' => $item['qty'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $outletProduct->stock_current,
                    'action_type' => 'plus',
                    'desc' => 'Mutasi dari gudang ' . $inventory->name,
                ]);

                StockMutationInventoryOutletItem::create([
                    'stock_mutation_id' => $mutation->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit();
            return redirect('/stock_mutation_inventory_to_outlet/print/' . $mutation->id)->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Data gagal ditambahkan! ');
        }
    }

    public function print(Request $request, $id)
    {
        $mutation = StockMutationInventoryOutlet::where('id', $id)->first();
        if (!$mutation) {
            abort(404);
        }
        $myInventoriesId = array_map(function ($item) {
            return $item['id'];
        }, collect(Auth::user()->inventories)->toArray());
        $isReceiver = in_array($mutation->outlet_destination_id, $myInventoriesId);

        return view('pages.stock_mutation_inventory_outlet.print', compact('mutation', 'isReceiver'));
    }

    public function receipt(Request $request, $id)
    {
        $mutation = StockMutationInventoryOutlet::where('id', $id)
            ->with(['inventorySource', 'OutletDestination'])
            ->first();
        $items = StockMutationInventoryOutletItem::where('stock_mutation_id', $id)
            ->with(['product'])
            ->get();

        // dd($mutation);

        $username = Auth::user()->users_name;

        return view('pages.stock_mutation_inventory_outlet.receipt', compact('mutation', 'items', 'username'));
    }
}
