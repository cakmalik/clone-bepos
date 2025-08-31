<?php

namespace App\Http\Controllers\Report;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;

class PurchaseOrderReportController extends Controller
{
    /**
     * Array berisi [id => nama] semua metode pembayaran (kecuali TEMPO).
     */
    public $PAYMENT_METHODS = [];

    public function __construct()
    {
        // Ambil semua metode pembayaran kecuali yang bernama “TEMPO”
        // Simpan dalam format [id => name]
        $this->PAYMENT_METHODS = PaymentMethod::where('name', '!=', 'TEMPO')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function index()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('pages.report.purchase_order.index', compact('suppliers'));
    }

    public function print(Request $request)
    {
        $user = auth()->user();
        $start_date     = $request->query('start_date');
        $end_date       = $request->query('end_date');
        $supplier_id    = $request->query('supplier_id');
        $summary_detail = $request->query('summary_detail') == 'detail';

        // 1) Ambil data Purchase beserta relasi yang diperlukan
        $query = Purchase::with([
                'outlet',
                'supplier',
                'user',
                // Eager load invoice + payments + relasi paymentMethod 
                'purchaseInvoice.invoicePayments.paymentMethod',
                'purchase_detail_po'
            ])
            ->whereHas('purchaseInvoice')
            ->whereHas('supplier')
            ->when($start_date, function ($q) use ($start_date) {
                $q->whereDate('purchase_date', '>=', $start_date);
            })
            ->when($end_date, function ($q) use ($end_date) {
                $q->whereDate('purchase_date', '<=', $end_date);
            })
            ->when($supplier_id, function ($q) use ($supplier_id) {
                $q->where('supplier_id', $supplier_id);
            })
            ->where('purchase_type', 'Purchase Order')
            ->where('purchase_status', 'Finish')
            ->whereNotNull('purchase_invoice_id');

        if ($user->role->role_name !== 'SUPERADMIN') {
            $query->whereIn('inventory_id', getUserInventory());
        }

        $orders = $query->get()->toArray();

        // 2) Siapkan array metode pembayaran berdasarkan ID (nilai awal = 0)
        //    $methods akan berisi [id => total_paid], misal [1 => 0, 2 => 0, ...]
        $methods = array_fill_keys(array_keys($this->PAYMENT_METHODS), 0);

        // 3) Inisialisasi total lain
        $totalNominalPo        = 0;
        $totalNominalDiscount  = 0;
        $totalInvoice          = 0;
        $totalPaid             = 0;

        foreach ($orders as &$order) {
            if (! isset($order['purchase_invoice'])) {
                continue;
            }

            // Siapkan struktur paids per invoice
            // Akan menyimpan [payment_method_id => sum_nominal]
            $order['purchase_invoice']['paids'] = [];

            // groupBy berdasarkan payment_method_id
            $paymentsGrouped = collect($order['purchase_invoice']['invoice_payments'])
                                   ->groupBy('payment_method_id');

            foreach ($paymentsGrouped as $methodId => $payments) {
                // Pastikan methodId ada di daftar $this->PAYMENT_METHODS
                if (! array_key_exists($methodId, $this->PAYMENT_METHODS)) {
                    // Jika metode tidak terdaftar (misal “TEMPO”), skip.
                    continue;
                }

                // Jumlahkan nominal_payment untuk satu metode
                $sumNominal = $payments->sum('nominal_payment');

                // Simpan ke array paids (key = methodId, value = sumNominal)
                $order['purchase_invoice']['paids'][$methodId] = $sumNominal;

                // Tambahkan ke ringkasan global
                $methods[$methodId] += $sumNominal;
            }

            // Akumulasi total PO, discount, invoice, paid
            $totalNominalPo       += ($order['purchase_invoice']['nominal'] ?? 0);
            $totalNominalDiscount += ($order['purchase_invoice']['nominal_discount'] ?? 0);
            $totalInvoice         += ($order['purchase_invoice']['total_invoice'] ?? 0);
            $totalPaid            += ($order['purchase_invoice']['nominal_paid'] ?? 0);

            // Filter detail PO
            $details = collect($order['purchase_detail_po']);
            if ($details->where('status', 'Material Receipt')->isNotEmpty()) {
                $order['purchase_detail_po'] = $details
                    ->where('status', 'Material Receipt')
                    ->values()
                    ->all();
            } else {
                $order['purchase_detail_po'] = $details
                    ->where('status', 'Purchase Order')
                    ->values()
                    ->all();
            }
        }

        // 4) Supaya di Blade tampil dalam urutan yang konsisten, 
        //    kembalikan $methods sebagai array [nama => total]
        //    bukan lagi [id => total], sehingga header bisa pakai nama.
        $methodsByName = [];
        foreach ($methods as $methodId => $totalPaidPerMethod) {
            $methodsByName[ $this->PAYMENT_METHODS[$methodId] ] = $totalPaidPerMethod;
        }

        // 5) Kirim ke view
        return view('pages.report.purchase_order.print', [
            'orders'             => $orders,
            'supplier_id'        => $supplier_id,
            'methods'            => $methodsByName,   // [‘CASH’ => 1000000, ‘TRANSFER’ => 500000, …]
            'summary_detail'     => $summary_detail,
            'totalNominalPo'     => $totalNominalPo,
            'totalNominalDiscount' => $totalNominalDiscount,
            'totalInvoice'       => $totalInvoice,
            'totalPaid'          => $totalPaid,
            'PAYMENT_METHODS'    => $this->PAYMENT_METHODS
        ]);
    }
}
