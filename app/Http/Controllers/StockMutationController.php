<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMutation;
use App\Models\StockMutationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class StockMutationController extends Controller
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

            $mutations = StockMutation::with(['inventorySource', 'inventoryDestination'])
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('date', '=', $endDate);
                })
                ->when($type == 'incoming', function ($query) use ($myInventoriesId) {
                    return $query->whereIn('inventory_destination_id', $myInventoriesId);
                })
                ->when($type == 'outgoing', function ($query) use ($myInventoriesId) {
                    return $query->whereIn('inventory_source_id', $myInventoriesId);
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->get();

            return DataTables::of($mutations)
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.stock_mutation.index');
    }

    public function create(Request $request)
    {
        $myInventories = Auth::user()->inventories;
        $inventories = Inventory::all();
        $products = Product::where('type_product', 'material')->get();
        return view('pages.stock_mutation.create', compact('myInventories', 'inventories', 'products'));
    }

    public function getProducts(Request $request)
    {
        $items = ProductStock::where('inventory_id', $request->query('inventory_id'))
            ->when($request->products_id, function ($query) use ($request) {
                return $query->whereIn('id', explode(',', $request->query('products_id')));
            })
            ->whereHas('product', function ($query) {
                return $query->where('type_product', 'material');
            })
            ->with(['product.productUnit'])
            ->get();

        return response()->json($items);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $validated = $request->validate([
            'inventory_source_id' => 'required|exists:inventories,id',
            'inventory_destination_id' => 'required|exists:inventories,id',
            'items' => 'array',
            'items.*.product_id' => 'required:exists:products,id',
            'items.*.qty' => 'required|numeric',
        ]);


        try {
            $inventorySource = Inventory::findOrFail($validated['inventory_source_id']);

            $mutation = StockMutation::create([
                'code' => autoCode('stock_mutations', 'code', 'PG-' . $inventorySource->code . '-' . date('y') . '-' . date('m') . '-', 4),
                'date' => date('Y-m-d H:i'),
                'inventory_source_id' => $validated['inventory_source_id'],
                'inventory_destination_id' => $validated['inventory_destination_id'],
                'creator_user_id' => Auth::user()->id,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $item) {
                StockMutationItem::create([
                    'stock_mutation_id' => $mutation->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit();
            return redirect()->route('stockMutation.edit', [$mutation->id])->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Data gagal ditambahkan!');
        }
    }

    public function edit(Request $request, $id)
    {
        $mutation = StockMutation::where('id', $id)->with(['inventorySource', 'inventoryDestination', 'creator',])->first();
        $myInventoriesId = array_map(function ($item) {
            return $item['id'];
        }, collect(Auth::user()->inventories)->toArray());
        $isReceiver = in_array($mutation->inventory_destination_id, $myInventoriesId);
        return view('pages.stock_mutation.edit', compact('mutation', 'isReceiver'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'array',
            'items.*.product_id' => 'required:exists:products,id',
            'items.*.qty' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            StockMutationItem::where('stock_mutation_id', $id)->delete();
            foreach ($validated['items'] as $item) {
                StockMutationItem::create([
                    'stock_mutation_id' => $id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                ]);
            }

            DB::commit();
            return redirect()->route('stockMutation.edit', [$id])->with('success', 'Data berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Data gagal diubah!');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'in:void,open',
        ]);
        $mutation = StockMutation::findOrFail($id);
        if ($mutation->status == 'draft') {
            $mutation->status = $validated['status'];
            $mutation->save();
            return redirect('/stock_mutation/print/' . $id)->with('success', 'Data berhasil diubah!');
        } else {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status!');
        }
    }

    public function print(Request $request, $id)
    {
        $mutation = StockMutation::with([
            'inventorySource',
            'inventoryDestination',
            'outletSource',
            'outletDestination',
            'items.product'
        ])->findOrFail($id);

        $myInventoriesId = Auth::user()->inventories->pluck('id')->toArray();

        // Jika tujuannya adalah inventory, maka cek apakah dia penerima
        $isReceiver = in_array($mutation->inventory_destination_id, $myInventoriesId);

        return view('pages.stock_mutation.print', compact('mutation', 'isReceiver'));
    }

    public function receipt(Request $request, $id)
    {
        $mutation = StockMutation::with([
            'inventorySource',
            'inventoryDestination',
            'outletSource',
            'outletDestination',
            'items.product'
        ])->findOrFail($id);

        if ($mutation->outletSource && $mutation->outletDestination) {
            $type = 'outlet_to_outlet';
            $source = $mutation->outletSource;
            $destination = $mutation->outletDestination;
        } elseif ($mutation->inventorySource && $mutation->inventoryDestination) {
            $type = 'inventory_to_inventory';
            $source = $mutation->inventorySource;
            $destination = $mutation->inventoryDestination;
        } elseif ($mutation->inventorySource && $mutation->outletDestination) {
            $type = 'inventory_to_outlet';
            $source = $mutation->inventorySource;
            $destination = $mutation->outletDestination;
        } elseif ($mutation->outletSource && $mutation->inventoryDestination) {
            $type = 'outlet_to_inventory';
            $source = $mutation->outletSource;
            $destination = $mutation->inventoryDestination;
        } else {
            return redirect()->back()->withErrors('Data mutasi tidak lengkap.');
        }

        $username = Auth::user()->users_name;

        return view('pages.stock_mutation.receipt', compact(
            'mutation', 'type', 'source', 'destination', 'username'
        ));
    }

    public function receive(Request $request, $id)
    {
        $mutation = StockMutation::with('items')->where('id', $id)->where('status', 'open')->first();
        $myInventoriesId = array_map(function ($item) {
            return $item['id'];
        }, collect(Auth::user()->inventories)->toArray());
        $isReceiver = in_array($mutation->inventory_destination_id, $myInventoriesId);

        if ($isReceiver) {
            DB::beginTransaction();
            try {
                $mutation->received_user_id = Auth::user()->id;
                $mutation->status = 'done';
                $mutation->save();

                foreach ($mutation->items as $item) {
                    $sourceStock = ProductStock::where('product_id', $item->product_id)->where('inventory_id', $mutation->inventory_source_id)->first();
                    $sourceStock->stock_current -= $item->qty;
                    $sourceStock->save();
                    $destinationStock = ProductStock::where('product_id', $item->product_id)->where('inventory_id', $mutation->inventory_destination_id)->first();
                    if ($destinationStock) {
                        $destinationStock->stock_current += $item->qty;
                        $destinationStock->save();
                    } else {
                        ProductStock::create(['inventory_id' => $mutation->inventory_destination_id, 'product_id' => $item->product_id, 'stock_current' => $item->qty]);
                    }
                }

                DB::commit();
                return redirect('/stock_mutation')->with('success', 'Data berhasil diubah!');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Data gagal diubah!');
            }
        } else {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status!');
        }
    }
}
