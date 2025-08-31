<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\UserOutlet;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Purchase::withTrashed()
            ->with('supplier', 'user')
            ->where('purchase_type', 'Purchase Order')
            ->whereBetween('purchase_date', [$start, $end]);

        // Jika status diisi, filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('purchase_status', $request->status);
        }

        $dataPurchase = $query->orderBy('updated_at', 'DESC')->get()
            ->map(function ($purchase) {
                $purchase->formatted_purchase_date = Carbon::parse($purchase->purchase_date)->format('d F Y H:i');
                return $purchase;
            });

        return view('pages.purchase.purchase_order.index', compact('dataPurchase', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $query = DB::table('purchases')->where('purchase_type', 'Purchase Order')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int) $c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }


        $supplier = Supplier::all();

        $purchaseDetail = [];

        $day = now()->subDays(30);

        $purchaseDetail = PurchaseDetail::query()
            ->select('purchase_details.*')
            ->join('purchases', 'purchase_id', 'purchases.id')
            ->where('status', 'Purchase Requisition')
            ->where('purchases.purchase_status', 'Finish')
            ->whereHas('product')
            ->with('product')
            ->orderBy('purchases.id', 'desc')
            ->where('purchase_date', '>=', $day)
            ->get();


        return view('pages.purchase.purchase_order.create', [
            'purchaseDetail' => $purchaseDetail,
            'code' => $cd,
            'supplier' => $supplier,
        ]);
    }

    public function getProduct_po(Request $request)
    {
        $purchase_detail_po = PurchaseDetail::whereIn('id', $request->po_product)->where('status', 'Purchase Requisition')->with('product')->get();

        return response()->json([
            'response' => $purchase_detail_po
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            $purchaseStatus = $request->action === 'finish' ? 'Finish' : 'Draft';

            $PO = Purchase::create([
                'code' => purchaseOrderCode(),
                // 'inventory_id' => myInventoryId(), ini di hide untuk custom destinasi penerimaan barang
                'purchase_date' => $request->purchase_date,
                'user_id' => Auth()->user()->id,
                'supplier_id' => $request->supplier_id,
                'purchase_type' => 'Purchase Order',
                'purchase_status' => $purchaseStatus
            ]);

            $PR = PurchaseDetail::whereIn('id', $request->items_po)->get();

            foreach ($PR as $pr) {
                PurchaseDetail::where('id', $pr->id)->update([
                    'purchase_po_id' => $PO->id,
                    'status' => 'Purchase Order',
                ]);
            }

            DB::commit();

            if ($purchaseStatus === 'Finish') {
                return redirect('/purchase_order_print/' . $PO->id)->with('success', 'Pesanan berhasil dibuat dan diselesaikan.');
            }

            return redirect('/purchase_order/' . $PO->id . '/edit')->withSuccess('Sukses disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal disimpan!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier = Supplier::all();
        $purchase = Purchase::where('purchase_type', 'Purchase Order')
            ->where('id', $id)
            ->with('inventory', 'user', 'supplier')
            ->first();

        $purchaseDetails = PurchaseDetail::query()
            ->where('status', 'Purchase Order')
            ->where('purchase_po_id', $id)
            ->whereHas('product')
            ->with('product')
            ->get();

        $product_id = [];
        foreach ($purchaseDetails as $pod) {
            $product_id[] = $pod->product_id;
        }


        $purchaseDetail = PurchaseDetail::query()
            ->whereHas('product')
            ->where('status', 'Purchase Requisition')
            ->whereNotIn('product_id', $product_id)
            ->with('product.ProductSupplier.supplier')
            ->get();

        return view('pages.purchase.purchase_order.edit', [
            'purchase' => $purchase,
            'purchaseDetail' => $purchaseDetail,
            'purchaseDetails' => $purchaseDetails,
            'supplier' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $supplierId = $request->supplier_id_update ? $request->supplier_id_update : $request->supplier_id_current;

            Purchase::where('id', $request->purchase_po_id)->update([
                'supplier_id' => $supplierId
            ]);


            $purchaseDetail = PurchaseDetail::where('purchase_po_id', $request->purchase_po_id)->first();

            $purchase_id = $purchaseDetail->purchase_id;

            PurchaseDetail::where('purchase_po_id', $request->purchase_po_id)->forceDelete();

            if ($request->items_current && !$request->items_other) {
                $product_current = Product::whereIn('id', $request->items_current)->get();

                foreach ($product_current as $pd) {

                    $product_id = $pd->id . '_id';
                    $product_name = $pd->id . '_name';
                    $product_qty = $pd->id . '_qty';
                    $product_hpp = $pd->id . '_hpp';
                    $product_subtotal = $pd->id . '_subtotal';
                    PurchaseDetail::create([
                        'inventory_id' => myInventoryId(),
                        'code' => $request->code,
                        'purchase_id' => $purchase_id,
                        'purchase_po_id' => $request->purchase_po_id,
                        'product_id' => $request->$product_id,
                        'product_name' => $request->$product_name,
                        'qty' => $request->$product_qty,
                        'price' => $request->$product_hpp,
                        'subtotal' => $request->$product_subtotal,
                        'status' => 'Purchase Order'
                    ]);
                }
            } elseif (!$request->items_current and $request->items_other) {
                $product_other_update = Product::whereIn('id', $request->items_other)->get();

                foreach ($product_other_update as $pd_other) {

                    $product_id = $pd_other->id . '_id';
                    $product_name = $pd_other->id . '_name';
                    $product_qty = $pd_other->id . '_qty';
                    $product_hpp = $pd_other->id . '_hpp';
                    $product_subtotal = $pd_other->id . '_subtotal';
                    PurchaseDetail::create([
                        'inventory_id' => myInventoryId(),
                        'code' => $request->code,
                        'purchase_id' => $purchase_id,
                        'purchase_po_id' => $request->purchase_po_id,
                        'product_id' => $request->$product_id,
                        'product_name' => $request->$product_name,
                        'qty' => $request->$product_qty,
                        'price' => $request->$product_hpp,
                        'subtotal' => $request->$product_subtotal,
                        'status' => 'Purchase Order'
                    ]);
                }
            } elseif ($request->items_current and $request->items_other) {

                $product_current_update = Product::whereIn('id', $request->items_current)->get();
                $product_other_update = Product::whereIn('id', $request->items_other)->get();

                foreach ($product_current_update as $pdd) {

                    $product_id = $pdd->id . '_id';
                    $product_name = $pdd->id . '_name';
                    $product_qty = $pdd->id . '_qty';
                    $product_hpp = $pdd->id . '_hpp';
                    $product_subtotal = $pdd->id . '_subtotal';
                    PurchaseDetail::create([
                        'inventory_id' => myInventoryId(),
                        'code' => $request->code,
                        'purchase_id' => $purchase_id,
                        'purchase_po_id' => $request->purchase_po_id,
                        'product_id' => $request->$product_id,
                        'product_name' => $request->$product_name,
                        'qty' => $request->$product_qty,
                        'price' => $request->$product_hpp,
                        'subtotal' => $request->$product_subtotal,
                        'status' => 'Purchase Order'
                    ]);
                }

                foreach ($product_other_update as $pd_other) {

                    $product_id = $pd_other->id . '_id';
                    $product_name = $pd_other->id . '_name';
                    $product_qty = $pd_other->id . '_qty';
                    $product_hpp = $pd_other->id . '_hpp';
                    $product_subtotal = $pd_other->id . '_subtotal';
                    PurchaseDetail::create([
                        'inventory_id' => myInventoryId(),
                        'code' => $request->code,
                        'purchase_id' => $purchase_id,
                        'purchase_po_id' => $request->purchase_po_id,
                        'product_id' => $request->$product_id,
                        'product_name' => $request->$product_name,
                        'qty' => $request->$product_qty,
                        'price' => $request->$product_hpp,
                        'subtotal' => $request->$product_subtotal,
                        'status' => 'Purchase Order'
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Sukses di Update!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal di Update!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     DB::beginTransaction();

    //     try {
    //         Purchase::where('id', $id)->update(['purchase_status' => 'Void']);

    //         PurchaseDetail::where('status', 'Purchase Order')->where('purchase_id', $id)->delete();

    //         Purchase::destroy($id);

    //         DB::commit();
            
    //         return redirect('/purchase_order')->with('success', 'Berhasil dibatalkan.');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Gagal membatalkan!');
    //     }
    // }

    public function getProduct_update(Request $request)
    {
        $po_other_insert = PurchaseDetail::whereIn('product_id', $request->PO_other)->with('product')->get();

        return response()->json([
            'response' => $po_other_insert
        ]);
    }


    public function finish(Request $request)
    {
        DB::beginTransaction();
        try {
            Purchase::where('id', $request->id)->update([
                'purchase_status' => 'Finish'
            ]);

            DB::commit();
            return redirect('/purchase_order_print/' . $request->id)->with('success', 'Pesanan berhasil di selesaikan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal di Selesaikan!');
        }
    }

    public function void(Request $request)
    {

        DB::beginTransaction();
        try {
            Purchase::where('id', $request->id)->update([
                'purchase_status' => 'Void',
            ]);

            PurchaseDetail::where('purchase_po_id', $request->id)->update([
                'status' => 'Purchase Requisition',
                'purchase_po_id' => NULL
            ]);
            DB::commit();
            return redirect('/purchase_order')->with('success', 'Sukses di Void!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal di Void!');
        }
    }


    public function reset($id)
    {
        PurchaseDetail::where('id', $id)->update([
            'status' => 'Purchase Requisition',
            'purchase_po_id' => NULL
        ]);
        return redirect()->back();
    }

    public function print($id)
    {

        $data_void = PurchaseDetail::where('purchase_po_id', $id)->where('purchase_receipt_id', '!=', NULL)->count();
        $void_true = true;

        if ($data_void > 0) {
            $void_true = false;
        }

        $company = profileCompany();
        $purchase = Purchase::where('id', $id)->with('purchase_detail_po', 'outlet', 'user')->first();
        $sum = PurchaseDetail::where('purchase_po_id', $id)->sum('subtotal');

        return view('pages.purchase.purchase_order.print', [
            'title' => 'ID TRANSAKSI - ' . $purchase->code,
            'purchase' => $purchase,
            'company' => $company,
            'sum' => $sum,
            'void' => $void_true
        ]);
    }

    public function nota($code)
    {
        $purchase = Purchase::with(
            'purchase_detail_po',
            'inventory',
            'user'
        )
            ->where('code', $code)
            ->first();

        if (!$purchase) {
            return abort(404, 'Purchase not found.');
        }

        if ($purchase->purchase_detail_po->where('status', 'Material Receipt')->isNotEmpty()) {
            $purchase->purchase_detail_po = $purchase->purchase_detail_po->where('status', 'Material Receipt')->where('is_bonus', 0);
        } else {
            $purchase->purchase_detail_po = $purchase->purchase_detail_po->where('status', 'Purchase Order')->where('is_bonus', 0);
        }

        $sum = $purchase->purchase_detail_po->sum('subtotal');

        $data = [
            'company' => profileCompany(),
            'purchase' => $purchase,
            'sum' => $sum
        ];

        return view('pages.purchase.purchase_order.nota', $data);
    }


    public function getDataSupplier(Request $request)
    {
        try {
            $supplier = Supplier::findOrFail($request->id);

            $purchases = Purchase::where('supplier_id', $supplier->id)
                ->with('purchaseDetails')
                ->get();
            // Calculate sum for each purchase
            $purchaseData = [];
            foreach ($purchases as $purchase) {
                $sum = $purchase->purchaseDetails->sum('subtotal');
                $formattedSum = 'Rp ' . number_format($sum, 0, ',', '.');
                $purchaseData[] = [
                    'id' => $purchase->id,
                    'code' => $purchase->code,
                    'purchase_status' => $purchase->purchase_status,
                    'purchase_date' => $purchase->purchase_date,
                    'purchase_type' => $purchase->purchase_type,
                    'sum' => $formattedSum,
                ];
            }
            return response()->json([
                'supplier' => $supplier,
                'purchases' => $purchaseData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getDetailProductPO(Request $request)
    {

        $purchaseDetail = PurchaseDetail::whereIn('status', ['Purchase Order', 'Material Receipt'])
            ->where('purchase_po_id', $request->id)
            ->with('product')
            ->get();

        return response()->json($purchaseDetail);
    }

    public function cancelOrder($id)
    {
        DB::beginTransaction();

        try {
            // 1. Ubah status Purchase menjadi Void
            Purchase::where('id', $id)->update([
                'purchase_status' => 'Void',
            ]);

            // 2. Ubah status PurchaseDetail dari "Purchase Order" menjadi "Purchase Requisition"
            PurchaseDetail::where('purchase_po_id', $id)
                        ->where('status', 'Purchase Order')
                        ->update(['status' => 'Purchase Requisition']);

            //destroy purchase
            Purchase::where('id', $id)->delete();

            DB::commit();
            
            return redirect('/purchase_order')->with('success', 'Order berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan order!');
        }
    }

}
