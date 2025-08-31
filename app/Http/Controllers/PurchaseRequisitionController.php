<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\UserOutlet;
use App\Models\PriceChange;
use App\Models\ProductUnit;
use App\Models\ProductPrice;
use App\Models\SellingPrice;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;


class PurchaseRequisitionController extends Controller
{
    public $typeProducts = [
        'product' => 'product',
        'material' => 'material',
    ];

    private function queryGetProduct($request)
    {
        return Product::query()
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
                'product_units.name as unit_name'
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
            ->where('products.deleted_at', null)
            ->where('products.is_bundle', 0)
            ->limit(1000)
            ->orderBy('products.name')
            ->get();
    }

    public function index(Request $request)
    {
        // Gunakan default hari ini jika tidak ada input tanggal
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Purchase::withTrashed()
            ->with('supplier', 'user')
            ->where('purchase_type', 'Purchase Requisition')
            ->whereBetween('purchase_date', [$start, $end]);

        // Filter status, default ke 'Draft' jika kosong
        $status = $request->input('status', null);
        if (!empty($status)) {
            $query->where('purchase_status', $status);
        }

        $dataPurchase = $query->orderBy('created_at', 'DESC')->get()
            ->map(function ($purchase) {
                $purchase->formatted_purchase_date = Carbon::parse($purchase->purchase_date)->format('d F Y H:i');
                return $purchase;
            });

        // Pastikan request object tetap membawa nilai default untuk status
        $request->merge(['status' => $status]);

        return view('pages.purchase.purchase_requisition.index', compact('dataPurchase', 'request'));
    }

    public function create(Request $request)
    {
        $query = DB::table('purchases')->where('purchase_type', 'Purchase Requisition')
            ->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }

        if ($request->ajax()) {
            $products = $this->queryGetProduct($request);

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                       <span data-id="pr_product_' . $row->id . '">
                        <a href="javascript:void(0)" id="items_product"
                            class="btn btn-outline-primary py-2 px-3" data-id="' . $row->id . '">
                            <li class="fas fa-add"></li>
                        </a>
                        </span>';
                })->rawColumns(['action'])->make(true);
        }
        $supplier = Supplier::all();
        return view('pages.purchase.purchase_requisition.create', [
            'code' => $cd,
            'supplier' => $supplier,
        ]);
    }

    public function getProduct_PR(Request $request)
    {
        $product = Product::query()
            ->whereIn('id', $request->items)
            ->with('productUnit')->get();

        return response()->json([
            'response' => $product
        ]);
    }


    public function store(Request $request)
    {

        // dd($request->all());
        DB::beginTransaction();

        $query = DB::table('purchase_details')->where('status', 'Purchase Requisition')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }

        try {
            $PR = Purchase::create([
                'code' => purchaseRequestionCode(),
                // 'inventory_id' => myInventoryId(), ini di hide untuk custom destinasi penerimaan barang
                'purchase_date' => $request->purchase_date,
                'user_id' => Auth()->user()->id,
                'purchase_type' => 'Purchase Requisition',
                'purchase_status' => $request->action === 'finish' ? 'Finish' : 'Draft',
            ]);


            $product = Product::whereIn('id', $request->items)->get();

            foreach ($product as $pd) {
                $code = purchaseDetailCode($pd->id);
                $product_id = $pd->id . '_id';
                $product_name = $pd->id . '_name';
                $product_qty = $pd->id . '_qty';
                $product_hpp = $pd->id . '_hpp';
                $product_subtotal = $pd->id . '_subtotal';

                PurchaseDetail::create([
                    // 'inventory_id' => $request->inventory_id,
                    'code'          => $code,
                    'purchase_id'   => $PR->id,
                    'product_id'    => $request->$product_id,
                    'product_name'  => $request->$product_name,
                    'qty'           => $request->$product_qty,
                    'price'         => $request->$product_hpp,
                    'subtotal'      => $request->$product_subtotal,
                    'status'        => 'Purchase Requisition',

                ]);
            }

            DB::commit();

            if ($request->action === 'draft') {
                return redirect('/purchase_requisition/' . $PR->id . '/edit')->with('success', 'Berhasil Simpan Sebagai Draft');
            } else {
                return redirect('/purchase_requisition_print/' . $PR->id . '/')->with('success', 'Pembelian Berhasil di Buat!');
            }
            
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/purchase_requisition/create')->with('error', 'Gagal di Simpan!');
        }
    }

    public function edit(Request $request, $id)
    {

        $purchase = Purchase::where('purchase_type', 'Purchase Requisition')->where('id', $id)->with('outlet', 'user')->first();
        $purchaseDetail = PurchaseDetail::query()
            ->whereHas('product')
            ->with('product')
            ->where('purchase_id', $id)
            ->where('status', 'Purchase Requisition')
            ->get();

        $query = DB::table('purchases')->where('purchase_type', 'Purchase Requisition')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd = "";

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int)$c->codes) + 1;
                $cd = sprintf("%04s", $tmp);
            }
        } else {
            $cd = "0001";
        }
        $query_pd = DB::table('purchase_details')->where('status', 'Purchase Requisition')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
        $cd_pd = "";

        if ($query_pd->count() > 0) {
            foreach ($query_pd->get() as $c_pd) {
                $tmp_pd = ((int)$c_pd->codes) + 1;
                $cd_pd = sprintf("%04s", $tmp_pd);
            }
        } else {
            $cd_pd = "0001";
        }

        $array = [];
        foreach ($purchaseDetail as $pdd) {
            $array[] = $pdd->product_id;
        }

        $year = date('y');

        $purchase_cpd = PurchaseDetail::where('status', 'Purchase Requisition')
            ->where('purchase_id', $id)->whereHas('product')->with('product')->first();


        if ($request->ajax()) {
            $products = $this->queryGetProduct($request);

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return ' <td id="pr_product_edit" data-id="pr_product_edit_' . $row->id . '">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm py-2 px-3"
                            data-id="' . $row->id . '" id="items_product_update">
                            <li class="fas fa-add"></li>
                        </a>
                    </td>';
                })->rawColumns(['action'])->make(true);
        }


        return view('pages.purchase.purchase_requisition.edit', [
            'purchase' => $purchase,
            'purchaseDetail' => $purchaseDetail,
            'purchase_cpd' => $purchase_cpd,
            'code' => $cd,
            'code_pd' => $cd_pd,
            'year' => $year
        ]);
    }

    public function getDetailProductPR(Request $request)
    {
        $purchaseDetail = PurchaseDetail::query()
            ->whereHas('product')
            ->with('product')
            ->where('status', 'Purchase Requisition')
            ->where('purchase_id', $request->id)
            ->get();

        return response()->json($purchaseDetail);
    }



    public function getProduct_update(Request $request)
    {
        $product = Product::whereIn('id', $request->items_other)->with('productUnit')->get();

        return response()->json([
            'response' => $product
        ]);
    }


    public function update(Request $request, Purchase $purchase)
    {
        $action = $request->input('action');
    
        DB::beginTransaction();
        try {
            // Jika action adalah 'update', lakukan update PurchaseDetail
            if ($action === 'update') {
                PurchaseDetail::where('purchase_id', $request->purchase_id)->forceDelete();
    
                if ($request->items_current and !$request->items_other) {
                    $product_current = Product::whereIn('id', $request->items_current)->get();
    
                    foreach ($product_current as $pd) {
                        $product_id = $pd->id . '_id';
                        $product_name = $pd->id . '_name';
                        $product_qty = $pd->id . '_qty';
                        $product_hpp = $pd->id . '_hpp';
                        $product_subtotal = $pd->id . '_subtotal';
                        PurchaseDetail::create([
                            // 'inventory_id' => $request->inventory_id,
                            'code' => $request->code,
                            'purchase_id' => $request->purchase_id,
                            'product_id' => $request->$product_id,
                            'product_name' => $request->$product_name,
                            'qty' => $request->$product_qty,
                            'price' => $request->$product_hpp,
                            'subtotal' => $request->$product_subtotal,
                            'status' => 'Purchase Requisition'
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
                            // 'inventory_id' => $request->inventory_id,
                            'code' => $request->code,
                            'purchase_id' => $request->purchase_id,
                            'product_id' => $request->$product_id,
                            'product_name' => $request->$product_name,
                            'qty' => $request->$product_qty,
                            'price' => $request->$product_hpp,
                            'subtotal' => $request->$product_subtotal,
                            'status' => 'Purchase Requisition'
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
                            // 'inventory_id' => $request->inventory_id,
                            'code' => $request->code,
                            'purchase_id' => $request->purchase_id,
                            'product_id' => $request->$product_id,
                            'product_name' => $request->$product_name,
                            'qty' => $request->$product_qty,
                            'price' => $request->$product_hpp,
                            'subtotal' => $request->$product_subtotal,
                            'status' => 'Purchase Requisition'
                        ]);
                    }
    
                    foreach ($product_other_update as $pd_other) {
                        $product_id = $pd_other->id . '_id';
                        $product_name = $pd_other->id . '_name';
                        $product_qty = $pd_other->id . '_qty';
                        $product_hpp = $pd_other->id . '_hpp';
                        $product_subtotal = $pd_other->id . '_subtotal';
                        PurchaseDetail::create([
                            // 'inventory_id' => $request->inventory_id,
                            'code' => $request->code,
                            'purchase_id' => $request->purchase_id,
                            'product_id' => $request->$product_id,
                            'product_name' => $request->$product_name,
                            'qty' => $request->$product_qty,
                            'price' => $request->$product_hpp,
                            'subtotal' => $request->$product_subtotal,
                            'status' => 'Purchase Requisition'
                        ]);
                    }
                }
            }
    
            // Jika action adalah 'finish', update status Purchase
            if ($action === 'finish') {
                Purchase::where('id', $request->purchase_id)->update([
                    'purchase_status' => 'Finish'
                ]);
            }
    
            // Commit transaksi
            DB::commit();
    
            // Redirect berdasarkan aksi yang dilakukan
            if ($action === 'update') {
                return redirect()->back()->with('success', 'Sukses diperbarui!');
            }
    
            if ($action === 'finish') {
                return redirect('/purchase_requisition_print/' . $request->purchase_id)->with('success', 'Berhasil diselesaikan!');
            }
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal diupdate! ' . $th->getMessage());
        }
    }
    

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            Purchase::where('id', $id)->update([
                'purchase_status' => 'Void'
            ]);

            PurchaseDetail::where('status', 'Purchase Requisition')
                ->where('purchase_id', $id)
                ->delete();

            Purchase::destroy($id);

            DB::commit();
            return redirect('/purchase_requisition')->with('success', 'Berhasil dibatalkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan!');
        }
    }

    public function print($id)
    {
        $data_void = PurchaseDetail::where('purchase_id', $id)
            ->where('purchase_po_id', '!=', NULL)
            ->count();
        $void_true = true;

        if ($data_void > 0) {
            $void_true = false;
        }

        $purchase = Purchase::where('id', $id)->first();

        return view('pages.purchase.purchase_requisition.print', [
            'title' => $purchase->code,
            'purchase' => $purchase,
            'void' => $void_true
        ]);
    }

    public function nota($code)
    {
        $purchase = Purchase::with([
            'purchaseDetailsNota' => function ($query) {
                $query->where('is_bonus', 0);
            },
            'user',
            'inventory',
            'supplier'
        ])->where('code', $code)->first();

        $sum = PurchaseDetail::where('purchase_id', $purchase->id)->sum('subtotal');

        $data = [
            'company' => profileCompany(),
            'purchase' => $purchase,
            'sum' => floatval($sum)
        ];

        return view('pages.purchase.purchase_requisition.nota', $data);
    }


    public function editPricePurchase($id)
    {
        $productsCategory = ProductCategory::where('parent_id', null)->where('outlet_id', getOutletActive()->id)->get();

        $productsUnit = ProductUnit::where('outlet_id', getOutletActive()->id)->get();

        $products = Product::with('productCategory', 'productUnit', 'brand')->where('id', $id)->first();
        $supplier = ProductSupplier::where('product_id', $products->id)->first();

        $productPrices = SellingPrice::leftJoin(DB::raw("(select selling_price_id, price, type from product_prices where product_id = $id) as pp"), 'pp.selling_price_id', '=', 'selling_prices.id')->get();

        $data_category = ProductCategory::find($products->product_category_id);
        if ($data_category) {
            $sub_categories = ProductCategory::where('parent_id', $data_category->parent_id)->get();
            if ($data_category->parent_id == null) {
                $sub_categories = ProductCategory::where('parent_id', $data_category->id)->get();
            }
        } else {
            $sub_categories = [];
        }
        $typeProducts = $this->typeProducts;
        $suppliers = Supplier::All();
        $brand = Brand::All();


        return view('pages.purchase.purchase_requisition.editprice', compact('products', 'productsCategory', 'productsUnit', 'productPrices', 'typeProducts', 'sub_categories', 'suppliers', 'supplier', 'brand'));
    }

    public function updatePricePurchase(Request $request, $product_id)
    {

        $validated = $request->validate([
            'name' => 'required',
            'product_category' => 'required',
            'product_unit' => 'required',
            'capital_price' => 'integer|nullable',
            'type_product' => 'required',
            'minimum_stock' => 'required',
            'image' => 'nullable|mimes:jpg,png,jpeg,gif,svg|max:4096',
            'main_selling_price_id' => 'numeric|exists:selling_prices,id',
            'product_prices' => 'array',
            'product_prices.*.selling_price_id' => 'required|exists:selling_prices,id',
            'product_prices.*.price' => 'nullable|numeric|min:0',
            'barcode' => [
                'nullable',
                // 'numeric',
                // 'digits:13',
                Rule::unique('products', 'barcode')->ignore($product_id),
            ],
            'supplier' => 'required'
        ]);
        if ($request->sub_category_id) {
            $validated['product_category'] = $request->sub_category_id;
        }

        $faker  = \Faker\Factory::create();
        //NOTE:jika request barcode kurang dari 13 digit, maka ku buatkan aja menggunakan faker
        //untuk menghandle barcode acak yang bs menyebabkan error
        // if (strlen($validated['barcode']) < 11) {
        //     $validated['barcode'] = $faker->ean13;
        // }

        // if (strlen($validated['barcode']) >= 14) {
        //     return redirect()->back()->withInput()->with('error', 'Barcode tidak boleh lebih dari 13 digit');
        // }
        $minus_price = Setting::where('name', 'minus_price')->first();
        foreach ($request->product_prices as $priceData) {
            if ($priceData['selling_price_id'] == $request->main_selling_price_id) {
                if (!$priceData['price'] or $priceData['price'] == 0 or $priceData['price'] == null or $priceData['price'] == '') {
                    return redirect()->back()->withInput()->with('error', 'Harga jual utama tidak boleh kosong');
                }

                if ($minus_price->value == true) {
                    if ($priceData['price'] < $request->capital_price) {
                        return redirect()->back()->withInput()->with('error', 'Harga Jual Harus Lebih Besar dari HPP!');
                    }
                }
            }
        }

        DB::beginTransaction();
        try {

            $product = Product::find($product_id);
            $capitalPrice = $product->capital_price;
            $product->barcode = $validated['barcode'];
            $product->name = $validated['name'];
            $product->product_category_id = $validated['product_category'];
            $product->product_unit_id = $validated['product_unit'];
            $product->capital_price = (int) str_replace('.', '', $validated['capital_price']);
            if ($product->type_product != $validated['type_product']) {
                if ($validated['type_product'] == 'material') {
                    $product->code = autoCode('products', 'code', 'M', 4);
                } elseif ($validated['type_product'] == 'product') {
                    $product->code = autoCode('products', 'code', 'P', 4);
                }
            }

            $product->brand_id = $request->brand;
            $product->type_product = $validated['type_product'];
            $product->minimum_stock = $validated['minimum_stock'];
            $product->outlet_id = getOutletActive()->id;
            $product->user_id = getUserIdLogin();
            if ($gambar = $request->file('image')) {
                // Hapus gambar lama jika ada
                if ($product->image) {
                    Storage::disk('public')->delete('images/' . $product->image);
                }

                $namaGambar = time() . '.' . $gambar->getClientOriginalExtension();
                Storage::disk('public')->put('images/' . $namaGambar, file_get_contents($gambar));
                $product->image = $namaGambar;
            }

            $product->save();

            $productSupplier = ProductSupplier::where('product_id', $product_id)->first();

            if ($productSupplier) {
                $productSupplier->supplier_id = $request->supplier;
                $productSupplier->save();
            } else {
                ProductSupplier::create([
                    'product_id'    => $product_id,
                    'supplier_id'   => $request->supplier
                ]);
            }

            $productSellingPriceOld = ProductPrice::where([
                ['product_id', $product->id],
                ['type', 'utama']
            ])->first();

            foreach ($validated['product_prices'] as $product_price) {
                if (!$product_price['price']) {
                    continue;
                }

                $price = ProductPrice::where('product_id', $product_id)
                    ->where('selling_price_id', $product_price['selling_price_id'])->first() ?? new ProductPrice;
                $price->product_id = $product->id;
                $price->selling_price_id = $product_price['selling_price_id'];
                $price->price = $product_price['price'];
                $price->type = $validated['main_selling_price_id'] == $product_price['selling_price_id'] ? 'utama' : 'lain';
                $price->save();

                if (
                    $productSellingPriceOld->selling_price_id != $validated['main_selling_price_id'] &&
                    $price->price != $productSellingPriceOld->price
                ) {

                    PriceChange::create([
                        'product_id'    => $product->id,
                        'user_id'       => Auth::id(),
                        'product_name'  => $product->name,
                        'date'          => Carbon::now(),
                        'hpp'           => $product->capital_price,
                        'selling_price' => $price->price,
                        'hpp_old'       => $capitalPrice,
                        'selling_price_old' => $productSellingPriceOld->price
                    ]);
                }
            }

            DB::commit();

            return redirect('/purchase_requisition/create')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Data gagal diubah');
        }
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
}
