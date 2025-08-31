<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Purchase;
use App\Models\Inventory;
use App\Models\UserOutlet;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use Illuminate\Support\Carbon;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $retur = Purchase::withTrashed()
                ->select('purchases.*')
                ->with('user', 'inventory', 'outlet')
                ->where([
                    ['purchase_type', 'Purchase Retur'],
                    ['purchase_status', $request->status]
                ])
                ->where(function ($query) use ($request) {
                    if ($request->start_date != '' && $request->end_date != '') {
                        $query->where([
                            ['purchase_date', '>=', startOfDay($request->start_date)],
                            ['purchase_date', '<=', endOfDay($request->end_date)]
                        ]);
                    }
                })
                ->orderBy('updated_at', 'desc')
                ->get()->map(function ($row) {
                    $row->purchase_date = Carbon::parse($row->purchase_date)->format('d F Y H:i');
                    return $row;
                });

            return DataTables::of($retur)
                ->addIndexColumn()
                ->addColumn('pr_code', function ($row) {
                    if ($row->purchase_status == 'Draft') {
                        $code_link =  $row->code;
                    } else {
                        $code_link = $row->code;
                    }

                    return $code_link;
                })

                ->addColumn('status', function ($row) {
                    if ($row->purchase_status == 'Draft') {
                        $status = '<span class="badge badge-sm text-uppercase bg-warning-lt">Draft</span>';
                    } else if ($row->purchase_status == 'Open') {
                        $status = ' <span class="badge badge-sm text-uppercase bg-yellow-lt">Open</span>';
                    } else if ($row->purchase_status == 'Finish') {
                        $status = '<span class="badge badge-sm text-uppercase bg-green-lt">Selesai</span>';
                    } else {
                        $status = '<span class="badge badge-sm text-uppercase bg-red-lt">Batal</span>';
                    }

                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $print = '';

                    if ($row->purchase_status == 'Draft') {
                        $print = '<a href="purchase_return/' . $row->id . '/edit" class="btn btn-md btn-outline-dark">
                                    <i class="fa fa-edit me-1 text-primary" aria-hidden="true"></i>
                                </a>';
                    } else if ($row->purchase_status == 'Finish') {
                        $print = '<a href="purchase_return_print/' . $row->id . '" class="btn btn-md btn-outline-primary">
                                    <i class="fa fa-print me-1" aria-hidden="true"></i>
                                </a>';
                    }

                    return $print;
                })

                ->addColumn('receiver', function ($row) {
                    return $row->inventory->name ?? $row->outlet->name ?? '-';
                })

                ->rawColumns(['status', 'pr_code', 'action', 'receiver'])->make(true);
        }

        return view('pages.purchase.purchase_return.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $query = DB::table('purchases')->where('purchase_type', 'Purchase Retur')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd =  "0001";
        }

        $invoice = DB::table('purchases')
            ->join('purchase_invoices', 'purchases.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->leftJoin('inventories', 'purchases.inventory_id', '=', 'inventories.id')
            ->leftJoin('outlets', 'purchases.outlet_id', '=', 'outlets.id')
            ->where('purchase_invoices.is_done', true)
            ->where('purchase_type', 'Purchase Order')
            ->where('nominal_returned', 0)
            ->select(
                'purchases.code as po_code', 
                'purchase_invoices.invoice_number',
                'suppliers.name as supplier',
                'purchases.id as po_id',
                'suppliers.id as supplier_id',
                'purchase_invoices.id',
                'inventories.name as inventory_name',
                'outlets.name as outlet_name'
            )
            ->get();

        $pn = PurchaseDetail::where('status', 'Material Receipt')->get();

        return view('pages.purchase.purchase_return.create', [
            'inventory' => Inventory::find(myInventoryId()),
            'code' => $cd,
            'invoice' => $invoice,
            'pn' => $pn
        ]);
    }

    public function getDataPO(Request $request)
    {

        $response = PurchaseDetail::where('purchase_po_id', $request->id)
            ->where('status', 'Material Receipt')->where('returned_qty', 0)->get();


        return response()->json([
            'response' => $response,
        ]);
    }


    public function getDataRetur(Request $request)
    {
        $response = PurchaseDetail::with('purchase')
            ->whereIn('id', $request->pn_id)
            ->get();

        return response()->json([
            'response' => $response,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // dd($request->all());
        DB::beginTransaction();
        
        try {
            $purchaseOrder = Purchase::where('code', $request->po_code)->firstOrFail();

            $purchaseStatus = $request->action === 'finish' ? 'Finish' : 'Draft';

            $RT = Purchase::create([
                'user_id' => auth()->id(),
                'inventory_id' => $purchaseOrder->inventory_id,
                'outlet_id' => $purchaseOrder->outlet_id,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_invoice_id' => $request->invoice_id,
                'code' => $request->code,
                'ref_code' => $request->po_code,
                'purchase_type' => 'Purchase Retur',
                'purchase_status' => $purchaseStatus,
            ]);

            $returIds = $request->po_id ?? [];

            if (!is_array($returIds)) {
                $returIds = [$returIds];
            }

            $retur = PurchaseDetail::whereIn('id', $returIds)->get();

            foreach ($retur as $rt) {
                $retur_qty = $rt->id . '_retur_qty';

                // Update returned_qty pada PO detail
                $rt->update([
                    'returned_qty' => $request->$retur_qty,
                ]);

                // Data untuk Purchase Retur Detail
                $product_name = $rt->id . '_retur_name';
                $qty = $rt->id . '_retur_qty';
                $price = $rt->id . '_price';
                $subtotal = $rt->id . '_subtotal';

                PurchaseDetail::create([
                    'inventory_id' => $rt->inventory_id,
                    'outlet_id' => $rt->outlet_id,
                    'purchase_id' => $RT->id,
                    'product_id' => $rt->product_id,
                    'purchase_po_id' => $rt->purchase_po_id,
                    'purchase_receipt_id' => $rt->purchase_receipt_id,
                    'code' => $request->code,
                    'product_name' => $request->$product_name,
                    'qty' => $request->$qty,
                    'price' => $request->$price,
                    'subtotal' => $request->$subtotal,
                    'status' => 'Purchase Retur',
                ]);
            }

            // Jika langsung ingin diselesaikan
            if ($request->action === 'finish') {
                $this->__finalizeReturn($RT);
            }

            DB::commit();

            return redirect(
                $request->action === 'draft'
                    ? '/purchase_return/' . $RT->id . '/edit'
                    : '/purchase_return_print/' . $RT->id
            )->with('success', $request->action === 'draft' ? 'Disimpan sebagai Draf!' : 'Retur selesai diproses!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('warning', $e->getMessage() ?? 'Gagal disimpan!');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchase_retur = Purchase::where('id', $id)->first();
        $PO = Purchase::where('code', $purchase_retur->ref_code)->first();
        $invoice_supplier = PurchaseInvoice::where('purchase_id', $PO->id)->first();
        $product_retur = PurchaseDetail::where('purchase_po_id', $PO->id)->where('status', 'Material Receipt')->get();

        return view('pages.purchase.purchase_return.edit', [
            'purchase_retur' => $purchase_retur,
            'product_retur' => $product_retur,
            'invoice_supplier' => $invoice_supplier,
            'PO' => $PO
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {

        DB::beginTransaction();
        try {
            $retur = PurchaseDetail::where('purchase_id', $request->retur_id)->get();
            $purchase_retur = Purchase::where('id', $request->retur_id)->first();

            $PO = Purchase::where('code', $purchase_retur->ref_code)->where('purchase_type', 'Purchase Order')->first();
            $POD = PurchaseDetail::where('purchase_po_id', $PO->id)->where('status', 'Material Receipt')->get();


            foreach ($POD as $p) {
                $returned_qty = $p->id . '_returned_qty';
                $retur_qty = $p->id . '_retur_qty';
                $subtotal = $p->id . '_subtotal';
                PurchaseDetail::where('id', $p->id)->update([
                    'returned_qty' => $request->$returned_qty + $request->$retur_qty
                ]);

                PurchaseDetail::where('purchase_id', $request->retur_id)->where('product_id', $p->product_id)->update([
                    'qty' => $request->$returned_qty + $request->$retur_qty,
                    'subtotal' => $request->$subtotal
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Sukses di Update!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('warning', 'Gagal di Update!');
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();

        $retur = Purchase::where('id', $id)->first();
        $po = Purchase::where('code', $retur->ref_code)->first();

        try {
            // Ubah purchase_status menjadi 'void'
            $retur->purchase_status = 'Void';
            $retur->save();

            Purchase::destroy($id);

            PurchaseDetail::where('status', 'Purchase Retur')->where('purchase_id', $id)->delete();

            PurchaseDetail::where('purchase_po_id', $po->id)->update([
                'returned_qty' => 0,
            ]);
            DB::commit();
            
             return redirect('/purchase_return')->with('success', 'Berhasil dibatalkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan!');
        }
    }

    public function finish(Request $request)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::with('purchase_detail_retur')->findOrFail($request->id);

            $this->__finalizeReturn($purchase);

            DB::commit();
            return redirect('/purchase_return')->with('success', 'Sukses di Simpan!');
        } catch (\Exception $e) {

            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('warning', $e->getMessage() ?? 'Gagal di Simpan!');
        }
    }

    public function getDetailProductRT(Request $request)
    {

        $purchase_retur = PurchaseDetail::where('purchase_po_id', $request->id)->where('status', 'Material Receipt')->get();

        return response()->json($purchase_retur);
    }

    // print retur
    public function print($id)
    {

        $company = profileCompany();
        $purchase = Purchase::where('id', $id)->with('purchase_detail_retur', 'outlet', 'user')->first();

        $data = [
            'title' => 'ID TRANSAKSI - ' . $purchase->code,
            'company' => $company,
            'purchase' => $purchase,
        ];

        return view('pages.purchase.purchase_return.print', $data);
    }

    public function nota($code)
    {
        $purchase = Purchase::with(
            'purchase_detail_retur',
            'inventory',
            'user'
        )->where('code', $code)->first();

        $sum = PurchaseDetail::where('purchase_po_id', $purchase->id)->sum('subtotal');


        $data = [
            'company' => profileCompany(),
            'purchase' => $purchase,
            'sum' => $sum
        ];

        return view('pages.purchase.purchase_return.nota', $data);
    }

    private function __finalizeReturn(Purchase $purchase)
    {
        foreach ($purchase->purchase_detail_retur as $pr) {
            $stockQuery = ProductStock::query()
                ->where('product_id', $pr->product_id);

            if ($purchase->inventory_id) {
                $stockQuery->where('inventory_id', $purchase->inventory_id);
            } else {
                $stockQuery->where('outlet_id', $purchase->outlet_id);
            }

            $product_stock = $stockQuery->first();

            if (!$product_stock) {
                throw new \Exception('Produk tidak ada di gudang / outlet');
            }

            $stock_before = $product_stock->stock_current;
            $stock_after = $stock_before - $pr->qty;

            $product_stock->update(['stock_current' => $stock_after]);

            $action_type = $stock_after > $stock_before ? 'PLUS' : 'MINUS';
            $description = $action_type === 'PLUS' ? 'PENAMBAHAN' : 'PENGURANGAN';

            ProductStockHistory::create([
                'document_number' => $purchase->code,
                'history_date' => now(),
                'action_type' => $action_type,
                'product_id' => $product_stock->product_id,
                'inventory_id' => $product_stock->inventory_id,
                'outlet_id' => $product_stock->outlet_id,
                'stock_change' => $pr->qty,
                'stock_before' => $stock_before,
                'stock_after' => $stock_after,
                'desc' => 'Retur (RP): ' . $purchase->code . ' melakukan ' . $description . ' QTY',
                'user_id' => auth()->id(),
            ]);
        }

        // Update status & nominal retur
        $purchase->update(['purchase_status' => 'Finish']);

        $totalReturn = $purchase->purchase_detail_retur->sum('subtotal');

        PurchaseInvoice::where('id', $purchase->purchase_invoice_id)
            ->update(['nominal_returned' => $totalReturn]);
    }

}
