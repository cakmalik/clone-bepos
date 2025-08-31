<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\UserOutlet;
use App\Models\JournalType;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\JournalNumber;
use App\Models\PaymentMethod;
use App\Models\ReceiptDetail;
use App\Models\InvoicePayment;
use App\Models\PurchaseDetail;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;
use App\Models\JournalTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Log;
use App\Models\JournalCategoryProduct;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReceptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $reception = Purchase::query()
                 ->where('purchase_type', 'Reception')
                //  ->whereIn('inventory_id', getUserInventory())
                 ->with([
                     'inventory',
                     'user',
                     'purchaseInvoice'
                 ])
                 ->where(function ($query) use ($request) {
                    if ($request->status === 'Open') {
                        // BELUM SELESAI: belum diterima ATAU belum lunas ATAU belum punya faktur
                        $query->where(function ($subQuery) {
                            $subQuery->where('purchase_status', '!=', 'Finish')
                                ->orWhereHas('purchaseInvoice', function ($q) {
                                    $q->where('is_done', '!=', 1); // Belum lunas
                                })
                                ->orWhereDoesntHave('purchaseInvoice'); // Belum punya faktur
                        });
                    } elseif ($request->status === 'Finish') {
                        // SELESAI: sudah diterima DAN lunas
                        $query->where('purchase_status', 'Finish')
                            ->whereHas('purchaseInvoice', function ($q) {
                                $q->where('is_done', 1);
                            });
                    }
     
                     // Filter berdasarkan tanggal
                     if (!empty($request->start_date) && !empty($request->end_date)) {
                         $query->whereBetween('purchase_date', [
                             startOfDay($request->start_date),
                             endOfDay($request->end_date)
                         ]);
                     }
                 })
                 ->orderBy('updated_at', 'DESC')
                 ->get()
                 ->map(function ($purchase) {
                     $purchase->purchase_date = Carbon::parse($purchase->purchase_date)->format('d F Y H:i');
                     return $purchase;
                 });
     
             return DataTables::of($reception)
                 ->addIndexColumn()
                 ->addColumn('code', function ($row) {
                     return $row->code;
                 })
                 ->addColumn('status', function ($row) {
                     if ($row->purchase_status == 'Draft') {
                         return '<span class="badge badge-sm bg-warning-lt">DISIMPAN</span>';
                     } elseif ($row->purchase_status == 'Open') {
                         return '<span class="badge badge-sm bg-orange-lt">BELUM DITERIMA</span>';
                     } elseif ($row->purchase_status == 'Finish') {
                         return '<span class="badge badge-sm bg-green-lt">DITERIMA</span>';
                     } else {
                         return '<span class="badge badge-sm bg-info-lt">BATAL</span>';
                     }
                 })
                 ->addColumn('payment_status', function ($row) {
                    if (!$row->purchaseInvoice) {
                        return '<span class="badge badge-sm bg-red-lt">BELUM ADA FAKTUR</span>';
                    } elseif ($row->purchaseInvoice->is_done == 1) {
                        return '<span class="badge badge-sm bg-green-lt">LUNAS</span>';
                    } else {
                        return '<span class="badge badge-sm bg-orange-lt">BELUM LUNAS</span>';
                    }                    
                 })
                 ->addColumn('action', function ($row) {
                    if (
                        $row->purchase_status == 'Finish' &&
                        (optional($row->purchaseInvoice)->is_done == 1 || $row->is_invoiced == 0)
                    ) {
                        return '<a href="purchase_reception_print/' . $row->id . '" class="btn btn-outline-primary" data-original-title="Show"><i class="fa fa-print"></i></a>';
                    } elseif ($row->purchase_status == 'Draft') {
                        return '<a href="purchase_reception/' . $row->id . '/edit" class="btn btn-white py-2 px-3"><li class="fas fa-edit"></li></a>';
                    } elseif ($row->purchase_status == 'Open' || optional($row->purchaseInvoice)->is_done == 0) {
                        return '<a href="purchase_reception/' . $row->id . '" class="btn btn-outline-dark py-2 px-3"><li class="fas fa-edit"></li></a>';
                    } else {
                        return '';
                    }
                    
                 })
                 ->rawColumns(['status', 'code', 'action', 'payment_status'])
                 ->make(true);
         }
     
         return view('pages.purchase.purchase_reception.index');
     }
     

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $query = DB::table('purchases')->where('purchase_type', 'Reception')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int) $c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }

        $purchase_po = Purchase::whereNull('ref_code')
            ->where('purchase_type', 'Purchase Order')
            ->where('purchase_status', 'Finish')
            ->whereHas('purchase_detail_po', function ($query) {
                $query->where('status', 'Purchase Order');
            })
            ->get();

        $po = PurchaseDetail::where('status', 'Purchase Order')->with('product')->get();
        
        $supplier = Supplier::all();
        $products = Product::query()
            ->select(
                'products.barcode',
                'products.code',
                'products.id',
                'products.name',
                'products.type_product',
                'products.capital_price',
                'brands.name as brand_name',
                'category.name as product_category',
                'suppliers.name as supplier_name',
                'product_units.name as unit_name',
            )
            ->leftJoin('brands', 'brand_id', 'brands.id')
            ->leftJoin('product_units', 'product_unit_id', 'product_units.id')
            ->leftJoin('product_categories as category', 'product_category_id', 'category.id')
            ->leftJoin('product_suppliers', 'product_suppliers.product_id', 'products.id')
            ->leftJoin('suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
            ->where(function ($query) use ($request) {
                if ($request->product != '') {
                    $query->where('products.name', 'LIKE', '%' . $request->product . '%');
                    $query->orWhere('products.code', 'LIKE', '%' . $request->product . '%');
                    $query->orWhere('products.barcode', 'LIKE', '%' . $request->product . '%');
                }
            })
            ->where('products.is_bundle', 0)
            ->where('products.deleted_at', null)
            ->limit(1000)
            ->orderBy('products.name')
            ->get();

            $inventories = Inventory::where('is_active', 1)->get();
            $outlets = Outlet::all();
            $recipients = $inventories->concat($outlets);


        return view('pages.purchase.purchase_reception.create', [
            'inventory' => Inventory::find(myInventoryId()),
            'code' => $cd,
            'purchase_po' => $purchase_po,
            'po' => $po,
            'supplier' => $supplier,
            'products' => $products,
            'recipients' => $recipients
        ]);
    }

    public function getPO(Request $request)
    {
        $po = PurchaseDetail::select(
            'purchase_details.*',
            'products.barcode',
            DB::raw('(SELECT price FROM product_prices WHERE product_prices.product_id = products.id AND product_prices.type = "utama" LIMIT 1) as product_price')
        )
            ->where('purchase_details.purchase_po_id', $request->po_id)
            ->where('purchase_details.status', 'Purchase Order')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->get();
        return response()->json([
            'response' => $po
        ]);
    }

    public function editMultiplePrices(Request $request)
    {
        try {
            $productList = $request->input('productList');

            // Perform price updates or other logic
            foreach ($productList as $product) {
                // Adjust as needed, for example:
                $productId = $product['productId'];
                $newPrice = $product['newPrice'];
                $newSubtotal = $product['newSubtotal'];
                $newPriceProduct = $product['newPriceProduct'];

                // Perform price updates or other logic here
                PurchaseDetail::where('id', $productId)->update([
                    'price' => $newPrice,
                    'subtotal' => $newSubtotal,
                ]);

                // Retrieve the product ID associated with the purchase
                $purchaseDetail = PurchaseDetail::find($productId);
                $productId_e = $purchaseDetail->product_id;
                
                Product::where('id', $productId_e)->update(['capital_price' => $newPrice]);
                ProductPrice::where('product_id', $productId_e)->where('type', 'utama')->update(['price' => $newPriceProduct]);
            }

            return response()->json(['message' => 'Harga berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function editPrice(Request $request, $product_id)
    {
        DB::beginTransaction();

        try {
            $product = Product::find($product_id);

            // Lakukan iterasi untuk setiap harga product
            foreach ($request->input('product_prices') as $product_price) {
                if (!$product_price['price']) {
                    continue;
                }

                $price = ProductPrice::where('product_id', $product_id)
                    ->where('selling_price_id', $product_price['selling_price_id'])->first() ?? new ProductPrice;
                $price->product_id = $product->id;
                $price->selling_price_id = $product_price['selling_price_id'];
                $price->price = $product_price['price'];
                $price->type = 'lain'; // Untuk harga product selain harga utama
                $price->save();
            }

            // Update harga purchase pada tabel purchase_details
            $purchaseDetails = PurchaseDetail::where('product_id', $product_id)->first();
            if ($purchaseDetails) {
                $purchaseDetails->price = $request->input('purchase_price');
                $purchaseDetails->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Harga product dan purchase berhasil diubah']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Harga product dan purchase gagal diubah']);
        }
    }


    public function editHarga()
    {
        $query = DB::table('purchases')->where('purchase_type', 'Reception')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int) $c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }



        $purchase_po = Purchase::where('ref_code', NULL)->where('purchase_type', 'Purchase Order')->where('purchase_status', 'Finish')->get();

        $po = PurchaseDetail::where('status', 'Purchase Order')->with('product')->get();


        return view('pages.purchase.purchase_reception.create', [
            'inventory' => Inventory::find(myInventoryId()),
            'code' => $cd,
            'purchase_po' => $purchase_po,
            'po' => $po
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

        $request->validate([
            'inventory_id' => 'nullable|exists:inventories,id',
            'outlet_id' => 'nullable|exists:outlets,id',
            'invoice_number' => 'required_if:is_invoiced,1',
        ], [
            'invoice_number.required_if' => 'No. Tagihan Supplier harus diisi.',
        ]);

        if (!$request->inventory_id && !$request->outlet_id) {
            return back()->with('error', 'Penerima harus dipilih!');
        }
        
        

        DB::beginTransaction();
        try {

            $invOrOutletCode = null;

            if ($request->inventory_id) {
                $invOrOutletCode = \App\Models\Inventory::find($request->inventory_id)?->code;
            } elseif ($request->outlet_id) {
                $invOrOutletCode = \App\Models\Outlet::find($request->outlet_id)?->code;
            }

            $PN = Purchase::create([
                'code' => purchaseReceptionCode($invOrOutletCode),
                'ref_code' => $request->po_code ?? null,
                'user_id' => $request->user_id,
                'inventory_id' => $request->inventory_id,
                'outlet_id' => $request->outlet_id,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'purchase_type' => 'Reception',
                'purchase_status' => 'Open',
                'is_invoiced' => $request->input('is_invoiced', 0),
            ]);
    
            if ($request->has('po_id') && $request->po_id !== null) {
                // Data berasal dari Purchase Order
                PurchaseDetail::where('purchase_po_id', $request->po_id)->update([
                    'purchase_receipt_id' => $PN->id,
                    'status' => 'Material Receipt',
                    'outlet_id' => $request->outlet_id,
                    'inventory_id' => $request->inventory_id
                ]);
    
                Purchase::where('id', $request->po_id)->update([
                    'ref_code' => $request->code,
                    'is_invoiced' => $request->input('is_invoiced', 0),
                    'inventory_id' => $request->inventory_id,
                    'outlet_id' => $request->outlet_id
                ]);
    
            } else {
               // pembelian sederhana
                foreach ($request->all() as $key => $value) {
                    // Deteksi produk utama dari pola "id_pn_qty"
                    if (preg_match('/(\d+)_pn_qty/', $key, $matches)) {
                        $productId = $matches[1];
                        $productName = $request->input("{$productId}_pn_name");
                        $productQty = $request->input("{$productId}_pn_qty");
                        $productPrice = $request->input("{$productId}_pn_capital_price");
                        $productSubtotal = $request->input("{$productId}_pn_subtotal") ?? 0;
        
                        if ($productQty > 0) {
                            PurchaseDetail::create([
                                'inventory_id' => $request->inventory_id,
                                'outlet_id' => $request->outlet_id,
                                'purchase_id' => $PN->id,
                                'product_id' => $productId,
                                'purchase_po_id' => $request->po_id,
                                'purchase_receipt_id' => $PN->id,
                                'code' => purchaseDetailCode($productId),
                                'product_name' => $productName,
                                'qty' => $productQty,
                                'price' => $productPrice,
                                'subtotal' => $productSubtotal,
                                'status' => 'Material Receipt',
                                'is_bonus' => 0 // Bukan bonus
                            ]);
                        }
                    }
                }

                // create PO
                $PO = Purchase::create([
                    'code' => purchaseOrderCode($invOrOutletCode),
                    'ref_code' => $PN->code,
                    'user_id' => $request->user_id,
                    'inventory_id' => $request->inventory_id,
                    'outlet_id' => $request->outlet_id,
                    'supplier_id' => $request->supplier_id,
                    'purchase_date' => $request->purchase_date,
                    'purchase_type' => 'Purchase Order',
                    'purchase_status' => 'Finish',
                    'is_invoiced' => $request->input('is_invoiced', 0),
                ]);

                // update ref_code di Purchase
                Purchase::where('id', $PN->id)->update([
                    'ref_code' => $PO->code
                ]);

                // ambil data purchase detail. update ke PO
                $PR = PurchaseDetail::where('purchase_receipt_id', $PN->id)->get();

                foreach ($PR as $key => $value) {
                    $PR[$key]->purchase_po_id = $PO->id;
                    $PR[$key]->save();
                }
               
            }
    
            // Proses semua item dalam request
            foreach ($request->all() as $key => $value) {
                // Deteksi produk bonus dari pola "id_bonus_qty"
                if (preg_match('/(\d+)_bonus_qty/', $key, $matches)) {
                    $productId = $matches[1];
                    $productName = $request->input("{$productId}_bonus_product_name");
                    $productQty = $request->input("{$productId}_bonus_qty");
                    $productPrice = $request->input("{$productId}_bonus_product_price");
                    $productSubtotal = $request->input("{$productId}_bonus_subtotal") ?? 0;
    
                    if ($productQty > 0) {
                        PurchaseDetail::create([
                            'inventory_id' => $request->inventory_id,
                            'outlet_id' => $request->outlet_id,
                            'purchase_id' => $PN->id,
                            'product_id' => $productId,
                            'purchase_po_id' => $request->po_id,
                            'purchase_receipt_id' => $PN->id,
                            'code' => purchaseDetailCode($productId) . '-BONUS',
                            'product_name' => $productName,
                            'qty' => $productQty,
                            'price' => $productPrice,
                            'subtotal' => $productSubtotal,
                            'status' => 'Material Receipt',
                            'is_bonus' => 1 // Bonus
                        ]);
                    }
                }
            }

            //ambil jumlah subtotal dari Purchase detail
            $subtotal = PurchaseDetail::where('purchase_receipt_id', $PN->id)->sum('subtotal');

            // hilangkan . diskon
            $discount = $request->nominal_discount;
            $discount = str_replace('.', '', $discount);

            $newPO = Purchase::where('id', $request->po_id)->first();

            if ($request->is_invoiced == 1) {
                $PI = PurchaseInvoice::create([
                    'code' => invoicePurchaseCode(),
                    'invoice_number' => $request->invoice_number,
                    'purchase_id' => $PO->id ?? $newPO->id,
                    'invoice_date' => $request->purchase_date,
                    'nominal' => $subtotal,
                    'nominal_discount' => $discount ?? 0,
                    'total_invoice' => $subtotal - $discount,
                    'nominal_returned' => $request->nominal_returned ?? 0,
                    'nominal_paid' => $request->nominal_paid ?? 0,
                    'is_done' => false,
                    'user_id' => auth()->user()->id,
                ]);

                // Update ke PN
                $PN->update([
                    'purchase_invoice_id' => $PI->id 
                ]);

                // Update ke PO jika ada
                if (isset($PO)) {
                    $PO->update([
                        'purchase_invoice_id' => $PI->id
                    ]);
                } elseif ($request->po_id) {
                    Purchase::where('id', $request->po_id)->update([
                        'purchase_invoice_id' => $PI->id
                    ]);
                }
            }

            DB::commit();
            return redirect('/purchase_reception/' . $PN->id)->with('success', 'Sukses di Simpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal disimpan! Error: ' . $e->getMessage());
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
        $query = DB::table('receipt_details')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int) $c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }

        $purchase = Purchase::with('purchaseInvoice')->where('id', $id)->first();

        $purchaseDetail = PurchaseDetail::where('purchase_receipt_id', $id)
            ->where('status', 'Material Receipt')
            ->where('is_bonus', 0)
            ->get();

        $purchaseBonus = PurchaseDetail::where('purchase_receipt_id', $id)
            ->where('status', 'Material Receipt')
            ->where('is_bonus', 1)
            ->get();

        $accepted_qty = PurchaseDetail::where('purchase_receipt_id', $id)
            ->where('status', 'Material Receipt')
            ->sum('accepted_qty');


        $paymentMethods = PaymentMethod::where('name', '!=', 'TEMPO')->get();
        $invoicePayment = InvoicePayment::where('purchase_invoice_id', $purchase->purchase_invoice_id)->get();

        $totalInvoice = $purchase->purchaseInvoice->total_invoice ?? 0;
        $totalPaid = $purchase->purchaseInvoice->nominal_paid ?? 0;
        $underPayment = $totalInvoice - $totalPaid;

        return view('pages.purchase.purchase_reception.show', [
            'purchase' => $purchase,
            'purchaseDetail' => $purchaseDetail,
            'code' => $cd,
            'accepted_qty' => $accepted_qty,
            'purchaseBonus' => $purchaseBonus,
            'paymentMethods' => $paymentMethods,
            'invoicePayment' => $invoicePayment,
            'totalInvoice' => $totalInvoice,
            'totalPaid' => $totalPaid,
            'underPayment' => $underPayment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data_void = PurchaseDetail::where('purchase_receipt_id', $id)->count();
        $void_true = true;

        if ($data_void > 0) {
            $void_true = false;
        }

        $purchase = Purchase::where('id', $id)->with('inventory', 'supplier')->first();
        $purchase_PN = PurchaseDetail::where('purchase_receipt_id', $id)->where('status', 'Material Receipt')->with('product')->get();
        $purchase_PN_items = PurchaseDetail::where('purchase_receipt_id', $id)->where('status', 'Material Receipt')->with('product')->first();
        $po_PN = Purchase::where('id', $purchase_PN_items->purchase_po_id)->first();
        $purchase_po = Purchase::where('ref_code', NULL)->where('purchase_type', 'Purchase Order')->where('purchase_status', 'Finish')->get();

        return view('pages.purchase.purchase_reception.edit', ['purchase' => $purchase, 'purchase_PN' => $purchase_PN, 'purchase_po' => $purchase_po, 'po_PN' => $po_PN, 'id' => $id, 'void' => $void_true]);
    }

    public function Open(Request $request)
    {

        DB::beginTransaction();
        try {
            Purchase::where('id', $request->id)->update([
                'purchase_status' => 'Open'
            ]);

            DB::commit();
            return redirect('/purchase_reception/' . $request->id)->with('success', 'Sukses Penerimaan Open!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect('/purchase_reception')->with('error', 'Gagal Penerimaan Open!');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            if ($request->po_id_change) {
                $purchase = Purchase::where('id', $request->id)->first();
                Purchase::where('ref_code', $purchase->code)->update([
                    'ref_code' => NULL
                ]);

                Purchase::where('id', $request->id)->update([
                    'supplier_id' => $request->supplier_id_change,
                    'ref_code' => $request->po_code_change,
                ]);

                Purchase::where('id', $request->po_id_change)->update([
                    'ref_code' => $purchase->code
                ]);

                PurchaseDetail::where('purchase_receipt_id', $request->id)->update([
                    'purchase_receipt_id' => NULL,
                    'status' => 'Purchase Order'
                ]);

                PurchaseDetail::where('purchase_po_id', $request->po_id_change)->update([
                    'purchase_receipt_id' => $request->id,
                    'status' => 'Material Receipt'
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Sukses di Update!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal di Update!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            Purchase::destroy($id);
            PurchaseDetail::where('status', 'Reception')->where('purchase_receipt_id', $id)->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di batalkan !'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data gagal di batalkan !'
            ]);
        }
    }

    // public function reception(Request $request)
    // {


    //     DB::beginTransaction();

    //     $request->validate([
    //         'code' => 'required',
    //         'shipment_ref_code' => 'required|max:255',
    //     ], [], [
    //         'code' => 'Kode Terima Barang',
    //         'shipment_ref_code' => 'No.Referensi'
    //     ]);

    //     $purchaseDetail = PurchaseDetail::whereIn('id', $request->id_detail)->get();

    //     foreach ($purchaseDetail as $pds) {
    //         $product2 = Product::find($pds->product_id);

    //         // $journalCategoryProduct2 = JournalCategoryProduct::query()
    //         //     ->join('product_categories as pc', 'journal_category_products.product_category_id', 'pc.id')
    //         //     ->select(
    //         //         'journal_category_products.*',
    //         //         'pc.name as product_category'
    //         //     )
    //         //     ->where('product_category_id', $product2->product_category_id)
    //         //     ->with('journal_settings_buy.debit_account', 'journal_settings_buy.credit_account')
    //         //     ->first();
    //     }

    //     // if (!$journalCategoryProduct2) {
    //     //     return redirect()->back()->with('error', 'Lengkapi Set Akun Barang! ');
    //     // }



    //     $received_date = Carbon::now();
    //     $PN = Purchase::findorFail($request->id_pn);
    //     try {
    //         $completed = [];


    //         // $journalType = JournalType::where('name', 'PEMBELIAN')->first();


    //         $historyDescription = 'Penerimaan Barang No : ' . $PN->code .
    //             ' Nomor PO : ' . $PN->ref_code . ' ' . $request->shipment_ref_code;

    //         // $journalNumber = JournalNumber::create([
    //         //     'code' => $PN->code,
    //         //     'journal_type_id' => $journalType->id,
    //         //     'date' => Carbon::now(),
    //         //     'inventory_id' =>  $PN->inventory_id,
    //         //     'user_id' => Auth()->user()->id,
    //         //     'is_done' => true,
    //         //     'description' => $historyDescription
    //         // ]);



    //         foreach ($purchaseDetail as $pd) {
    //             $purchase = Purchase::where('id', $pd->purchase_id)->with('inventory')->first();
    //             $product_stock = ProductStock::query()
    //                 ->where('product_id', $pd->product_id)
    //                 ->where('inventory_id', $purchase->inventory_id)->first();

    //             $accepted_qty = $pd->id . '_new_qty';

    //             if ($request->$accepted_qty > $pd->qty) {
    //                 return redirect()->back()->withWarning('Qty di Terima Melebihi Qty');
    //             }


    //             PurchaseDetail::where('id', $pd->id)->update([
    //                 'accepted_qty' => $pd->accepted_qty + $request->$accepted_qty
    //             ]);
    //             $pd->subtotal = ($pd->accepted_qty + $request->$accepted_qty) * $pd->price;
    //             $pd->save();

    //             ReceiptDetail::create([
    //                 'code' => $request->code,
    //                 'purchase_detail_id' => $pd->id,
    //                 'received_date' => $received_date,
    //                 'shipment_ref_code' => $request->shipment_ref_code,
    //                 'accepted_qty' => $request->$accepted_qty
    //             ]);

    //             if ($product_stock) {
    //                 ProductStock::where('id', $product_stock->id)->update([
    //                     'stock_current' => $product_stock->stock_current + $request->$accepted_qty,
    //                 ]);

    //                 $ps_after = ProductStock::where('id', $product_stock->id)->first();

    //                 if ($product_stock->stock_current < $ps_after->stock_current) {
    //                     $action_type = 'PLUS';
    //                     $description = 'PENAMBAHAN';
    //                 } else {
    //                     $action_type = 'MINUS';
    //                     $description = 'PENGURANGAN';
    //                 }

    //                 ProductStockHistory::create([
    //                     'document_number' => $request->code,
    //                     'history_date' => Carbon::now(),
    //                     'action_type' => $action_type,
    //                     'product_id' => $product_stock->product_id,
    //                     'inventory_id' => $purchase->inventory_id,
    //                     'stock_change' => $request->$accepted_qty,
    //                     'stock_before' => $product_stock->stock_current,
    //                     'stock_after' => $ps_after->stock_current,
    //                     'desc' => 'Penerimaan : ' . $request->code . ' Melakukan ' . $description . ' Qty/Bonus',
    //                     'user_id' => getUserIdLogin(),
    //                 ]);
    //             } else {
    //                 ProductStock::create([
    //                     'product_id' => $pd->product_id,
    //                     'inventory_id' => $purchase->inventory_id,
    //                     'stock_current' => $request->$accepted_qty,
    //                 ]);


    //                 ProductStockHistory::create([
    //                     'document_number' => $request->code,
    //                     'history_date' => Carbon::now(),
    //                     'action_type' => 'PLUS',
    //                     'product_id' => $pd->product_id,
    //                     'inventory_id' => $purchase->inventory_id,
    //                     'stock_change' => $request->$accepted_qty,
    //                     'stock_before' => 0,
    //                     'stock_after' => $request->$accepted_qty,
    //                     'desc' => 'Penerimaan : ' . $request->code . ' Melakukan PENAMBAHAN Produk',
    //                     'user_id' => getUserIdLogin()
    //                 ]);
    //             }


    //             if ($pd->accepted_qty + $request->$accepted_qty == $pd->qty) {
    //                 $completed[] = true;
    //             } else {
    //                 $completed[] = false;
    //             }
    //             $nowCode = Carbon::now()->format('YmdHis');

    //             $product = Product::find($pd->product_id);


    //             // $journalCategoryProduct = JournalCategoryProduct::query()
    //             //     ->join('product_categories as pc', 'journal_category_products.product_category_id', 'pc.id')
    //             //     ->select(
    //             //         'journal_category_products.*',
    //             //         'pc.name as product_category'
    //             //     )
    //             //     ->where('product_category_id', $product->product_category_id)
    //             //     ->with('journal_settings_buy.debit_account', 'journal_settings_buy.credit_account')
    //             //     ->first();


    //             // $nominal  = $request->$accepted_qty * $product->capital_price;

    //             // $journalDescription = 'Penerimaan Barang PO: ' .  $PN->ref_code . '-Ref:' . $request->shipment_ref_code;

    //             // JournalTransaction::create([
    //             //     'code' => $nowCode . '-DEBIT',
    //             //     'journal_number_id' => $journalNumber->id,
    //             //     'type' => 'debit',
    //             //     'journal_account_id' => $journalCategoryProduct->journal_settings_buy->debit_account->id,
    //             //     'nominal' => $nominal,
    //             //     'description' => $journalDescription
    //             // ]);

    //             // JournalTransaction::create([
    //             //     'code' => $nowCode . '-KREDIT',
    //             //     'journal_number_id' => $journalNumber->id,
    //             //     'type' => 'credit',
    //             //     'journal_account_id' => $journalCategoryProduct->journal_settings_buy->credit_account->id,
    //             //     'nominal' => $nominal,
    //             //     'description' => $journalDescription
    //             // ]);
    //         }

    //         if (!in_array(false, $completed)) {
    //             Purchase::where('id', $request->id_pn)->update([
    //                 'purchase_status' => 'Finish'
    //             ]);

    //             DB::commit();
    //             return redirect('/purchase_reception_print/' . $request->id_pn)->with('success', 'Sukses di Simpan!');
    //         }

    //         DB::commit();
    //         return redirect()->back()->with('success', 'Sukses di Simpan!');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Gagal di Simpan!');
    //     }
    // }

    public function reception(Request $request)
    {
        // dd($request->all());

        DB::beginTransaction();

        $request->validate([
            'code' => 'required',
            'shipment_ref_code' => 'required|max:255',
        ], [], [
            'code' => 'Kode Terima Barang',
            'shipment_ref_code' => 'No.Referensi'
        ]);

        $purchaseDetail = PurchaseDetail::whereIn('id', $request->id_detail)->get();

        $received_date = Carbon::now();
        $PN = Purchase::findOrFail($request->id_pn);
        try {
            $completed = [];

            //update ref_code dengan po_code
            Purchase::where('id', $request->po_id)->update([
                'ref_code' => $request->po_code
            ]);

            foreach ($purchaseDetail as $pd) {
                if ($pd->is_bonus == 0) {
                    $this->processNonBonusProduct($pd, $request, $received_date, $PN);
                }
            }

            foreach ($purchaseDetail as $pd) {
                if ($pd->is_bonus == 1) {
                    $this->processBonusProduct($pd, $request, $received_date, $PN);
                }
            }

            if (!in_array(false, $completed)) {
                Purchase::where('id', $request->id_pn)->update([
                    'purchase_status' => 'Finish'
                ]);
            }

            DB::commit();
            return redirect('/purchase_reception_print/' . $request->id_pn)->with('success', 'Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal di Simpan!');
        }
    }


    private function processNonBonusProduct($pd, $request, $received_date, $PN)
    {
        $purchase = Purchase::where('id', $pd->purchase_receipt_id)->with('inventory', 'outlet')->first();
        
        $product_stock = ProductStock::query()
            ->where('product_id', $pd->product_id)
            ->when($purchase->inventory_id, function ($query, $invId) {
                $query->where('inventory_id', $invId);
            })
            ->when($purchase->outlet_id, function ($query, $outletId) {
                $query->where('outlet_id', $outletId);
            })
            ->first();

        $accepted_qty = $pd->id . '_new_qty';

        if ($request->$accepted_qty > $pd->qty) {
            return redirect()->back()->withWarning('Qty di Terima Melebihi Qty');
        }

        PurchaseDetail::where('id', $pd->id)->update([
            'accepted_qty' => $pd->accepted_qty + $request->$accepted_qty
        ]);
        $pd->subtotal = ($pd->accepted_qty + $request->$accepted_qty) * $pd->price;
        $pd->save();

        ReceiptDetail::create([
            'code' => $request->code,
            'purchase_detail_id' => $pd->id,
            'received_date' => $received_date,
            'shipment_ref_code' => $request->shipment_ref_code,
            'accepted_qty' => $request->$accepted_qty
        ]);

        $this->updateProductStock($product_stock, $purchase, $request->$accepted_qty, $pd->product_id, $pd->is_bonus, $request);


        if ($pd->accepted_qty + $request->$accepted_qty == $pd->qty) {
            $completed[] = true;
        } else {
            $completed[] = false;
        }
    }

    private function processBonusProduct($pd, $request, $received_date, $PN)
    {
        $purchase = Purchase::where('id', $pd->purchase_receipt_id)->with('inventory', 'outlet')->first();
        $product_stock = ProductStock::query()
            ->where('product_id', $pd->product_id)
            ->when($purchase->inventory_id, function ($query, $invId) {
                $query->where('inventory_id', $invId);
            })
            ->when($purchase->outlet_id, function ($query, $outletId) {
                $query->where('outlet_id', $outletId);
            })
            ->first();    

        $accepted_qty = $pd->id . '_new_qty';

        if ($request->$accepted_qty > $pd->qty) {
            return redirect()->back()->withWarning('Qty di Terima Melebihi Qty');
        }

        PurchaseDetail::where('id', $pd->id)->update([
            'accepted_qty' => $pd->accepted_qty + $request->$accepted_qty
        ]);

        $pd->subtotal = 0;
        $pd->save();

        ReceiptDetail::create([
            'code' => $request->code,
            'purchase_detail_id' => $pd->id,
            'received_date' => $received_date,
            'shipment_ref_code' => $request->shipment_ref_code,
            'accepted_qty' => $request->$accepted_qty
        ]);

        $this->updateProductStock($product_stock, $purchase, $request->$accepted_qty, $pd->product_id, $pd->is_bonus, $request);


        if ($pd->accepted_qty + $request->$accepted_qty == $pd->qty) {
            $completed[] = true;
        } else {
            $completed[] = false;
        }
    }

    private function updateProductStock($product_stock, $purchase, $accepted_qty, $product_id, $is_bonus, $request)
    {
        if ($product_stock) {
            ProductStock::where('id', $product_stock->id)->update([
                'stock_current' => $product_stock->stock_current + $accepted_qty,
            ]);

            $ps_after = ProductStock::where('id', $product_stock->id)->first();

            if ($product_stock->stock_current < $ps_after->stock_current) {
                $action_type = 'PLUS';
                $description = $is_bonus ? 'PENAMBAHAN (Bonus)' : 'PENAMBAHAN';
            } else {
                $action_type = 'MINUS';
                $description = 'PENGURANGAN';
            }

            ProductStockHistory::create([
                'document_number' => $request->code,
                'history_date' => Carbon::now(),
                'action_type' => $action_type,
                'product_id' => $product_id,
                'inventory_id' => $purchase->inventory_id,
                'outlet_id' => $purchase->outlet_id,
                'stock_change' => $accepted_qty,
                'stock_before' => $product_stock->stock_current,
                'stock_after' => $ps_after->stock_current,
                'desc' => 'Penerimaan (PN): ' . $purchase->code . ' melakukan ' . $description . ' QTY',
                'user_id' => getUserIdLogin(),
            ]);
        } else {
            ProductStock::create([
                'product_id' => $product_id,
                'inventory_id' => $purchase->inventory_id,
                'outlet_id' => $purchase->outlet_id,
                'stock_current' => $accepted_qty,
            ]);

            ProductStockHistory::create([
                'document_number' => $request->code,
                'history_date' => Carbon::now(),
                'action_type' => 'PLUS',
                'product_id' => $product_id,
                'inventory_id' => $purchase->inventory_id,
                'outlet_id' => $purchase->outlet_id,
                'stock_change' => $accepted_qty,
                'stock_before' => 0,
                'stock_after' => $accepted_qty,
                'desc' => 'Penerimaan (PN): ' . $purchase->code . ' melakukan PENAMBAHAN QTY',
                'user_id' => getUserIdLogin()
            ]);
        }
    }

    public function finish(Request $request)
    {
        DB::beginTransaction();
        try {
            Purchase::where('id', $request->id)->update([
                'purchase_status' => 'Finish'
            ]);

            DB::commit();
            return redirect('/purchase_reception')->with('success', 'Sukses di Selesai!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal di Selesai!');
        }
    }

    public function print($id)
    {
        $purchase = Purchase::with('purchaseInvoice')->find($id);

        return view('pages.purchase.purchase_reception.print', [
            'title' => 'ID TRANSAKSI - ' . $purchase->code,
            'purchase' => $purchase,
            'purchaseInvoice' => $purchase->purchaseInvoice
        ]);
    }

    public function summary($code)
    {

        $purchase = Purchase::with(
            'purchase_detail_reception',
            'inventory',
            'user',
            'purchaseInvoice'
        )
            ->with([
                'purchase_detail_reception' => function ($query) {
                    $query->where('purchase_details.status', 'Material Receipt');
                }
            ])
            ->where('code', $code)->first();

        $sum = PurchaseDetail::where([
            ['purchase_receipt_id', $purchase->id],
            ['status', 'Material Receipt']
        ])->sum('subtotal');
        
        $purchaseInvoice = PurchaseInvoice::where('purchase_id', $purchase->id)->first();

        if($purchase->is_invoiced) {
            $total = $sum - $purchase->purchaseInvoice->nominal_discount;
        } else {
            $total = $sum;
        }

        $data = [
            'company' => profileCompany(),
            'purchase' => $purchase,
            'total' => $total,
            'purchaseInvoice' => $purchaseInvoice,
        ];

        return view('pages.purchase.purchase_reception.summary', $data);
    }

    public function detailNota($code)
    {

        $purchase = Purchase::with(
            'purchase_detail_reception',
            'inventory',
            'user'
        )->where('code', $code)->first();


        $receipt = DB::table('receipt_details as rd')
            ->leftJoin('purchase_details as pd', 'rd.purchase_detail_id', 'pd.id')
            ->leftJoin('purchases as p', 'pd.purchase_po_id', 'p.id')
            ->join('products as pro', 'pd.product_id', 'pro.id')
            ->leftJoin('suppliers as s', 'p.supplier_id', 's.id')
            ->select(
                'shipment_ref_code',
                'received_ref_code',
                'pd.product_name',
                'pro.code as product_code',
                'p.code as po_code',
                'rd.received_date',
                's.name as supplier',
                'rd.accepted_qty',
                'pd.purchase_receipt_id',
                'pd.is_bonus'
            )
            ->where([['pd.purchase_receipt_id', $purchase->id], ['rd.accepted_qty', '>', 0]])
            ->get();



        $data = [
            'company' => profileCompany(),
            'purchase' => $purchase,
            'receipt' => $receipt
        ];

        return view('pages.purchase.purchase_reception.detailNota', $data);
    }


    public function getDetailProductPN(Request $request)
    {
        $purchaseDetail = PurchaseDetail::where('status', 'Material Receipt')->where('purchase_receipt_id', $request->id)->with('product', 'purchase')->get();

        return response()->json($purchaseDetail);
    }

    public function getProductBonus(Request $request)
    {
        $product = Product::query()
            ->whereIn('id', $request->items)
            ->with(['productUnit', 'productPrice' => function ($query) {
                $query->where('type', 'utama');
            }])->get();

        return response()->json([
            'response' => $product
        ]);
    }

    public function getSupplier(Request $request)
    {
        try {
            $supplier = Supplier::findOrFail($request->id);

            return response()->json([
                'supplier' => $supplier,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProductReceiption(Request $request)
    {
        $product = Product::query()
            ->whereIn('id', $request->items)
            ->with(['productUnit', 'productPrice' => function ($query) {
                $query->where('type', 'utama');
            }])->get();

        return response()->json([
            'response' => $product
        ]);
    }

    public function payment(Request $request, $id)
    {
        $cleanedNominal = str_replace('.', '', $request->nominal_payment);
        $request->merge(['nominal_payment' => $cleanedNominal]);

        $purchase = Purchase::findOrFail($id);
        $invoice = $purchase->purchaseInvoice;

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice untuk pembelian ini tidak ditemukan.');
        }

        $underPayment = $invoice->total_invoice - $invoice->nominal_paid;

        $request->validate([
            'payment_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'required|string',
            'nominal_payment' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($underPayment) {
                    if ($value > $underPayment) {
                        $fail('Nominal tidak boleh melebihi kekurangan pembayaran.');
                    }
                }
            ],
        ], [
            'payment_date.required' => 'Tanggal wajib diisi.',
            'payment_method_id.required' => 'Pembayaran wajib dipilih.',
            'description.required' => 'Keterangan wajib diisi.',
            'nominal_payment.required' => 'Nominal wajib diisi.',
            'nominal_payment.numeric' => 'Nominal harus berupa angka.',
            'nominal_payment.min' => 'Nominal tidak boleh nol.',
        ]);

        DB::beginTransaction();

        try {
            $selectedDate = $request->payment_date;
            $currentTime = Carbon::now()->format('H:i:s');
            $paymentDateTime = Carbon::parse($selectedDate . ' ' . $currentTime);

            InvoicePayment::create([
                'code' => invoicePaymentCode(),
                'purchase_invoice_id' => $invoice->id,
                'payment_method_id' => $request->payment_method_id,
                'user_id' => auth()->id(),
                'nominal_payment' => $cleanedNominal,
                'payment_date' => $paymentDateTime,
                'description' => $request->description,
            ]);

            $invoice->nominal_paid += $cleanedNominal;
            if ($invoice->nominal_paid >= $invoice->total_invoice) {
                $invoice->is_done = 1;
            }
            $invoice->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error('Pembayaran Gagal: ' . $e->getMessage());
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pembayaran. Silakan coba lagi.');
        }
    }


}
