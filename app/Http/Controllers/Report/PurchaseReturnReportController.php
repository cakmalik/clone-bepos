<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseReturnReportController extends Controller
{
    public $PAYMENT_METHODS = [
        'CASH',
        'TRANSFER',
    ];

    public function index()
    {
        $suppliers = Supplier::all();

        return view('pages.report.purchase_return.index', compact('suppliers'));
    }

    public function print(Request $request)
    {
        $user = auth()->user();
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $supplier_id = $request->query('supplier_id');
        $summary_detail = $request->query('summary_detail') == 'detail';

        $query = Purchase::with('outlet', 'supplier', 'user', 'purchaseInvoice.invoicePayments', 'purchase_detail_retur')
            ->whereHas('purchaseInvoice')
            ->whereHas('supplier')
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('purchase_date', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('purchase_date', '<=', $end_date);
            })
            ->when($supplier_id, function ($query) use ($supplier_id) {
                $query->where('supplier_id', '=', $supplier_id);
            })
            ->where('purchase_type', 'Purchase Retur')
            ->where('purchase_status', 'Finish')
            ->whereNotNull('purchase_invoice_id');

        if ($user->role->role_name !== 'SUPERADMIN') {
            $query->whereIn('inventory_id', getUserInventory());
        }

        $orders = $query->get()->toArray();

        $methods = [];
        foreach ($this->PAYMENT_METHODS as $payment_method) {
            $methods[$payment_method] = 0;
        }

        $totalNominalInvoice = 0;
        $totalNominalRetur = 0;
        $totalNominalPo = 0;
        $totalPaid = 0;
        foreach ($orders as &$order) {
            if (!isset($order['purchase_invoice'])) {
                continue;
            }

            $order['purchase_invoice']['paids'] = [];
            $payment_types = collect($order['purchase_invoice']['invoice_payments'])->groupBy('payment_type');
            foreach ($payment_types as $payment_type => $payments) {
                $order['purchase_invoice']['paids'][$payment_type] = $payments->sum('nominal_payment');
                $methods[$payment_type] += $order['purchase_invoice']['paids'][$payment_type];
            }

            $totalNominalInvoice += $order['purchase_invoice']['total_invoice'];
            $totalNominalRetur += $order['purchase_invoice']['nominal_returned'];
            $totalNominalPo += $order['nominal_amount'];
            $totalPaid += $order['purchase_invoice']['nominal_paid'];
        }

        return view('pages.report.purchase_return.print', compact(
            'orders',
            'supplier_id',
            'methods',
            'summary_detail',
            'totalNominalInvoice',
            'totalNominalRetur',
            'totalNominalPo',
            'totalPaid'
        ));
    }
}
