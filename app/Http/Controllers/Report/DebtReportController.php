<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtReportController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();

        return view('pages.report.debt.index', compact('suppliers'));
    }

    public function print(Request $request)
    {
        try {
            $summary_detail = $request->query('summary_detail') == 'detail';
            $supplier_id = $request->query('supplier_id');

            if ($summary_detail) {
                $invoices = DB::table('purchases')
                    ->leftJoin('purchase_invoices', 'purchases.purchase_invoice_id', '=', 'purchase_invoices.id')
                    ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                    ->when($supplier_id, function ($query) use ($supplier_id) {
                        $query->where('purchases.supplier_id', $supplier_id);
                    })
                    ->where('purchase_invoices.is_done', false)
                    ->select(
                        'purchase_invoices.*',
                        'purchases.code as po_code',
                        'suppliers.name as supplier_name',
                    )
                    ->get();

                $invoicesCol = collect($invoices);
                $totalNominal = $invoicesCol->sum('nominal');
                $totalReturned = $invoicesCol->sum('nominal_returned');
                $totalDebt = $invoicesCol->sum('nominal');
                $totalPaid = $invoicesCol->sum('nominal_paid');
                $totalRemain = $totalDebt - $totalPaid;

                return view('pages.report.debt.print', compact(
                    'invoices',
                    'summary_detail',
                    'totalNominal',
                    'totalReturned',
                    'totalDebt',
                    'totalPaid',
                    'totalRemain'
                ));
            } else {
                $debts = PurchaseInvoice::select(
                    'suppliers.name as supplier_name',
                    DB::raw('SUM(purchase_invoices.nominal - purchase_invoices.nominal_paid) as nominal_unpaid'),
                )
                    ->leftJoin('purchases', 'purchases.id', 'purchase_invoices.purchase_id')
                    ->leftJoin('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->when($supplier_id, function ($query) use ($supplier_id) {
                        $query->where('suppliers.id', $supplier_id);
                    })
                    ->groupBy('suppliers.id')
                    ->get();

                return view('pages.report.debt.print', compact('debts', 'summary_detail'));
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
