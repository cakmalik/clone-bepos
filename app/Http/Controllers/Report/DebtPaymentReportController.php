<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\InvoicePayment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DebtPaymentReportController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('pages.report.debt_payment.index', compact(
            'suppliers',
        ));
    }

    public function print(Request $request)
    {
        try {
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $supplier_id = $request->query('supplier_id');

            $payments = InvoicePayment::select(
                'invoice_payments.payment_date',
                'purchase_invoices.code',
                'purchase_invoices.invoice_number',
                'suppliers.name as supplier_name',
                'invoice_payments.payment_type',
                'invoice_payments.nominal_payment',
            )
                ->leftJoin('purchase_invoices', 'purchase_invoices.id', '=', 'invoice_payments.purchase_invoice_id')
                ->leftJoin('purchases', 'purchase_invoices.purchase_id', '=', 'purchases.id')
                ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('invoice_payments.payment_date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('invoice_payments.payment_date', '<=', $end_date);
                })
                ->when($supplier_id, function ($q) use ($supplier_id) {
                    $q->where('suppliers.id', $supplier_id);
                })
                ->get();

            $totalPayment = collect($payments)->sum('nominal_payment');

            return view('pages.report.debt_payment.print', compact(
                'start_date',
                'end_date',
                'supplier_id',
                'payments',
                'totalPayment',
            ));
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
