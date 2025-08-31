<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\ReprintLog;
use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\Setting;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected $productController;

    protected $repoTransaction;

    private $user_id;

    private $outlet_id;

    public function __construct(ProductController $productController, TransactionRepository $repoTransaction)
    {
        $this->productController = $productController;
        $this->repoTransaction = $repoTransaction;
    }

    /**
     * penjualan langsung
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'outlet_id' => 'required',
            'user_id' => 'required',
            'payment_method_id' => 'required',
            'nominal_amount' => 'required',
            'discount_amount' => 'required',
            'final_amount' => 'required',
            'nominal_pay' => 'required',
            'nominal_change' => 'required',
            'status' => 'required',
            'sales_details' => 'required|array',
            'creator_user_id' => 'required',
            'cashier_user_id' => 'required',
        ]);

        if ($validated->fails()) {
            return responseAPI(false, 'Validasi gagal', $validated->errors());
        }
        // return response()->json($request->all());

        $payment_method = PaymentMethod::firstOrCreate(
            [
                'name' => $request->payment_method_name,
            ],
            [
                'name' => $request->payment_method_name,
                'transaction_fees' => 0,
            ],
        );

        return $this->repoTransaction->saveAsSales($request, $payment_method->id);
        // return responseAPI(true, 'Berhasil', $res);
    }

    /**
     * transaksi sebagai draft
     *
     * @param  mixed  $request
     */
    public function saveAsDraft(Request $request): JsonResponse
    {
        $this->user_id = auth()->user()->id;
        $this->outlet_id = auth()->user()->outlets[0]?->id;

        $validated = Validator::make($request->all(), [
            'outlet_id' => 'required',
            'user_id' => 'required',
            'payment_method_id' => 'required',
            // 'customer_id' => 'required',
            'nominal_amount' => 'required',
            'discount_amount' => 'required',
            'final_amount' => 'required',
            'nominal_pay' => 'required',
            'nominal_change' => 'required',
            // 'status' => 'required',
            'sales_details' => 'required|array',
            'creator_user_id' => 'required',
            'cashier_user_id' => 'required',
        ]);

        if ($validated->fails()) {
            return responseAPI(false, 'Validasi gagal', $validated->errors());
        }

        $res = $this->repoTransaction->saveAsDraft($request);

        return response()->json($res);
    }

    /**
     * untuk menampilkan list draft
     *
     * @return void
     */
    public function getDrafts()
    {
        $data = $this->repoTransaction->getDrafts();

        return responseAPI(true, 'Berhasil', $data);
    }

    public function getHistories(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $subDay = $request->input('sub_day', 7);

        $outlet_id = optional(Auth::user()->outlets->first())->id;
        $cashier_user_id = Auth::id();

        $his = DB::table('sales')
            ->leftJoin('outlets as ot', 'sales.outlet_id', '=', 'ot.id')
            ->join('users as us', 'sales.user_id', '=', 'us.id')
            ->leftJoin('customers as cs', 'sales.customer_id', '=', 'cs.id')
            ->leftJoin('cashflows as cfl', 'sales.sale_code', '=', 'cfl.transaction_code')
            ->select(
                'us.users_name as user_name',
                'cs.name as customer_name',
                'sales.sale_code',
                'sales.sale_date',
                'sales.id',
                'sales.discount_amount',
                'sales.final_amount as total',
                'sales.payment_status',
                'sales.shipping_status',
                'sales.is_retur',
                'sales.ref_code',
                'sales.retur_type',
                'cfl.cashflow_close_id',
            )
            ->where('sales.status', 'success')
            ->where('sales.outlet_id', $outlet_id)
            ->where('sales.cashier_user_id', $cashier_user_id)
            ->where('sales.sale_date', '>', Carbon::now()->subDays($subDay))
            ->whereNull('cfl.cashflow_close_id')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sales.sale_code', 'like', "%{$search}%")
                        ->orWhere('cs.name', 'like', "%{$search}%");
                });
            })
            ->when($request->shipping_status, fn($query) => $query->where('sales.shipping_status', $request->shipping_status))
            ->orderByDesc('sales.created_at')
            ->paginate($perPage);

        $salesIds = $his->pluck('id')->toArray();

        // Ambil sales_details sekaligus untuk mencegah N+1 query
        $salesDetails = $this->getSalesDetails($salesIds);

        $his->getCollection()->transform(function ($item) use ($salesDetails) {
            $item->sale_date = Carbon::parse($item->sale_date)->translatedFormat('d/m/y, H:i');
            $item->customer_name = $item->customer_name ?? 'no-name';
            $item->cashier_name = $item->user_name;
            $item->discount_amount = (int) $item->discount_amount;
            $item->total = (int) $item->total;
            $item->sales_details = $salesDetails[$item->id] ?? [];

            return $item;
        });

        return responseAPI(true, 'Berhasil', [
            'data' => $his->items(),
            'total' => $his->total(),
            'current_page' => $his->currentPage(),
            'per_page' => $his->perPage(),
            'last_page' => $his->lastPage(),
            'has_more_pages' => $his->hasMorePages(),
        ]);
    }

    /**
     * Mengambil sales details dalam satu query
     */
    private function getSalesDetails(array $salesIds): array
    {
        return DB::table('sales_details')
            ->whereIn('sales_id', $salesIds)
            ->get()
            ->groupBy('sales_id')
            ->toArray();
    }

    /**
     * riwayat transaksi
     *
     * @return void
     */
    private function mapItems($item)
    {
        $sales_details = DB::table('sales_details')
            ->leftJoin('products', 'sales_details.product_id', '=', 'products.id')
            ->select('sales_details.*', 'products.name as product_name', 'products.code as product_code', 'products.capital_price as capital_price')
            ->where('sales_details.sales_id', $item->id)
            ->get();

        // TODO:kalau butuh besok besok, jgn hps dlu
        // $sales_details->map(function ($item) {
        //     $item->sales_detail_id = $item->id;
        //     return $item;
        // });

        return $sales_details;
    }

    public function show($id)
    {
        $id = (int) $id;
        $sal = Sales::find($id);
        $sal->sale_date = $sal->formatted_sale_date;
        $sal->due_date = Carbon::parse($sal->tempo_due_date)->translatedFormat('d-m-Y');

        if (! $sal) {
            return response()->json(
                [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        $sal->load([
            'salesDetails' => function ($query) {
                $query->with('product.productUnit');
            },
            'customer',
            'outlet',
            'paymentMethod',
            'table',
            'user',
            'bank',
        ]);

        // map detail
        $sal->salesDetails = $sal->salesDetails->map(function ($detail) {
            $detail->default_unit_symbol = $detail->product?->productUnit?->symbol ?? 'pcs';

            return $detail;
        });

        $sal->outletImageUrl = ($sal->outlet?->outlet_image && Storage::exists('images/' . $sal->outlet->outlet_image)) ? url('storage/images/' . $sal->outlet->outlet_image) : null;
        $sal->powered_by = config('app.powered_by');
        $sal->custom_footer = Setting::where('name', 'custom_footer')->first()?->desc;

        return response()->json(
            [
                'success' => true,
                'status' => 'success',
                'data' => $sal,
            ],
            200,
        );
    }

    public function void(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                // 'sales_id' => 'required',
                // 'sales_detail_id' => 'required',
                'email' => 'required|email',
                'pin' => 'required',
                'type' => 'required',
                'void_message' => 'required',
            ],
            [
                'email.required' => 'Otorisasi tidak boleh kosong',
                'pin.required' => 'PIN tidak boleh kosong',
            ],
        );

        if ($validate->fails()) {
            return responseAPI(false, $validate->errors()->first(), null);
        }

        $validation = $this->repoTransaction->isValidSuperior($request->email, (int) $request->pin);

        if (! $validation) {
            return responseAPI(false, 'Validasi gagal, Pin yang anda masukkan tidak sesuai', null);
        }

        // ini validasi ketika barang belum di draft
        if ($request->void_type == 'non_draft_item' || $request->void_type == 'non_draft_cart') {
            $res = $this->repoTransaction->voidNonDraft($request);

            return response()->json($res);
        }

        if ($request->type == 'changeQty') {
            return responseAPI(true, 'Berhasil', null);
        }

        if ($request->type == 'item') {
            $res = $this->repoTransaction->voidItem($request);
        } elseif ($request->type == 'cart') {
            $res = $this->repoTransaction->voidCart($request);
        } else {
            // yang akhir ini untuk qty
        }

        return response()->json($res);
    }

    public function voidWithoutValidation(Request $request)
    {
        // dd($request->all());
        if ($request->type == 'item') {
            $res = $this->repoTransaction->voidItem($request);
        } elseif ($request->type == 'cart') {
            $res = $this->repoTransaction->voidCart($request);
        } else {
            // yang akhir ini untuk qty
        }

        return response()->json($res);
    }

    public function reprintLog(Sales $sales): array
    {
        if (! $sales) {
            return [
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
                'data' => null,
            ];
        }

        try {
            // Gunakan increment langsung ke kolom database
            $sales->increment('reprint_count');
            $sales->save();

            // save to log
            $log = new ReprintLog;
            $log->sales_id = $sales->id;
            $log->user_id = auth()->user()->id;
            $log->reprint_time = now();
            $log->save();

            $success = true;
            $message = 'Transaksi berhasil dicetak ulang';
            $data = null;
        } catch (\Exception $e) {
            $success = false;
            $message = 'Transaksi gagal dicetak ulang';
            $data = [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function refundFull(Sales $sales, Request $request): JsonResponse
    {
        $sales->load('salesDetails');

        DB::beginTransaction();
        try {
            $sl_retur = $this->__createNewReturRowSales($sales, $request, 'full');

            foreach ($sales->salesDetails as $sod) {
                if ($sod->parent_sales_detail_id != null) {
                    continue;
                }
                $this->__storeToSalesDetails('full', $sod, $sl_retur, $sod->qty);
            }

            // sales update subtotal dan final_amaount
            $this->__updateReturSales($sl_retur, $request);
            $this->__updateOriginalSales($sales, $request);
            $this->__updateProductStock($sl_retur, $request);
            $this->__createReturCashflow($sl_retur, $request);

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Transaksi berhasil di refund',
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    public function refundPartial(Sales $sales, Request $request): JsonResponse
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            // clone sales
            $sl_retur = $this->__createNewReturRowSales($sales, $request, 'partial');

            // clone sales detail
            foreach ($request->items as $req_item) {
                $salesDetailOri = SalesDetail::where('id', $req_item['id'])->first();
                $this->__storeToSalesDetails('partial', $salesDetailOri, $sl_retur, $req_item['qty_refund']);
            }

            // sales update subtotal dan final_amaount
            $this->__updateReturSales($sl_retur, $request);
            $this->__updateOriginalSales($sales, $request);
            $this->__updateProductStock($sl_retur, $request);
            $this->__createReturCashflow($sl_retur, $request);

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Transaksi berhasil di refund',
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    private function __updateProductStock($sales_retur, $request): void
    {

        // dd($sales_retur->id);
        $hppRetur = 0;
        foreach ($sales_retur->salesDetails as $sd) {
            if ($sd->parent_sales_detail_id != null) { // jika dia bukan stok utama maka lewati./skip aja
                continue;
            }
            // Log::info('sd_id: ' . $sd->id);
            Log::info('sd_id: ' . $sd->id);
            // jika bundle maka cek product -> is_main_stock
            $original_product = Product::find($sd->product_id);
            $is_main_stock = $original_product->is_main_stock;
            // jika is_main_stock maka ambilkan dari stock utama.
            // jika tidak main stock maka ambilkan dari product nya

            // dd($sales_retur);
            if ($is_main_stock == 0) {
                Log::info('run is not main stok');
                $this->__updateStockAndHistory($sales_retur, $sd);
            } else {
                Log::info('run main stok');
                Log::info($sd);
                $salesDetailChilds = SalesDetail::where('parent_sales_detail_id', $sd->id)->get();
                Log::info('sales_detail child ');
                Log::info($salesDetailChilds->toJson());
                foreach ($salesDetailChilds as $sdChild) {
                    $this->__updateStockAndHistory($sales_retur, $sdChild);
                }
            }

            $product = Product::find($sd->product_id);
            $hppRetur += $product->capital_price * $sd->qty;
        }

        // update cashflow
        $cf = Cashflow::where('transaction_code', $sales_retur->ref_code)->first();

        if ($cf) {
            if ($sales_retur->paymentMethod->name != 'TEMPO') {
                $cf->amount -= $sales_retur->final_amount;
                $cf->total_hpp -= $hppRetur;
                $cf->profit = $cf->amount - $cf->total_hpp;
                $cf->save();
            }
        }
    }

    private function __updateStockAndHistory($sales_retur, $sd)
    {
        $product_stock = ProductStock::where('product_id', $sd->product_id)
            ->where('outlet_id', $sales_retur->outlet_id)
            ->first();

        // mengubah kembali qty product stock
        ProductStock::where('product_id', $sd->product_id)
            ->where('outlet_id', $sales_retur->outlet_id)
            ->update([
                'stock_current' => $product_stock->stock_current + $sd->qty,
            ]);

        if ($product_stock) {
            $ps_after = ProductStock::where('id', $product_stock->id)->first();

            if ($product_stock->stock_current < $ps_after->stock_current) {
                $action_type = 'PLUS';
                $description = 'PENAMBAHAN dari Retur';
            } else {
                $action_type = 'MINUS';
                $description = 'PENGURANGAN dari Retur';
            }

            ProductStockHistory::create([
                'document_number' => $sales_retur->code,
                'history_date' => Carbon::now(),
                'action_type' => $action_type,
                'product_id' => $product_stock->product_id,
                'outlet_id' => $product_stock->outlet_id,
                'stock_change' => $sd->qty,
                'stock_before' => $product_stock->stock_current,
                'stock_after' => $ps_after->stock_current,
                'desc' => 'Retur : ' . $sales_retur->code . ' Melakukan ' . $description . ' Qty',
                'user_id' => getUserIdLogin(),
            ]);
        }
    }

    private function __createNewReturRowSales($sales, $request, $returType): ?Sales
    {

        $retur_code = IdGenerator::generate(returConfigCode());

        try {
            $retur = $sales->replicate();
            $retur->sale_code = $retur_code;
            $retur->ref_code = $sales->sale_code;
            $retur->is_retur = true;
            $retur->sales_parent_id = $sales->id;
            $retur->creator_user_id = Auth::user()->id;
            $retur->cashier_user_id = Auth::user()->id;
            $retur->receipt_code = null;
            $retur->refund_reason = $request->reason;
            $retur->retur_type = $returType;
            // ini harus di update
            // $retur->nominal_pay = 0;
            // $retur->nominal_amount = 0;
            // $retur->final_amount = 0;
            $retur->save();

            $sales->ref_code = $retur_code;
            $sales->refund_reason = $request->reason;
            $sales->save();

            return $retur;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    private function __storeToSalesDetails($refund_type, $original_sales_detail_item, $sales_retur, $qty_retur = null)
    {
        DB::beginTransaction();
        try {
            $qty_retur = $qty_retur ?? $original_sales_detail_item->qty_retur;

            $sl_detail_retur = $original_sales_detail_item->replicate(); // clone
            $sl_detail_retur->sales_id = $sales_retur->id;
            $sl_detail_retur->hpp = null;
            $sl_detail_retur->profit = 0;
            $sl_detail_retur->status = 'retur';
            $sl_detail_retur->is_retur = false;
            $sl_detail_retur->qty = $qty_retur;
            $sl_detail_retur->save();

            // cek juga apakah ada item detail punya child lagi (bundles)
            $getChilds = SalesDetail::where('parent_sales_detail_id', $original_sales_detail_item->id)->get();

            // aku terapkan ini hanya untuk partial
            if ($getChilds) {
                foreach ($getChilds as $child) {
                    // dapetin dlu bundle object db nya. buat dapetin real qty paket nya
                    $productBundle = ProductBundle::where('product_bundle_id', $original_sales_detail_item->product_id)
                        ->where('product_id', $child->product_id)
                        ->first();

                    $sl_detail_replicate = $child->replicate();
                    $sl_detail_replicate->parent_sales_detail_id = $sl_detail_retur->id;
                    $sl_detail_replicate->status = 'retur';
                    $sl_detail_replicate->qty = $qty_retur * $productBundle->qty;
                    $sl_detail_replicate->save();
                }
            }

            $new_qty = $original_sales_detail_item->qty;
            $original_sales_detail_item->qty = $new_qty;
            $original_sales_detail_item->subtotal = $new_qty * $original_sales_detail_item->final_price;
            $original_sales_detail_item->status = 'retur';
            $original_sales_detail_item->qty_retur = $qty_retur;
            $original_sales_detail_item->is_retur = true;
            $original_sales_detail_item->profit = ($original_sales_detail_item->final_price - $original_sales_detail_item->hpp) * $new_qty;
            $original_sales_detail_item->save();

            $sl_detail_retur->subtotal = $sl_detail_retur->qty * $sl_detail_retur->final_price;
            $sl_detail_retur->save();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return false;
        }
    }

    private function __updateReturSales($sales_retur, $request)
    {
        if ($request->has('grandtotal_refund')) {
            $grand_total = $request->grandtotal_refund;
        } else {
            $grand_total = $sales_retur->salesDetails->sum('subtotal');
        }
        $sales_retur->nominal_pay = 0;
        $sales_retur->nominal_change = 0;
        $sales_retur->nominal_amount = $grand_total;
        $sales_retur->final_amount = $grand_total - $sales_retur->discount_amount;
        $sales_retur->nominal_refund = $grand_total;
        $sales_retur->save();
    }

    private function __updateOriginalSales($sales, $request)
    {
        // ambild data retur
        $retur_obj = Sales::where('sales_parent_id', $sales->id)->first();

        // kalkulasi ulang subtotal sales ytg ori
        $subtotal = $sales->salesDetails->sum('subtotal');
        $nominal_refund = $retur_obj?->nominal_refund;

        $sales->nominal_amount = $subtotal;
        $sales->final_amount = $subtotal - $sales->discount_amount;
        $sales->nominal_refund = $nominal_refund;
        $sales->save();
    }

    private function __createReturCashflow($sales_retur, $request)
    {
        $cashflow = new Cashflow();
        $cashflow->outlet_id = auth()->user()->outlets[0]->id;
        $cashflow->user_id = auth()->user()->id;
        $cashflow->code = IdGenerator::generate([
            'table' => 'cashflows',
            'field' => 'code',
            'length' => 11,
            'prefix' => 'CF' . date('ym') . '-',
            'reset_on_prefix_change' => true,
        ]);
        $cashflow->transaction_code = $sales_retur->sale_code;
        $cashflow->transaction_date = Carbon::now();
        $cashflow->type = 'out';
        $cashflow->amount = $sales_retur->nominal_refund;
        $cashflow->total_hpp = $sales_retur->salesDetails->sum('total_hpp');
        $cashflow->profit = null;
        $cashflow->desc = 'Retur';
        $cashflow->save();
    }

    public function syncPrices($id)
    {
        $sales = Sales::with('salesDetails.productPrice')->findOrFail($id);

        if ($sales->status !== 'draft') {
            return response()->json(['message' => 'Hanya sales dengan status draft yang bisa disinkronkan'], 400);
        }

        $hasChanges = false;
        $nominalAmount = 0;
        $discountAmount = 0;
        $finalAmount = 0;

        foreach ($sales->salesDetails as $detail) {
            $productPrice = $detail->productPrice;

            if ($productPrice && $detail->price != $productPrice->price) {
                $detail->price = $productPrice->price;
                $hasChanges = true;
            }

            // Hitung diskon per item (persen)
            $itemDiscount = ($detail->price * $detail->discount) / 100;
            $finalPrice = $detail->price - $itemDiscount;

            $detail->final_price = $finalPrice;
            $detail->subtotal = $finalPrice * $detail->qty;
            $detail->profit = $detail->subtotal - (($detail->hpp ?? 0) * $detail->qty);

            $detail->save();

            $nominalAmount += $detail->price * $detail->qty;
            $discountAmount += $itemDiscount * $detail->qty;
            $finalAmount += $detail->subtotal;
        }

        $sales->nominal_amount = $nominalAmount;
        $sales->discount_amount = $discountAmount;
        $sales->final_amount = $finalAmount;
        $sales->nominal_change = 0;
        $sales->save();

        return response()->json([
            'sales' => $sales->fresh('salesDetails.productPrice'),
            'message' => $hasChanges ? 'Harga produk berhasil disinkronkan' : null
        ]);
    }
}
