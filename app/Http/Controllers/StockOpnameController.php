<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\UserOutlet;
use App\Models\StockOpname;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    private function queryGetProduct($request)
    {
        return Product::query()
            ->select('products.code', 'products.barcode', 'products.id', 'products.name', 'products.type_product', 'brands.name as brand_name', 'category.name as product_category', 'suppliers.name as supplier_name')
            ->leftJoin('brands', 'brand_id', 'brands.id')
            ->leftJoin('product_categories as category', 'product_category_id', 'category.id')
            ->leftJoin('product_suppliers', 'product_suppliers.product_id', 'products.id')
            ->leftJoin('suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
            ->where(function ($query) use ($request) {
                if ($request->selected_product != null) {
                    $query->whereNotIn('products.id', $request->selected_product);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->product != '') {
                    $query->where('products.name', 'LIKE', '%' . $request->product . '%');
                    $query->orWhere('products.barcode', 'LIKE', '%' . $request->product . '%');
                }
            })
            ->where('products.deleted_at', null)
            ->limit(1000)
            ->orderBy('products.name')
            ->get();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $opname = StockOpname::with('inventory', 'outlet', 'user')
                ->where('status', '!=', 'adjustment')
                ->where(function ($query) use ($request) {
                    if ($request->has('status')) {
                        $query->where('status', $request->status);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->start_date != '' && $request->end_date != '') {
                        $query->where([['so_date', '>=', startOfDay($request->start_date)], ['so_date', '<=', endOfDay($request->end_date)]]);
                    }
                })
                ->get()
                ->map(function ($row) {
                    $row->so_date = Carbon::parse($row->so_date)->format('d F Y H:i');
                    return $row;
                });

            return DataTables::of($opname)
                ->addIndexColumn()
                ->addColumn('opname_code', function ($row) {
                    if ($row->status == 'selesai') {
                        $code_link = '<a href="stock_opname_preview/' . $row->id . '"  data-original-title="Show" >' . $row->code . '</a>';
                    } else {
                        $code_link = '<a href="stock_opname/detail/' . $row->id . '" data-original-title="Show" >' . $row->code . '</a>';
                    }

                    return $code_link;
                })

                ->addColumn('status', function ($row) {
                    if ($row->status == 'selesai') {
                        $status = '<span class="badge badge-sm bg-green">Selesai</span>';
                    } else {
                        $status = ' <span class="badge badge-sm bg-yellow">Belum Selesai</span>';
                    }

                    return $status;
                })
                ->addColumn('inventory', function ($row) {
                    $inventory = '';
                    if ($row->inventory) {
                        $inventory = $row->inventory->name;
                    } else {
                        $inventory = '-';
                    }

                    return $inventory;
                })
                ->addColumn('outlet', function ($row) {
                    $outlet = '';
                    if ($row->outlet) {
                        $outlet = $row->outlet->name;
                    } else {
                        $outlet = '-';
                    }

                    return $outlet;
                })
                ->rawColumns(['status', 'opname_code', 'inventory', 'outlet'])
                ->make(true);
        }

        return view('pages.stock_opname.index', ['title' => 'Stok Opname']);
    }

    public function index2()
    {
        return view('pages.stock_opname.index2', ['title' => 'Stok Opname']);
    }

    public function add2(){
        return view('pages.stock_opname.create2');
    }

    public function add(Request $request)
    {
        $inventory = Inventory::All();
        $outlet = Outlet::All();

        if ($request->ajax()) {
            $products = $this->queryGetProduct($request);

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return ' <td id="opname_product" data-id="opname_product_' .
                        $row->id .
                        '">
                    <a href="javascript:void(0)" id="items_opname"
                        class="btn btn-primary btn-sm py-2 px-3" data-id="' .
                        $row->id .
                        '">
                        <li class="fas fa-add"></li>
                    </a>
                    </td>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.stock_opname.create', [
            'title' => 'Buat Opname',
            'outlet' => $outlet,
            'inventory' => $inventory,
        ]);
    }

    public function getProductOpname(Request $request)
    {
        $product = Product::query()
            ->whereIn('id', $request->items)
            ->get();

        return response()->json([
            'response' => $product,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'items' => 'array',
        ]);

        DB::beginTransaction();

        try {
            $code = '';
            $inventory_id = null;
            $outlet_id = null;
            if ($request->type == 'GUDANG') {
                $inventory = Inventory::find($request->inventory_id);
                $code = opnameCode($inventory->code);
                $inventory_id = $request->inventory_id;
            } else {
                $outlet = Outlet::find($request->outlet_id);
                $code = opnameCode($outlet->code);
                $outlet_id = $request->outlet_id;
            }

            $stockOpname = StockOpname::create([
                'code' => $code,
                'so_date' => Carbon::now(),
                'outlet_id' => $outlet_id,
                'inventory_id' => $inventory_id,
                'user_id' => Auth()->user()->id,
                'status' => 'belum_selesai',
            ]);

            $product = Product::whereIn('id', $request->items)->get();
            foreach ($product as $pd) {
                $qty = $pd->id . '_qty';

                StockOpnameDetail::create([
                    'code' => opnameDetailCode($pd->code),
                    'stock_opname_id' => $stockOpname->id,
                    'product_id' => $pd->id,
                    'qty_system' => 0,
                    'qty_so' => $request->$qty,
                    'qty_selisih' => 0,
                ]);
            }

            DB::commit();
            return redirect('/stock_opname/edit/' . $stockOpname->id)->withSuccess('Sukses di Simpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }

    public function check_qty(Request $request)
    {
        $product_stock = ProductStock::where('product_id', $request->id)->first();

        return response()->json([
            'response' => $product_stock,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $stockOpname = StockOpname::where('id', $id)->with('stockOpnameDetails.product')->first();
        $inventory = Inventory::All();
        $outlet = Outlet::All();
        $productCurrent = [];

        foreach ($stockOpname->stockOpnameDetails as $op) {
            $productCurrent[] = $op->product_id;
        }

        if ($request->ajax()) {
            $products = $this->queryGetProduct($request);

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($productCurrent, $request) {
                    $column = '<td id="opname_product" data-id="opname_product_' . $row . '">';

                    if ($request->selected_product != null && in_array($row->id, $productCurrent) && in_array($row->id, $request->selected_product)) {
                        $column = $column . '<span class="badge badge-sm bg-green">Dipilih</span></td>';
                    } else {
                        $column =
                            $column .
                            '<a href="javascript:void(0)" id="items_opname"
                        class="btn btn-primary btn-sm py-2 px-3"
                        data-id="' .
                            $row->id .
                            '">
                        <li class="fas fa-add"></li></a>';
                    }

                    return $column;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.stock_opname.edit', [
            'title' => 'Edit Stock Opname',
            'outlet' => $outlet,
            'inventory' => $inventory,
            'stockOpname' => $stockOpname,
            'product_current' => $productCurrent,
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $request->validate([
            'type' => 'required',
            'items' => 'array',
        ]);
        try {
            StockOpnameDetail::where('stock_opname_id', $id)->forceDelete();

            if ($request->type == 'GUDANG') {
                StockOpname::where('id', $id)->update([
                    'outlet_id' => null,
                    'inventory_id' => $request->inventory_id,
                ]);
            } else {
                StockOpname::where('id', $id)->update([
                    'outlet_id' => $request->outlet_id,
                    'inventory_id' => null,
                ]);
            }

            $product = Product::whereIn('id', $request->items)->get();

            foreach ($product as $pd) {
                $qty = $pd->id . '_qty';

                StockOpnameDetail::create([
                    'code' => opnameDetailCode($pd->code),
                    'stock_opname_id' => $id,
                    'product_id' => $pd->id,
                    'qty_system' => 0,
                    'qty_so' => $request->$qty,
                    'qty_selisih' => 0,
                ]);
            }

            DB::commit();
            return redirect()->back()->withSuccess('Sukses di Update!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    public function finish(Request $request)
    {
        DB::beginTransaction();

        try {
            StockOpname::where('id', $request->id)->update([
                'status' => 'selesai',
            ]);

            $stockOpname = StockOpname::query()
                ->where('id', $request->id)
                ->with('stockOpnameDetails.product', 'inventory', 'outlet', 'user')
                ->first();

            foreach ($stockOpname->stockOpnameDetails as $op) {
                if ($stockOpname->inventory_id) {
                    $productStock = ProductStock::query()
                        ->where('inventory_id', $stockOpname->inventory_id)
                        ->where('product_id', $op->product_id)
                        ->first();
                } else {
                    $productStock = ProductStock::query()
                        ->where('outlet_id', $stockOpname->outlet_id)
                        ->where('product_id', $op->product_id)
                        ->first();
                }

                if ($productStock) {
                    StockOpnameDetail::query()
                        ->where('stock_opname_id', $request->id)
                        ->where('product_id', $op->product_id)
                        ->update([
                            'qty_system' => $productStock->stock_current,
                            'qty_selisih' => $op->qty_so - $productStock->stock_current,
                        ]);
                } else {
                    StockOpnameDetail::query()
                        ->where('stock_opname_id', $request->id)
                        ->where('product_id', $op->product_id)
                        ->update([
                            'qty_system' => 0,
                            'qty_selisih' => $op->qty_so,
                        ]);
                }
            }

            $company = ProfilCompany::where('status', 'active')->first();
            DB::commit();

            return view('pages.stock_opname.preview', [
                'title' => 'Nomor SO : ' . $stockOpname->code,
                'company' => $company,
                'stockOpname' => $stockOpname,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Selesaikan!');
        }
    }

    public function detail($id)
    {
        $stockOpname = StockOpname::where('id', $id)->with('inventory', 'outlet', 'user')->first();
        $stockOpnameDetail = StockOpnameDetail::where('stock_opname_id', $id)->with('product')->get();

        return view('pages.stock_opname.detail', [
            'stockOpname' => $stockOpname,
            'stockOpnameDetail' => $stockOpnameDetail,
        ]);
    }

    public function destroy($id)
    {
        try {
            StockOpnameDetail::where('stock_opname_id', $id)->delete();
            StockOpname::where('id', $id)->delete();

            return redirect('/stock_opname')->withSuccess('Sukses di Hapus!');
        } catch (\Exception $e) {
            return redirect()->back()->withWarning('Gagal di Hapus!');
        }
    }

    public function preview($id)
    {
        $stockOpname = StockOpname::where('id', $id)->orWhere('code', $id)->with('stockOpnameDetails', 'stockOpnameDetails.product', 'stockOpnameDetails.product_unit', 'stockOpnameDetails.product_category', 'inventory', 'user')->first();

        $company = ProfilCompany::where('status', 'active')->first();

        return view('pages.stock_opname.preview', [
            'title' => 'Nomor SO : ' . $stockOpname->code,
            'company' => $company,
            'stockOpname' => $stockOpname,
        ]);
    }

    public function getSoData($code)
    {
        $stockOpname = StockOpname::where('code', $code)->with('stockOpnameDetails', 'stockOpnameDetails.product', 'stockOpnameDetails.product_unit', 'stockOpnameDetails.product_category', 'inventory', 'outlet', 'user')->first();

        $company = ProfilCompany::where('status', 'active')->first();

        return view('pages.stock_opname.report.so-detail', [
            'title' => 'Nomor SO : ' . $stockOpname->code,
            'company' => $company,
            'stockOpname' => $stockOpname,
        ]);
    }
}
