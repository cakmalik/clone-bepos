<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\PriceChange;
use App\Models\ProductUnit;
use Illuminate\Support\Str;
use App\Imports\ImportStock;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\SellingPrice;
use Illuminate\Http\Request;
use App\Models\ProductBundle;
use App\Models\ProfilCompany;
use App\Exports\ProductExport;
use App\Imports\ImportProduct;
use App\Imports\ImportPrice;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\CustomerCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ProductPriceTemplateExport;
use App\Exports\ProductStockTemplateExport;

class ProductController extends Controller
{
    public $typeProducts = [
        'product' => 'product',
        // 'material' => 'material',
    ];

    public $can_add_product = false;
    public function __construct()
    {
        $this->can_add_product = ProfilCompany::canAddProduct();
        $this->middleware(function ($request, $next) {
            if (! in_array('Product', getMenuPermissions())) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini');
            }
            return $next($request);
        });
    }
    private function queryGetProduct($request)
    {
        // Ambil semua product_id yang sudah ada di ProductBundle
        $excludedProductIds = ProductBundle::pluck('product_id')->toArray();

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
                    $query->where('products.name', 'LIKE', '%' . $request->product . '%')
                        ->orWhere('products.code', 'LIKE', '%' . $request->product . '%')
                        ->orWhere('products.barcode', 'LIKE', '%' . $request->product . '%');
                }
            })
            ->where('products.deleted_at', null)
            ->where('products.is_bundle', 0)
            ->whereNotIn('products.id', $excludedProductIds)
            ->limit(1000)
            ->orderBy('products.name')
            ->get();
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::query()
                ->select(
                    'products.barcode',
                    'products.code',
                    'products.id',
                    'products.name',
                    'products.type_product',
                    'products.capital_price as capital_price',
                    'products.deleted_at',
                    'brands.name as brand_name',
                    'category.name as product_category',
                    'category.is_parent_category',
                    'parent_category.name as parent_category_name',
                    'product_units.name as product_unit',
                    'product_prices.price as product_price'
                )
                ->leftJoin('product_categories as category', 'product_category_id', 'category.id')
                ->leftJoin('product_categories as parent_category', 'category.parent_id', 'parent_category.id')
                ->leftJoin('product_units', 'product_unit_id', 'product_units.id')
                ->leftJoin('product_prices', 'product_id', 'products.id')
                ->leftJoin('brands', 'brand_id', 'brands.id')
                ->leftJoin('product_suppliers as ps', 'products.id', 'ps.product_id')
                ->where([
                    ['product_prices.type', 'utama']
                ]);

            if ($request->is_bundle !== null) {
                $products->where('products.is_bundle', $request->is_bundle);
            }

            if ($request->category != '') {
                $products->where(function ($query) use ($request) {
                    $query->where('category.parent_id', $request->category)
                        ->orWhere('category.id', $request->category);
                });
            }

            if ($request->supplier != '') {
                $products->where('ps.supplier_id', $request->supplier);
            }

            if ($request->product != '') {
                $products->where(function ($query) use ($request) {
                    $query->where('products.name', 'LIKE', '%' . $request->product . '%')
                        ->orWhere('products.barcode', 'LIKE', '%' . $request->product . '%');
                });
            }

            $products = $products->orderBy('name')
                ->limit(5000)
                ->get()->map(function ($row) {
                    $row->product_price = decimalToRupiahView($row->product_price);
                    $row->capital_price_new = decimalToRupiahView($row->getRawOriginal('capital_price'));
                    $row->brand_name = $row->brand_name ?? '-';

                    if (!$row->is_parent_category) {
                        $row->product_category = $row->parent_category_name;
                    }

                    return $row;
                });

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view(
                        'pages.product.product_all.product_action',
                        ['row' => $row]
                    );
                })->rawColumns(['action'])->make(true);
        }

        $productCategory = ProductCategory::query()
            ->where('is_parent_category', true)
            ->orderBy('name')
            ->get();

        $supplier = Supplier::orderBy('name')->get();

        $inventories = DB::table('inventories')
            ->select('id', 'name')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get();

        $outlets = DB::table('outlets')
            ->select('id', 'name')
            ->whereNull('deleted_at')
            ->get();

        return view('pages.product.product_all.index', compact('productCategory', 'supplier', 'inventories', 'outlets'));
    }

    public function add()
    {
        $productsCategory = ProductCategory::where('parent_id', null)->get();
        $productsUnit     = ProductUnit::get();
        $sellingPrice     = SellingPrice::get();
        $typeProducts     = $this->typeProducts;
        $supplier         = Supplier::All();
        $brand            = Brand::All();
        $can_add_product = $this->can_add_product;
        return view('pages.product.product_all.create', compact('productsCategory', 'productsUnit', 'sellingPrice', 'typeProducts', 'supplier', 'brand', 'can_add_product'));
    }

    public function create(Request $request)
    {
        // dd($request->all());

        $validated = $request->validate([
            'name'                              => 'required',
            'is_bundle'                         => 'nullable',
            'product_category'                  => 'required',
            'product_unit'                      => 'required',
            'capital_price'                     => 'nullable',
            'type_product'                      => 'required',
            'minimum_stock'                     => 'required',
            'image'                             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:4096',
            'main_selling_price_id'             => 'nullable|numeric|exists:selling_prices,id',
            'product_prices'                    => 'array',
            'product_prices.*.selling_price_id' => 'required|exists:selling_prices,id',
            'product_prices.*.price'            => 'nullable|min:0',
            'main_selling_price_id'             => 'required',
            'barcode'                           => 'nullable|unique:products,barcode',
            'supplier'                          => 'nullable',
        ], [
            'main_selling_price_id.required' => 'Harga jual utama wajib ditentuakan!',
        ]);

        $faker = \Faker\Factory::create();
        //NOTE:jika request barcode kurang dari 13 digit, maka ku buatkan aja menggunakan faker
        //untuk menghandle barcode acak yang bs menyebabkan error
        // if (strlen($validated['barcode']) < 11) {
        //     $validated['barcode'] = $faker->ean13;
        // }

        // if (strlen($validated['barcode']) >= 14) {
        //     return redirect()->back()->withInput()->with('error', 'Barcode tidak boleh lebih dari 13 digit');
        // }

        if ($request->sub_category_id) {
            $validated['product_category'] = $request->sub_category_id;
        }
        $minus_price = Setting::where('name', 'minus_price')->first();



        foreach ($request->product_prices as $priceData) {
            if ($priceData['selling_price_id'] == $request->main_selling_price_id) {
                if ($priceData['price'] === null || $priceData['price'] == '') {
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
            $product                        = new Product;
            $product->barcode               = $validated['barcode'];
            $product->name                  = $validated['name'];
            $product->is_bundle             = $validated['is_bundle'];
            $product->product_category_id   = $validated['product_category'];
            $product->product_unit_id       = $validated['product_unit'];
            $product->capital_price         = (int) str_replace(['Rp.', '.', ' '], '', $validated['capital_price']);

            
            if ($validated['type_product'] == 'material') {
                $product->code = autoCode('products', 'code', 'M', 4);
            } else if ($validated['type_product'] == 'product') {
                $product->code = autoCode('products', 'code', 'P', 4);
            }
            $product->brand_id      = $request->brand;
            $product->type_product  = $validated['type_product'];
            $product->minimum_stock = $validated['minimum_stock'];
            $product->outlet_id     = getOutletActive()->id;
            $product->user_id       = getUserIdLogin();

            if ($gambar = $request->file('image')) {
                // $product->image = $request->file('image')->store('products');
                $namaGambar = time() . '.' . $gambar->getClientOriginalExtension();
                Storage::disk('public')->put('images/' . $namaGambar, file_get_contents($gambar));
                $product->image = $namaGambar;
            }

            $product->save();

            if ($request->supplier) {
                ProductSupplier::create([
                    'product_id'  => $product->id,
                    'supplier_id' => $request->supplier,
                ]);
            }

            $minimumMargin = $product->productCategory->minimum_margin;
            $typeMargin    = $product->productCategory->type_margin;
            foreach ($validated['product_prices'] as $product_price) {

                if (!isset($product_price['price']) || $product_price['price'] === '') {
                    continue;
                }

                $price = new ProductPrice;

                $price->product_id       = $product->id;
                $price->selling_price_id = $product_price['selling_price_id'];
                $price->price            = str_replace(['Rp.', '.'], '', $product_price['price']);
                $price->type             = $validated['main_selling_price_id'] == $product_price['selling_price_id'] ? 'utama' : 'lain';

                if ($product_price['price'] == 0) {
                    $price->save();
                    continue;
                }

                if ($typeMargin == 'PERCENT') {
                    $calculatedPrice = $validated['capital_price'] +
                        ($validated['capital_price'] * $minimumMargin / 100);
                } elseif ($typeMargin == 'NOMINAL') {
                    $calculatedPrice = $validated['capital_price'] + $minimumMargin;
                } else {
                    $calculatedPrice = $validated['capital_price'];
                }

                $calculatedPrice = str_replace(['Rp.', '.'], '', $calculatedPrice);

                if ($product_price['price'] < $calculatedPrice) {
                    $minExpectedPrice = number_format($calculatedPrice, 0, ',', '.');
                    return redirect()->back()->withInput()->with('error', 'Harga Jual harus lebih besar dari HPP dengan margin persen! (Minimal: Rp ' . $minExpectedPrice . ')');
                }

                $price->save();
            }
            DB::commit();

            //jika is bundle true, maka redirect ke edit budle product
            if ($product->is_bundle == true) {
                return redirect()->route('product.edit', $product->id)->with('success', 'Data berhasil ditambahkan, silahkan tambahkan produk paket');
            }

            return redirect()->route('product.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function destroy($id)
    {
        $productStock = ProductStock::where('product_id', $id)->first();

        if ($productStock) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal dihapus, Produk masih ada di stock!'
            ], 422);
        }

        try {

            $product = Product::findOrFail($id);
            $product->update(['barcode' => null]);

            if ($product->salesDetails->count() > 0 || $product->productStockHistories->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal dihapus, Produk sudah tercatat ditransaksi!'
                ], 422);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal dihapus, Terjadi kesalahan!'
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $productsCategory = ProductCategory::where('parent_id', null)->get();
        $productsUnit = ProductUnit::get();
        $products = Product::with('productCategory', 'productUnit', 'brand')->where('id', $id)->first();
        $supplier = ProductSupplier::where('product_id', $products->id)->first();

        $productPrices = SellingPrice::leftJoin('product_prices as pp', function ($join) use ($id) {
            $join->on('pp.selling_price_id', '=', 'selling_prices.id')
                ->where('pp.product_id', '=', $id)
                ->whereNull('pp.customer_category_id');
        })
        ->select([
            'selling_prices.id as selling_price_id',
            'selling_prices.name',
            'pp.price',
            'pp.type',
            'pp.product_id',
            'pp.customer_category_id',
        ])
        ->get()->map(function ($row) {
            $row->price = floatval($row->price ?? 0);
            return $row;
        });
        
        // dd($productPrices->toArray());

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
        $suppliers    = Supplier::All();
        $brand        = Brand::All();
        $customerPrices = ProductPrice::where('product_id', $products->id)->where('customer_category_id', '!=', null)->get();
        $customerCategories = CustomerCategory::all();
        $is_customer_price = Setting::where('name', 'customer_price')->where('value', '1')->first();

        if ($request->ajax()) {
            $products = $this->queryGetProduct($request);

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                       <span data-id="pr_product_' . $row->id . '">
                        <a href="javascript:void(0)" id="items_product"
                            class="btn btn-primary btn-sm py-2 px-3" data-id="' . $row->id . '">
                            <li class="fas fa-add"></li>
                        </a>
                        </span>';
                })->rawColumns(['action'])->make(true);
        }

        $allProducts = Product::with('productUnit')->where('is_bundle', 0)->get();
        $itemBundles = ProductBundle::where('product_bundle_id', $products->id)->get();

        return view('pages.product.product_all.edit', compact(
            'products',
            'productsCategory',
            'productsUnit',
            'productPrices',
            'typeProducts',
            'sub_categories',
            'suppliers',
            'supplier',
            'brand',
            'is_customer_price',
            'customerCategories',
            'customerPrices',
            'allProducts',
            'itemBundles'
        ));
    }

    public function update(Request $request, $product_id)
    {

        // dd($request->all());

        $request->merge([
            'product_prices' => array_filter($request->product_prices, function ($price) {
                return isset($price['selling_price_id']) && isset($price['price']);
            })
        ]);

        $validated = $request->validate([
            'name'                              => 'required',
            'is_bundle'                         => 'nullable',
            'product_category'                  => 'required',
            'product_unit'                      => 'required',
            'capital_price'                     => 'required',
            'type_product'                      => 'required',
            'minimum_stock'                     => 'required',
            'image'                             => 'nullable|mimes:jpg,png,jpeg,gif,svg|max:4096',
            'main_selling_price_id'             => 'numeric|exists:selling_prices,id',
            'product_prices'                    => 'array',
            'product_prices.*.selling_price_id' => 'required|exists:selling_prices,id',
            'product_prices.*.price'            => 'nullable|min:0',
            'barcode'                           => [
                'nullable',
                // 'numeric',
                // 'digits:13',
                Rule::unique('products', 'barcode')->ignore($product_id),
            ],
            'supplier'                          => 'nullable',
        ]);

        $validated['capital_price'] = (int) str_replace(['Rp.', '.', ' '], '', $validated['capital_price']);

        if ($request->sub_category_id) {
            $validated['product_category'] = $request->sub_category_id;
        }

        $faker = \Faker\Factory::create();
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
                if (! $priceData['price'] || $priceData['price'] == 0 || $priceData['price'] == null || $priceData['price'] == '') {
                    return redirect()->back()->withInput()->with('error', 'Harga jual utama tidak boleh kosong');
                }

                if ($minus_price->value && $priceData['price'] < $request->capital_price) {
                    return redirect()->back()->withInput()->with('error', 'Harga Jual Harus Lebih Besar dari HPP!');
                }
            }
        }

        DB::beginTransaction();
        try {

            $product = Product::find($product_id);
            // make sure cannot change satuan. when ada transaction atau purchase atau history atau sales atau stockopname
            // jadi tidak usah di simpan dlu unit id
            $capitalPrice                   = $product->capital_price;
            $product->barcode               = $validated['barcode'];
            $product->name                  = $validated['name'];

            //jika versi advance aja
            $product->is_bundle                 = $validated['is_bundle'] ?? 0;
            $product->product_category_id       = $validated['product_category'];
            $product->is_support_qty_decimal    = $request->is_support_qty_decimal == '1' ? true : false;
            $product->capital_price             = (int) str_replace(['Rp.', '.', ' '], '', $validated['capital_price']);

            if ($product->type_product != $validated['type_product']) {
                if ($validated['type_product'] == 'material') {
                    $product->code = autoCode('products', 'code', 'M', 4);
                } elseif ($validated['type_product'] == 'product') {
                    $product->code = autoCode('products', 'code', 'P', 4);
                }
            }

            $product->brand_id          = $request->brand;
            $product->type_product      = $validated['type_product'];
            $product->minimum_stock     = $validated['minimum_stock'];
            $product->product_unit_id   = $validated['product_unit'];
            $product->outlet_id         = getOutletActive()->id;
            $product->user_id           = getUserIdLogin();

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

            if ($request->supplier) {
                $productSupplier = ProductSupplier::where('product_id', $product_id)->first();

                if ($productSupplier) {
                    $productSupplier->supplier_id = $request->supplier;
                    $productSupplier->save();
                } else {
                    ProductSupplier::create([
                        'product_id'  => $product_id,
                        'supplier_id' => $request->supplier,
                    ]);
                }
            }

            $productSellingPriceOld = ProductPrice::where([
                ['product_id', $product->id],
                ['type', 'utama'],
            ])->first();

            $minimumMargin = $product->productCategory->minimum_margin;
            $typeMargin    = $product->productCategory->type_margin;

            foreach ($validated['product_prices'] as $product_price) {
                $sellingPriceId = $product_price['selling_price_id'];

                // $raw = 'Rp. 25.000';
                // $clean = str_replace(['Rp.', '.'], '', $raw);
                // $value = floatval($clean);

                // dd($value); // hasilnya: 25000

                $priceRaw = str_replace(['Rp.', '.', ' '], '', $product_price['price']);
                $priceValue = (int) $priceRaw;                

                $existingPrice = ProductPrice::where('product_id', $product_id)
                    ->where('selling_price_id', $sellingPriceId)
                    ->first();

                // Jika harga = 0 → hapus permanen kalau ada
                if ($priceValue <= 0) {
                    if ($existingPrice) {
                        $existingPrice->forceDelete();
                    }
                    continue;
                }

                // Validasi harga minimum
                if ($typeMargin == 'PERCENT') {
                    $calculatedPrice = $validated['capital_price'] + ($validated['capital_price'] * $minimumMargin / 100);
                } elseif ($typeMargin == 'NOMINAL') {
                    $calculatedPrice = $validated['capital_price'] + $minimumMargin;
                } else {
                    $calculatedPrice = $validated['capital_price'];
                }

                $calculatedPrice = str_replace(['Rp.', '.'], '', $calculatedPrice);

                if ($priceValue < $calculatedPrice) {
                    $minExpectedPrice = number_format($calculatedPrice, 2, ',', '.');
                    return redirect()->back()->withInput()->with('error', 'Harga Jual harus lebih besar dari HPP dengan margin! (Minimal: Rp ' . $minExpectedPrice . ')');
                }

                // Simpan atau update harga
                $price = $existingPrice ?? new ProductPrice;
                $price->product_id = $product->id;
                $price->selling_price_id = $sellingPriceId;
                $price->price = $priceValue;
                $price->type = $validated['main_selling_price_id'] == $sellingPriceId ? 'utama' : 'lain';
                $price->save();

                // Cek perubahan dan log
                if (
                    $productSellingPriceOld->selling_price_id != $validated['main_selling_price_id'] ||
                    $price->price != $productSellingPriceOld->price ||
                    $product->capital_price != $capitalPrice
                ) {
                    PriceChange::create([
                        'product_id'        => $product->id,
                        'user_id'           => Auth::id(),
                        'product_name'      => $product->name,
                        'date'              => Carbon::now(),
                        'hpp'               => $product->capital_price,
                        'selling_price'     => $price->price,
                        'hpp_old'           => $capitalPrice,
                        'selling_price_old' => $productSellingPriceOld->price,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('product.detail', $product_id)->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Data gagal diubah');
        }
    }

    public function updateCustomerPrice(Request $request, $product_id)
    {

        $validated = $request->validate([
            'product_prices' => 'required|array',
            'product_prices.*.customer_category_id' => 'required|exists:customer_categories,id',
            'product_prices.*.price' => 'nullable|numeric|min:0',
        ], [
            'product_prices.*.customer_category_id.required' => 'Kategori pelanggan wajib diisi.',
            'product_prices.*.customer_category_id.exists' => 'Kategori pelanggan tidak valid.',
            'product_prices.*.price.numeric' => 'Harga harus berupa angka.',
            'product_prices.*.price.min' => 'Harga tidak boleh kurang dari 0.',
        ]);

        $product = Product::findOrFail($product_id);

        $existingPrices = ProductPrice::where('product_id', $product_id)
            ->whereIn('customer_category_id', collect($validated['product_prices'])->pluck('customer_category_id'))
            ->get()
            ->keyBy('customer_category_id');

        foreach ($validated['product_prices'] as $priceData) {
            if ($priceData['price'] != null && $priceData['price'] != 0) {
                if (isset($existingPrices[$priceData['customer_category_id']])) {
                    $existingPrices[$priceData['customer_category_id']]->update(['price' => $priceData['price']]);
                } else {
                    ProductPrice::create([
                        'product_id' => $product_id,
                        'selling_price_id' => 1,
                        'customer_category_id' => $priceData['customer_category_id'],
                        'price' => $priceData['price'],
                        'type' => 'lain',
                    ]);
                }
            }
        }

        return redirect()->route('product.detail', ['id' => $product_id])->with('success', 'Harga pelanggan berhasil diperbarui.');
    }

    public function toggleMainStock(Request $request, $id)
    {
        $request->validate([
            'is_main_stock' => 'required|boolean'
        ]);

        $product = Product::findOrFail($id);
        $product->is_main_stock = $request->is_main_stock;
        $product->save();

        return response()->json(['message' => 'Status stok utama berhasil diperbarui.']);
    }


    public function storeOrUpdateItemBundle(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);

            ProductBundle::where('product_bundle_id', $id)->delete();

            if (empty($request->items) || !is_array($request->items)) {
                throw new \Exception("Harap tambahkan setidaknya satu produk dalam paket.");
            }

            foreach ($request->items as $productId) {
                $qty = $request->input("{$productId}_qty");

                if ($qty === null || $qty === '' || $qty <= 0) {
                    throw new \Exception("Qty untuk produk ID {$productId} tidak valid.");
                }

                ProductBundle::create([
                    'product_bundle_id' => $id,
                    'product_id' => $productId,
                    'qty' => $qty,
                ]);
            }

            DB::commit();
            return redirect()->route('product.detail', ['id' => $id])->with('success', 'Produk paket berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage()); // **Tampilkan error yang lebih jelas**
        }
    }


    public function detail($id)
    {
        $products     = Product::with('productCategory', 'productUnit')->where('id', $id)->first();

        $productSupplier = ProductSupplier::where('product_id', $products->id)->first();
        $supplierName = $productSupplier ? $productSupplier->supplier->name : null;

        $productPrice = ProductPrice::query()
            ->select(
                'selling_prices.name as price_name',
                'price',
                'type',
                'product_units.name as unit_name',
                'product_units.symbol as unit_symbol'
            )
            ->join('selling_prices', 'selling_prices.id', '=', 'product_prices.selling_price_id')
            ->join('products', 'products.id', '=', 'product_prices.product_id')
            ->join('product_units', 'product_units.id', '=', 'products.product_unit_id')
            ->where('product_id', $id)
            ->whereNull('customer_category_id')
            ->get();

        $is_customer_price = Setting::where('name', 'customer_price')->where('value', '1')->first();

        $customerPrices = ProductPrice::query()
            ->select(
                'customer_categories.name as category_name',
                'product_prices.price',
                'product_prices.type'
            )
            ->join('customer_categories', 'customer_categories.id', '=', 'product_prices.customer_category_id')
            ->where('product_id', $id)
            ->whereNotNull('customer_category_id')
            ->get();


        $productBundles = ProductBundle::where('product_bundle_id', $id)->get();

        return view('pages.product.product_all.detail', compact('products', 'productPrice', 'customerPrices', 'supplierName', 'is_customer_price', 'productBundles'));
    }

    public function printBarcode()
    {
        return view('pages.product.product_all.print_barcode');
    }

    public function cetakPriceTag(Request $request)
    {
        $selectedProducts = json_decode($request->selectedProducts, true);
        $products         = Product::with('productPrice', 'productPriceUtama')->whereIn('id', $selectedProducts)->get();
        return view('pages.product.product_all.price-tag-pdf', [
            'products' => $products,
        ]);
    }

    public function deleteProductBundle($bundle_id)
    {
        $productBundle = ProductBundle::where('id', $bundle_id)->first();

        if ($productBundle) {
            $productBundle->delete();

            return response()->json([
                'message' => 'Produk bundel berhasil dihapus.'
            ]);
        } else {
            return response()->json([
                'message' => 'Produk bundel tidak ditemukan.'
            ]);
        }
    }

    public function getProductBundle(Request $request)
    {
        $product = Product::query()
            ->whereIn('id', $request->items)
            ->whereNotIn('id', ProductBundle::pluck('product_id'))
            ->with('productUnit')
            ->get();

        return response()->json([
            'response' => $product
        ]);
    }

    public function exportExcel(Request $request)
    {
        $stockType = $request->get('type', 'all'); 
        $company = ProfilCompany::where('status', 'active')->first();
        $tanggal = now()->format('d-m-Y');
        
        return Excel::download(new ProductExport($stockType), $company->name . "_{$stockType}_{$tanggal}.xlsx");
    }
    

    public function templateProduct()
    {
        $filePath = storage_path('app/templates/template_products.xls');

        if (!file_exists($filePath)) {
            abort(404, 'File template tidak ditemukan.');
        }
    
        return response()->download($filePath);
    }

    public function templateStock()
    {
        $filename = 'template_stocks.xls';

        return Excel::download(new ProductStockTemplateExport, $filename);
    }

    public function templatePrice()
    {
        $filename = 'template_prices.xls';

        return Excel::download(new ProductPriceTemplateExport, $filename);
    }

    public function importProduct(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $outletId = auth()->user()->outlet_id ?? 1;

            Excel::import(new ImportProduct($outletId), $request->file('file'));

            return redirect()->back()->with('success', 'Import produk berhasil!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }

    public function importStock(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'destination' => 'required|in:inventory,outlet', 
        ]);
    
        try {
            $destinationType = $request->input('destination');
            $destinationInventoryId = $request->input('destination_inventory_id');
            $destinationOutletId = $request->input('destination_outlet_id');
    
            if ($destinationType === 'inventory') {
                
                $inventory = Inventory::find($destinationInventoryId);

                if (!$inventory) {
                    throw new \Exception('Inventory tidak ditemukan.');
                }

                Excel::import(new ImportStock($destinationInventoryId, $destinationType), $request->file('file'));

            } elseif ($destinationType === 'outlet') {
            
                $outlet = Outlet::find($destinationOutletId);

                if (!$outlet) {
                    throw new \Exception('Outlet tidak ditemukan.');
                }

                Excel::import(new ImportStock($destinationOutletId, $destinationType), $request->file('file'));
            }
            
    
    
            return redirect()->back()->with('success', 'Import stok berhasil!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor stok: ' . $e->getMessage());
        }
    }

    public function importPrices(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new ImportPrice, $request->file('file'));

        return redirect()->back()->with('success', 'Harga produk berhasil diimport.');
    }
    

    
}
