<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\InvoicePayment;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoicePaymentController extends Controller
{
    public function index()
    {
        $purchase = DB::table('purchases')
            ->join('purchase_invoices', 'purchases.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchase_invoices.is_done', false)
            ->where('purchases.purchase_type', 'Purchase Order')
            ->selectRaw(
                'purchases.supplier_id as supplier_id,
                suppliers.code as code,
                suppliers.name as supplier,
                sum(total_invoice) as debt,
                sum(nominal_paid) as nominal_paid,
                min(invoice_date) as invoice_date
            '
            )->groupBy(
                'supplier_id',
                'suppliers.code',
                'purchases.supplier_id',
                'suppliers.name'
            )->get();

        return view('pages.purchase.invoice_payment.index', [
            'purchase'  => $purchase
        ]);
    }

    public function create($id)
    {
        $query = DB::table('invoice_payments')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd =  "0001";
        }

        $purchase = DB::table('purchases')
            ->join('purchase_invoices', 'purchases.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.purchase_type', 'Purchase Order')
            ->where('purchase_invoices.is_done', false)
            ->select(
                'purchases.id as id',
                'purchase_invoices.id as id_invoice',
                'purchases.supplier_id as supplier_id',
                'suppliers.name as supplier'
            )->first();

        $invoice = DB::table('purchases')
            ->join('purchase_invoices', 'purchases.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->where('purchases.supplier_id', $id)
            ->where('purchase_invoices.is_done', false)
            ->where('purchases.purchase_type', 'Purchase Order')
            ->select(
                'purchase_invoices.*',
                'purchases.code as po_code'
            )->get()->map(function ($row) {
                $row->invoice_date = dateWithTime($row->invoice_date);

                return $row;
            });

        $paymentMethods = DB::table('payment_methods')
            ->where('name', '!=', 'Tempo')
            ->get();

        return view('pages.purchase.invoice_payment.create', [
            'code' => $cd,
            'purchase' => $purchase,
            'invoice' => $invoice,
            'paymentMethods' => $paymentMethods
        ]);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        $rawNominal = preg_replace('/\D/', '', $request->nominal);
        $nominalInt = (int) $rawNominal;

        $validator = Validator::make($request->all(), [
            'code'              => 'required',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'nominal'           => 'required|string',
            'description'       => 'required',
            'id_invoice'        => 'required|exists:purchase_invoices,id',
        ], [
            'payment_method_id.required' => 'Metode Pembayaran wajib diisi',
        ], [
            'payment_method_id' => 'Metode Pembayaran',
            'nominal'           => 'Nominal',
            'description'       => 'Keterangan',
            'id_invoice'        => 'Tagihan',
        ]);

        $invoice = PurchaseInvoice::find($request->id_invoice);
        if ($invoice) {
            $sisa = $invoice->total_invoice - $invoice->nominal_paid;
            $validator->after(function ($validator) use ($nominalInt, $sisa) {
                if ($nominalInt > $sisa) {
                    $validator->errors()->add('nominal', 'Nominal melebihi batas maksimal (Rp.' . number_format($sisa, 0, ',', '.') . ')');
                }
            });
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            InvoicePayment::create([
                'code'                 => invoicePaymentCode(),
                'purchase_invoice_id'  => $request->id_invoice,
                'payment_method_id'    => $request->payment_method_id,
                'user_id'              => getUserIdLogin(),
                'payment_date'         => $request->payment_date,
                'nominal_payment'      => $nominalInt,
                'description'          => $request->description,
            ]);

            // Update nominal_paid dan status is_done
            $invoice->nominal_paid += $nominalInt;
            if ($invoice->nominal_paid >= $invoice->total_invoice) {
                $invoice->is_done = true;
            }
            $invoice->save();

            DB::commit();
            return redirect('/invoice_payment')->with('success', 'Sukses disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan InvoicePayment: '.$e->getMessage());
            return redirect()->back()->with('error', 'Gagal disimpan!');
        }
    }

}
