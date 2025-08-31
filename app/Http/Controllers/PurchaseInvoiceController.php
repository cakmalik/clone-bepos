<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseInvoiceExport;
use Yajra\DataTables\Facades\DataTables;

class PurchaseInvoiceController extends Controller
{

    private function queryPurchaseInvoice($request)
    {
        return PurchaseInvoice::query()
            ->with(['purchase' => function ($query) {
                $query->where('purchases.inventory_id', getUserInventory());
            }, 'purchase.supplier'])
            ->where(function ($query) use ($request) {
                if ($request->start_date != '' && $request->end_date != '') {
                    $query->where([
                        ['invoice_date', '>=', startOfDay($request->start_date)],
                        ['invoice_date', '<=', endOfDay($request->end_date)]
                    ]);
                }

                if ($request->status != '') {
                    $query->where('is_done', $request->status);
                }
            })
            // ->orderBy('updated_at', 'desc') jika diterapkan, saat mau retur - bug: tidak muncul produk
            ->get()
            ->map(function ($row) {
                $row->invoice_date = dateStandar($row->invoice_date);
                $row->nominal_rp = decimalToRupiahView($row->nominal);
                $row->nominal_discount_rp = decimalToRupiahView($row->nominal_discount);
                $row->nominal_total_inv = decimalToRupiahView($row->total_invoice);
                $row->nominal_returned_rp = decimalToRupiahView($row->nominal_returned);
                $row->nominal_paid_rp = decimalToRupiahView($row->nominal_paid);
                $row->is_done = $row->is_done ? 'LUNAS' : 'BELUM LUNAS';

                return $row;
            });
    }



    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchaseInvoices = $this->queryPurchaseInvoice($request);

            return DataTables::of($purchaseInvoices)
                ->addIndexColumn()
                ->addColumn('invoice_number', function ($row) {
                    return $row->invoice_number;
                })
                ->addColumn('po_code', function ($row) {
                    $code = $row->code;
                    return '<a href="purchase_order_print/' . $row->purchase_id . '" data-original-title="Show">' .
                        $code . '</a>';
                })
                
                ->addColumn('is_done', function ($row) {
                    return '<span class="badge bg-' . ($row->is_done == 'LUNAS' ? 'green-lt' : 'orange-lt') . '">' . $row->is_done . '</span>';
                })
                ->addColumn('detail', function ($row) {
                    return '<button class="btn btn-sm btn-outline-primary rounded" id="viewDetailButton" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#detailModal"><i class="fa fa-receipt me-1" aria-hidden="true"></i> Detail</button>
                    
                    <a href="purchase_invoice_show/' . $row->id . '" class="btn btn-sm btn-outline-primary rounded" data-original-title="Print">' .
                        '<i class="fa fa-print me-1"></i> Cetak</a>';
                })
                ->rawColumns(['invoice_number', 'po_code', 'is_done', 'detail', 'print'])->make(true);
        }

        return view('pages.purchase.purchase_invoice.index');
    }

    public function create()
    {
        $query = DB::table('purchase_invoices')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd =  "0001";
        }

        $PO = DB::table('purchases')
            ->join('purchase_details', function ($join) {
                $join->on('purchases.id', '=', 'purchase_details.purchase_po_id')
                    ->where('purchase_details.is_bonus', 0);
            })
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            // ->where('purchase_type', 'Purchase Order')
            ->whereNotNull('purchases.ref_code')
            ->where('purchase_status', 'Finish')
            ->where('purchase_invoice_id', null)
            ->where('is_invoiced', false)
            ->selectRaw('
            purchases.id as purchase_id, 
            purchases.code, 
            purchases.purchase_date, 
            suppliers.name as supplier, 
            CAST(SUM(purchase_details.subtotal) AS UNSIGNED) as subtotal
        ')
            ->groupBy('purchase_po_id', 'purchases.id', 'purchases.code', 'purchases.purchase_date', 'suppliers.name')
            ->get();


        // dd($PO);


        $purchase_invoice = PurchaseInvoice::with('purchase')->get();

        return view('pages.purchase.purchase_invoice.create', [
            'PO'    => $PO,
            'code'  => $cd,
            'purchase_invoice' => $purchase_invoice
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'code' => 'required',
            'invoice_number' => 'required|max:255',
            'invoice_date' => 'required',
            'po_code' => 'required',
            'supplier' => 'required',
            'nominal' => 'required',
            'nomimal_discount' => 'nullable',
            'total_invoice' => 'required',
        ], [], [
            'invoice_number' => 'No. Tagihan Supplier',
            'invoice_date' => 'Tanggal',
            'po_code' => 'Kode PO',
            'supplier' => 'Supplier',
            'nominal' => 'Nominal',
            'nominal_discount' => 'Diskon',
            'total_invoice' => 'Total Tagihan',
        ]);

        try {


            $PO = Purchase::where('code', $request->po_code)->first();

            if ($PO->ref_code == null) {
                return redirect()->route('purchaseInvoice.index')
                    ->with('error', 'Barang dari Purchase Order belum diterima!');
            }

            $PI = PurchaseInvoice::create([
                'code' => $request->code,
                'purchase_id' => $request->id,
                'invoice_date' => $request->invoice_date . ' ' . $request->time,
                'invoice_number' => $request->invoice_number,
                'nominal' => $request->nominal,
                'nominal_discount' => $request->nominal_discount ?? 0,
                'total_invoice' => $request->total_invoice,
                'nominal_returned' => $request->nominal_returned ?? 0,
                'nominal_paid' => $request->nominal_paid ?? 0,
                'is_done' => false,
                'user_id' => auth()->user()->id
            ]);

            Purchase::where(function ($query) use ($request) {
                $query->where('code', $request->po_code) // PO
                      ->orWhere('ref_code', $request->po_code); // PN
            })->update([
                'purchase_invoice_id' => $PI->id
            ]);

            DB::commit();
            return redirect()->route('purchaseInvoice.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchaseInvoice.index')->with('error', 'Data gagal disimpan! Silakan coba lagi.');
        }
    }



    public function export(Request $request)
    {
        $purchaseInvoices = $this->queryPurchaseInvoice($request);

        if ($request->type == 'pdf') {
            $pdf = PDF::loadView('export.purchase_invoice_pdf', compact('purchaseInvoices'));

            return $pdf->stream('Report Purchase Invoice.pdf');
        } else {
            return Excel::download(new PurchaseInvoiceExport($purchaseInvoices), 'Report Purchase Invoice.xlsx');
        }
    }

    public function getInvoiceDetails($id)
    {
        $invoice = PurchaseInvoice::with('purchase.supplier')->find($id);

        return view('pages.purchase.purchase_invoice.detail', compact('invoice'));
    }

    //show
    public function show($id)
    {
        $purchaseInvoice = PurchaseInvoice::with('purchase')->find($id);

        return view('pages.purchase.purchase_invoice.show', compact('purchaseInvoice'));
    }

    public function nota($code)
    {
        $purchase = Purchase::with(
            'purchase_detail_po',
            'inventory',
            'user',
            'purchaseInvoice'
        )
            ->where('code', $code)
            ->first();

        if (!$purchase) {
            return abort(404, 'Purchase not found.');
        }

        if ($purchase->purchase_detail_po->where('status', 'Material Receipt')->isNotEmpty()) {
            $purchase->purchase_detail_po = $purchase->purchase_detail_po
                ->where('status', 'Material Receipt')
                ->where('is_bonus', 0);
        } else {
            $purchase->purchase_detail_po = $purchase->purchase_detail_po
                ->where('status', 'Purchase Order')
                ->where('is_bonus', 0);
        }

        $sum = $purchase->purchase_detail_po->sum('subtotal');

        $data = [
            'company' => profileCompany(),
            'purchase' => $purchase,
            'sum' => $sum,
            'purchaseInvoice' => $purchase->purchaseInvoice
        ];

        return view('pages.purchase.purchase_invoice.nota', $data);
    }
}
