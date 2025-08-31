<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockOpname;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use App\Models\Userinventory;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;


class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $stock_opname = StockOpname::where('status', 'selesai')
            ->where('ref_code', null)
            ->with('inventory', 'outlet')
            ->orderBy('id', 'desc')
            ->get();


        if ($request->ajax()) {
            $adjustment = StockOpname::with('inventory', 'outlet')
                ->where('ref_code', '!=', null)
                ->where('status', 'adjustment')
                ->where(function ($query) use ($request) {
                    if ($request->start_date != '' && $request->end_date != '') {
                        $query->where([
                            ['so_date', '>=', startOfDay($request->start_date)],
                            ['so_date', '<=', endOfDay($request->end_date)]
                        ]);
                    }
                })
                ->when($request->inventory, function ($query) use ($request) {
                    $query->where('inventory_id', $request->inventory);
                })
                ->when($request->outlet, function ($query) use ($request) {
                    $query->where('outlet_id', $request->outlet);
                })
                ->when($request->inventory && $request->outlet, function ($query) use ($request) {
                    $query->where('inventory_id', $request->inventory)
                        ->where('outlet_id', $request->outlet);
                })
                ->get()->map(function ($row) {
                    $row->so_date = Carbon::parse($row->so_date)->format('d F Y H:i');
                    $row->opname_code = $row->ref_code;
                    return $row;
                });

            return DataTables::of($adjustment)
                ->addIndexColumn()
                ->addColumn('adjustment_code', function ($row) {
                    if ($row->status == 'adjustment') {
                        $code_link =  '<a href="stock_adjustment_preview/'
                            . $row->id . '"  data-original-title="Show" >'
                            . $row->code . '</a>';
                    }

                    return $code_link;
                })

                ->addColumn('status', function ($row) {
                    if ($row->status == 'adjustment') {
                        $status = '<span class="badge badge-sm bg-green-lt">Selesai</span>';
                    }

                    return $status;
                })
                ->addColumn('inventory', function ($row) {
                    $inventory = '';
                    if ($row->inventory) {
                        $inventory =  $row->inventory->name;
                    } else {
                        $inventory =  '-';
                    }

                    return $inventory;
                })
                ->addColumn('outlet', function ($row) {
                    $outlet = '';
                    if ($row->outlet) {
                        $outlet =  $row->outlet->name;
                    } else {
                        $outlet =  '-';
                    }

                    return $outlet;
                })

                ->rawColumns(['status', 'inventory', 'outlet', 'adjustment_code'])->make(true);
        }


        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            $inventory = Inventory::all();
            $outlet = Outlet::all();
        } else {
            $inventory = Inventory::whereIn('id', getUserInventory())->get();
            $outlet = Outlet::where('id', getUserOutlet())->get();
        }


        return view('pages.stock_adjustment.index', [
            'stock_opname' => $stock_opname,
            'inventory' => $inventory,
            'outlet' => $outlet
        ]);
    }

    public function adjustment(Request $request)
    {

        $query = DB::table('stock_opnames')->select(DB::raw('MAX(RIGHT(ref_code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd =  "0001";
        }
        $opname = StockOpname::with('inventory', 'outlet')
            ->where('id', $request->id)
            ->where('ref_code', null)
            ->first();

        $stockOpnameDetail = StockOpnameDetail::with('product')
            ->where('stock_opname_id', $request->id)
            ->where('qty_selisih', '!=', 0)->get();

        return view('pages.stock_adjustment.adjustment', [
            'opname' => $opname,
            'code' => $cd,
            'StockOpnameDetail' => $stockOpnameDetail
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth()->user();
            $now = Carbon::now();

            $opname = StockOpname::where('id', $request->id)->first();
            if (!$opname || $opname->status == 'adjustment' || $opname->ref_code != null) {
                return redirect()->back()->with('error', 'Stock Opname sudah di adjustment');
            }

            $opname->update(['ref_code' => $request->ref_code]);

            $adj = StockOpname::where('id', $request->id)
                ->withSum('adjustment_detail', 'adjustment_nominal_value')
                ->first();

            $stockOpnameDetail = StockOpnameDetail::where('stock_opname_id', $request->id)->with('product')->get();

            $inventory_id = $request->inventory_id ?? null;
            $outlet_id = $request->outlet_id ?? null;

            $createAdj = StockOpname::updateOrCreate(
                ['code' => $request->ref_code, 'ref_code' => $opname->code],
                [
                    'so_date' => $now,
                    'inventory_id' => $inventory_id,
                    'outlet_id' => $outlet_id,
                    'user_id' => $user->id,
                    'status' => 'adjustment'
                ]
            );

            foreach ($stockOpnameDetail as $sod) {
                $qty_adjustment = $sod->id . '_qty_adjustment';
                $adjustment_qty = $request->$qty_adjustment;
                $new_stock = $sod->qty_system + $adjustment_qty;

                $sod->update([
                    'ref_code' => $request->ref_code,
                    'qty_adjustment' => $adjustment_qty,
                    'qty_after_adjustment' => $new_stock,
                    'adjustment_nominal_value' => $adjustment_qty * $sod->product->capital_price
                ]);

                $product_stock = ProductStock::updateOrCreate(
                    ['product_id' => $sod->product_id, 'inventory_id' => $inventory_id, 'outlet_id' => $outlet_id],
                    []
                );
                $stock_before = $product_stock->stock_current ?? 0; // Ambil stok terbaru sebelum update
                $product_stock->update(['stock_current' => $new_stock]); // Perbarui stok

                $action_type = $adjustment_qty > 0 ? 'PLUS' : 'MINUS';

                $this->createStockHistory($product_stock, $adj, $action_type, $user, $adjustment_qty, $sod->product->capital_price, $new_stock, $stock_before);
            }

            DB::commit();

            return redirect()->route('stockAdjustment.preview', ['id' => $createAdj->id]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' Line : ' . $e->getLine());
            DB::rollback();
            return redirect()->route('stockAdjustment.index')->with('error', 'Data gagal di Adjustment!');
        }
    }

    private function createStockHistory($product_stock, $adj, $action_type, $user, $adjustment_qty, $capital_price, $new_stock, $stock_before)
    {
        \Log::info($adjustment_qty);
        ProductStockHistory::create([
            'document_number' => $adj->code,
            'history_date' => Carbon::now(),
            'action_type' => $action_type,
            'user_id' => $user->id,
            'product_id' => $product_stock->product_id,
            'inventory_id' => $product_stock->inventory_id ?? null,
            'outlet_id' => $product_stock->outlet_id ?? null,
            'stock_change' => $adjustment_qty ?? 0,
            'stock_before' => $stock_before,
            'stock_after' => $new_stock,
            'desc' => 'Adjustment NO : ' . $adj->code . ' ' . ($action_type === 'PLUS' ? 'PENAMBAHAN' : 'PENGURANGAN') . ' Stok Opname No: ' . $adj->ref_code,
        ]);
    }


    public function detail($id)
    {
        $adjustment = StockOpname::where('id', $id)->with('inventory', 'outlet')->first();
        $adjustment_detail = StockOpnameDetail::where('ref_code', $adjustment->code)->with('product')->get();

        return view('pages.stock_adjustment.detail', [
            'adjustment' => $adjustment,
            'adjustment_detail' => $adjustment_detail
        ]);
    }

    public function preview($id)
    {
        $adjustment = StockOpname::where('id', $id)
            ->with(
                'adjustment_detail',
                'inventory',
                'outlet',
                'adjustment_detail.product',
                'user',
                'adjustment_detail.product_category',
                'adjustment_detail.product_unit'
            )->first();

        $company = ProfilCompany::where('status', 'active')->first();

        $sum = StockOpnameDetail::query()
            ->where('ref_code', $adjustment->ref_code)
            ->where('qty_adjustment', '!=', null)
            ->sum('adjustment_nominal_value');

        return view('pages.stock_adjustment.preview', [
            'title' => 'Nomor Adjustment : ' . $adjustment->code,
            'company' => $company,
            'adjustment' => $adjustment,
            'sum' => $sum
        ]);
    }

    public function getDataAdjustment($code)
    {
        $adjustment = StockOpname::with(
            'adjustment_detail',
            'inventory',
            'outlet',
            'adjustment_detail.product',
            'user',
            'adjustment_detail.product_category',
            'adjustment_detail.product_unit'
        )->where('code', $code)->first();

        $sum = StockOpnameDetail::where('ref_code', $adjustment->code)->where('qty_adjustment', '!=', null)
            ->sum('adjustment_nominal_value');

        $company = ProfilCompany::where('status', 'active')->first();

        return view('pages.stock_adjustment.report.ar-detail', [
            'title'         => 'Data Stock Adjustment',
            'adjustment'    => $adjustment,
            'company'       => $company,
            'sum'           => $sum
        ]);
    }
}
