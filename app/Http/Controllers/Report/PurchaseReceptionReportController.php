<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\ReceiptDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseReceptionReportController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();

        return view('pages.report.purchase_reception.index', compact('suppliers'));
    }

    public function print(Request $request)
    {
        try {
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $supplier_id = $request->query('supplier_id');
            $summary_detail = $request->query('summary_detail') == 'detail';

            if ($summary_detail) {
                $receipts = ReceiptDetail::select(
                    'receipt_details.received_date',
                    'receipt_details.code as reception_ref_code',
                    'receipt_details.shipment_ref_code',
                    'purchases.ref_code as purchase_order_code',
                    'suppliers.name as supplier_name',
                    'products.code as product_code',
                    'products.name as product_name',
                    'receipt_details.accepted_qty as accepted_qty',
                    'purchase_details.is_bonus as is_bonus'
                )
                    ->leftJoin('purchase_details', 'receipt_details.purchase_detail_id', 'purchase_details.id')
                    ->leftJoin('purchases', 'purchase_details.purchase_receipt_id', 'purchases.id')
                    ->leftJoin('suppliers', 'purchases.supplier_id', 'suppliers.id')
                    ->leftJoin('products', 'purchase_details.product_id', 'products.id')
                    ->where('purchases.purchase_type', 'Reception')
                    ->where('receipt_details.accepted_qty', '>', 0)
                    ->when($start_date, function ($query) use ($start_date) {
                        $query->whereDate('receipt_details.received_date', '>=', $start_date);
                    })
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->whereDate('receipt_details.received_date', '<=', $end_date);
                    })
                    ->when($supplier_id, function ($query) use ($supplier_id) {
                        $query->where('purchases.supplier_id', $supplier_id);
                    })
                    ->whereIn('purchases.inventory_id', getUserInventory())
                    ->get();

                $lastSame = 0;
                foreach ($receipts as $index => &$receipt) {
                    if ($index == 0 || $receipt->reception_ref_code != $receipts[$index - 1]->reception_ref_code) {
                        $receipts[$lastSame]->rowspan = $index - $lastSame;
                        $receipt->rowspan = count($receipts);
                        $lastSame = $index;
                    }
                }

                return view('pages.report.purchase_reception.print', compact('receipts', 'summary_detail', 'start_date', 'end_date'));
            } else {
                $suppliers = Supplier::with([
                    'purchases' => function ($query) {
                        $query->where('purchase_type', 'Reception');
                    },
                    'purchases.purchase_detail_reception' => function ($query) use ($start_date, $end_date) {
                        $query->when($start_date, function ($query) use ($start_date) {
                            $query->whereDate('created_at', '>=', $start_date);
                        })
                            ->when($end_date, function ($query) use ($end_date) {
                                $query->whereDate('created_at', '<=', $end_date);
                            });
                    },
                    'purchases.purchase_detail_reception.product',
                ])
                    ->when($supplier_id, function ($query) use ($supplier_id) {
                        $query->where('id', $supplier_id);
                    })
                    ->get()
                    ->toArray();

                $products_code = [];

                foreach ($suppliers as &$supplier) {
                    $supplier['products'] = [];
                    $supplier['bonus_qty'] = 0;
                    foreach ($supplier['purchases'] as $purchase) {
                        foreach ($purchase['purchase_detail_reception'] as $detail) {
                            $supplier['products'][] = [
                                'code' => $detail['product']['code'],
                                'qty' => $detail['accepted_qty'],
                                'is_bonus' => $detail['is_bonus'],
                            ];

                            if ($detail['is_bonus'] == 1) {
                                $supplier['bonus_qty'] += $detail['accepted_qty'];
                            }

                            $products_code[$detail['product']['code']] = $detail['product']['name'];
                        }
                    }
                    $supplier['products'] = collect($supplier['products'])->groupBy('code');
                }

                $suppliers = collect($suppliers)->filter(fn($supplier) => sizeof($supplier['products']));

                return view('pages.report.purchase_reception.print', compact('suppliers', 'products_code', 'summary_detail', 'start_date', 'end_date'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
