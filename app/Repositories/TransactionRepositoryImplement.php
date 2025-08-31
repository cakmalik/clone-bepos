<?php

namespace App\Repositories;

use App\Models\Cashflow;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\ProductUnit;
use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransactionRepositoryImplement implements TransactionRepository
{
    private $user_id;

    private $outlet_id;

    /**
     * untuk memvalidasi apakah dapat izin dari atasan
     *
     * @param  mixed  $email
     * @param  mixed  $pin
     */
    public function isValidSuperior(string $email, int $pin): bool
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return false;
        }

        if ($user->decryptedPin() === $pin) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * menampilkan transaksi yang status nya draft
     */
    public function getDrafts(): array
    {
        $cashier_user_id = auth()->user()->id;
        $draft = DB::table('sales')
            ->leftJoin('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->leftJoin('users', 'sales.user_id', '=', 'users.id')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('sales.*', 'outlets.name as outlet_name', 'users.users_name as user_name', 'customers.name as customer_name')
            ->where('sales.status', 'draft')
            ->whereDate('sales.created_at', Carbon::today())
            ->where('sales.outlet_id', auth()->user()->outlets[0]?->id)
            ->where('sales.user_id', $cashier_user_id)
            ->get();

        $draft = $this->__mapingDraft($draft);

        $data = [
            'data' => $draft,
            'total' => $draft->count(),
        ];

        return $data;
    }

    private function __mapingDraft($draft)
    {
        return $draft->map(function ($item) {
            $item->sales_details = $this->mapItemSalesDetail($item);
            $item->customer_id = $item->customer_id ? $item->customer_id : null;
            $item->customer_name = $item->customer_id ? $item->customer_name : null;
            $item->date = Carbon::parse($item->created_at)->translatedFormat('d F y, H:i');

            return $item;
        });
    }

    /**
     * menyimpan transaksi sebagai draft
     *
     * @param  mixed  $request
     */
    public function saveAsDraft($request): array
    {
        // dd($request->all());
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;

        // cek apakah fungsi untuk update draft
        if ($request->selected_sales_id != null && $request->is_update_draft == true) {
            $sales = Sales::find($request->selected_sales_id);
            if ($sales) {
                $res = $this->updateDraftOrSale($request, 'draft'); // NOTE:handle existing draft

                return $res;
            }
        }
        //  cek apakah sudah ada sales_id ny
        if ($request->selected_sales_id != null) {
            $sales = Sales::find($request->selected_sales_id);
            if ($sales) {
                $res = $this->updateDraftOrSale($request, 'draft'); // NOTE:handle existing draft

                return $res;
            }
        }

        DB::beginTransaction();
        try {
            // store to sales
            $saveToSalesTable = $this->storeToSales($request);
            if (! $saveToSalesTable['success']) {
                DB::rollBack();

                return responseAPI(false, $saveToSalesTable['message'], $saveToSalesTable['data']);
            }

            // store to sales_details termasuk product stock dan product stock hisstories
            $saveToSalesDetailTable = $this->storeToSalesDetail($saveToSalesTable['data'], $request);
            if (! $saveToSalesDetailTable['success']) {
                DB::rollBack();

                return responseAPI(false, $saveToSalesDetailTable['message'], $saveToSalesDetailTable['data']);
            }

            DB::commit();
            $success = true;
            $message = 'Transaksi berhasil disimpan';
            $data = null;
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    /**
     * menyimpan transaksi secara langsung status sebagai success
     *
     * @param  mixed  $request
     */
    public function saveAsSales($request, $payment_method_id): array
    {
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;

        //  cek apakah sudah ada sales_id nya
        if ($request->selected_sales_id != null) {
            $sales = Sales::find($request->selected_sales_id);
            if ($sales) {
                $res = $this->updateDraftOrSale($request, 'sales'); // NOTE:handle existing draft

                return $res;
            }
        }

        DB::beginTransaction();
        try {
            // store to sales
            $saveToSalesTable = $this->storeToSales($request, 'success', $payment_method_id);
            if (! $saveToSalesTable['success']) {
                DB::rollBack();

                return responseAPI(false, $saveToSalesTable['message'], $saveToSalesTable['data']);
            }

            // store to sales_details termasuk product stock dan product stock hisstories
            $saveToSalesDetailTable = $this->storeToSalesDetail($saveToSalesTable['data'], $request, 'success');
            if (! $saveToSalesDetailTable['success']) {
                DB::rollBack();

                return responseAPI(false, $saveToSalesDetailTable['message'], $saveToSalesDetailTable['data']);
            }

            // store to cashflow

            // $paymentMethod = PaymentMethod::find($payment_method_id);

            // if ($paymentMethod->name != 'TEMPO') {
            $saveToCashflowTable = $this->storeToCashflow($saveToSalesTable['data'], $saveToSalesDetailTable['data'], $request);
            if (! $saveToCashflowTable['success']) {
                DB::rollBack();

                return responseAPI(false, $saveToCashflowTable['message'], $saveToCashflowTable['data']);
            }
            // }

            DB::commit();
            $success = true;
            $message = 'Transaksi berhasil';
            $data = $saveToSalesTable['data'];
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    public function updateDraftOrSale($request, string $type = 'draft')
    {
        $payment_method = PaymentMethod::firstOrCreate(
            [
                'name' => $request->payment_method_name ?? 'Cash',
            ],
            [
                'name' => $request->payment_method_name ?? 'Cash',
                'transaction_fees' => 0,
            ],
        );

        $type_desc = $type == 'draft' ? 'Draft / ' : 'Penjualan / ';
        $type_update = $type == 'draft' ? 'Update Draft / ' : 'Penjualan / ';

        // return responseAPI(false, '', $request->all());
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;

        if (count($req_sales_detail = $request->sales_details) == 0) {
            return responseAPI(false, 'Tidak ada item yang dipilih');
        }

        DB::beginTransaction();
        try {
            $sale = Sales::find($request->selected_sales_id);
            $total_profit = 0;
            $total_hpp = 0;
            foreach ($req_sales_detail as $rsd) {
                if ($rsd['new_price'] != false || $rsd['new_price'] != 0) {
                    $new_price = $rsd['new_price'];
                } else {
                    $new_price = $rsd['price'];
                }

                // cari apa sudah ada atau belum produknya
                $sales_detail = SalesDetail::where('sales_id', $request->selected_sales_id)
                    ->where('product_id', $rsd['id'])
                    ->first();
                $selectedVariantId = $rsd['selected_variant_id'] ?? $this->__getMainProductPriceId($rsd['id']);
                // Ambil harga terbaru dari tabel product_prices
                $product_price = ProductPrice::find($selectedVariantId);

                // Gunakan harga dari product_price jika berbeda dari yang di-draft
                if ($product_price && $sales_detail && $product_price->price != $sales_detail->price) {
                    $new_price = $product_price->price;
                } elseif (!empty($rsd['new_price']) && $rsd['new_price'] > 0) {
                    $new_price = $rsd['new_price'];
                } else {
                    $new_price = $rsd['price'];
                }

                // jika sudah ada
                if ($sales_detail) {
                    // 1. update stock history
                    $product_stock_history = ProductStockHistory::where('outlet_id', $sale->outlet_id)
                        ->where('product_id', $rsd['id'])
                        ->latest()
                        ->first();

                    if ($rsd['quantity'] > $sales_detail->qty) {
                        $next_action_type = 'minus';
                        $next_stock_change = $rsd['quantity'] - $sales_detail->qty;
                    } elseif ($rsd['quantity'] < $sales_detail->qty) {
                        $next_action_type = 'plus';
                        $next_stock_change = $sales_detail->qty - $rsd['quantity'];
                    } else {
                        $next_action_type = 'none';
                        $next_stock_change = 0;
                    }

                    $newStock = $next_action_type == 'minus' ? $product_stock_history->stock_after - $next_stock_change : $product_stock_history->stock_after + $next_stock_change;
                    $updateQty = $next_action_type == 'minus' ? '(+QTY) ' : '(-QTY) ';


                    if ($next_action_type == 'minus' || $next_action_type == 'plus') {
                        // kode ini ketika masuk draft - checkout draft - update qty - payment
                        // new product stock histories
                        $psh = new ProductStockHistory;
                        $psh->outlet_id = $product_stock_history->outlet_id;
                        $psh->user_id = $product_stock_history->user_id;
                        $psh->product_id = $product_stock_history->product_id;
                        $psh->document_number = $product_stock_history->document_number;
                        $psh->history_date = now();
                        $psh->stock_change = $next_stock_change;
                        $psh->stock_before = $product_stock_history->stock_after;
                        $psh->stock_after = $newStock;
                        $psh->action_type = $next_action_type;
                        $psh->desc = $updateQty . $type_desc . $sale->sale_code;
                        $psh->save();

                        // 2. update product stock
                        $product_stock = ProductStock::where([
                            ['product_id', $rsd['id']],
                            ['outlet_id', $this->outlet_id],
                        ])->first();
                        $product_stock->stock_current = $newStock;
                        $product_stock->save();
                    } else {
                        if ($type != 'draft') {
                            $psh = new ProductStockHistory;
                            $psh->outlet_id = $product_stock_history->outlet_id;
                            $psh->user_id = $product_stock_history->user_id;
                            $psh->product_id = $product_stock_history->product_id;
                            $psh->document_number = $product_stock_history->document_number;
                            $psh->history_date = now();
                            $psh->stock_change = $next_stock_change;
                            $psh->stock_before = $product_stock_history->stock_after;
                            $psh->stock_after = $product_stock_history->stock_after;
                            $psh->action_type = $next_action_type;
                            $psh->desc = 'Draft ke Penjualan / ' . $sale->sale_code;

                            $psh->save();
                        }
                    }

                    // return [
                    //     'success' => false,
                    //     'message' => 'a',
                    //     'data' => $request->all(),
                    //     'data2'=> $req_sales_detail
                    // ];

                    // update sales_detail
                    $sales_detail->price = $rsd['price'];
                    $sales_detail->hpp = $rsd['capital_price'];
                    $sales_detail->qty = $rsd['quantity'];
                    $sales_detail->unit_symbol = $rsd['selected_unit_symbol'];
                    $sales_detail->unit_id = $rsd['selected_unit_id'];
                    $sales_detail->product_price_id = $rsd['selected_variant_id'];
                    $sales_detail->final_price = $new_price;
                    $sales_detail->subtotal = $new_price * $rsd['quantity'];
                    $sales_detail->profit = ($new_price - $rsd['capital_price']) * $rsd['quantity'];
                    $sales_detail->is_tiered = $rsd['is_tier'] ?? false;
                    if ($sales_detail->status === 'void') {
                        $sales_detail->status = 'success';
                    }
                    $sales_detail->save();

                    $total_profit += $sales_detail->profit;

                    $product = Product::find($sales_detail->product_id);
                    $total_hpp += $product->capital_price * $sales_detail->qty;
                }

                // insert sales_detail
                if (! $sales_detail) {
                    // return [
                    //     'success' => false,
                    //     'message' => 'b',
                    //     'data' => 'bb',
                    // ];
                    $sd = new SalesDetail;
                    $sd->sales_id = $request->selected_sales_id;
                    $sd->outlet_id = $this->outlet_id;
                    $sd->user_id = $this->user_id;
                    $sd->product_id = $rsd['id'];
                    $sd->product_price_id = $selectedVariantId;
                    $sd->product_name = $rsd['name'];
                    $sd->price = $rsd['price'];
                    $sd->hpp = $rsd['capital_price'];
                    $sd->discount = $rsd['discount'] ?? 0;
                    $sd->qty = $rsd['quantity'];
                    $sd->unit_symbol = $rsd['selected_unit_symbol'];
                    $sd->unit_id = $rsd['selected_unit_id'];
                    $sd->final_price = $new_price;
                    $sd->subtotal = $new_price * $rsd['quantity'];
                    $sd->profit = ($new_price - $rsd['capital_price']) * $rsd['quantity'];
                    $sd->save();

                    // update stock
                    $product_stock = ProductStock::where([
                        ['product_id', $rsd['id']],
                        ['outlet_id', $this->outlet_id],
                    ])->first();

                    $product_stock->stock_current -= $rsd['quantity'];
                    $product_stock->save();

                    // input ke product_stock_histories
                    $psh = new ProductStockHistory;
                    $psh->outlet_id = $this->outlet_id;
                    $psh->user_id = $this->user_id;
                    $psh->product_id = $rsd['id'];
                    $psh->inventory_id = null;
                    $psh->document_number = $sale->sale_code;
                    $psh->history_date = now();
                    $psh->stock_change = $rsd['quantity'];
                    $psh->stock_before = $product_stock->stock_current + $rsd['quantity'];
                    $psh->stock_after = $product_stock->stock_current;
                    $psh->action_type = 'minus';
                    $psh->desc = $type_update . $sale->sale_code;

                    $psh->save();
                }
            }

            // kalkulasi ulang
            $sales = Sales::find($request->selected_sales_id);
            $sales->nominal_amount = $request->nominal_amount;
            $sales->discount_amount = $request->discount_amount ?? 0;
            $sales->discount_type = $request->discount_type ?? 'nominal';
            $sales->final_amount = $request->final_amount;
            $sales->nominal_pay = $request->nominal_pay;
            $sales->nominal_change = $request->nominal_change;
            $sales->payment_method_id = $payment_method->id ?? 1;
            $sales->transaction_fees = $request->transaction_fees ?? 0;
            $sales->status = $type == 'draft' ? 'draft' : 'success';

            $sales->bank_id = $request->bank_id;
            $sales->note = $request->note;

            $sales->save();

            // INPUT To CASHFLOW
            $isExistCashflow = Cashflow::where('transaction_code', $sales->sale_code)->first();
            if ($request->status == 'success' && ! $isExistCashflow) {
                $cashflow = new Cashflow;
                $cashflow->outlet_id = $this->outlet_id;
                $cashflow->user_id = $this->user_id;
                $cashflow->code = IdGenerator::generate([
                    'table' => 'cashflows',
                    'field' => 'code',
                    'length' => 11,
                    'prefix' => 'CF' . date('ym') . '-',
                    'reset_on_prefix_change' => true,
                ]);
                $cashflow->transaction_code = $sales->sale_code;
                $cashflow->transaction_date = $sales->sale_date;
                $cashflow->type = 'in';
                $cashflow->amount = $sales->final_amount;
                $cashflow->profit = $total_profit;
                $cashflow->total_hpp = $total_hpp;
                $cashflow->desc = 'Penjualan';
                $cashflow->save();
            }

            DB::commit();
            $message = $sales->status = $type == 'draft' ? 'Berhasil disimpan dan diperbarui' : 'Transaksi berhasil';
            $success = true;
            $data = [
                'id' => $sales->id, // pikirkan kalo mau merubah ini karena digunakan di front end
                'nominal_change' => $sales->nominal_change,
            ];
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    public function voidCart($request): array
    {
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;

        DB::beginTransaction();
        try {
            // update sales
            $sales = Sales::find($request->sales_id);
            $sales->status = 'void';
            $sales->note = $request->void_message ?? 'Batal Pesan';
            $sales->save();

            // update product stock
            $sales_details = SalesDetail::where('sales_id', $request->sales_id)
                ->where('status', '!=', 'void')
                ->get();

            foreach ($sales_details as $sales_detail) {
                $product_stock = ProductStock::where([
                    ['product_id', $sales_detail->product_id],
                    ['outlet_id', $this->outlet_id],
                ])->first();
                $product_stock->stock_current += $sales_detail->qty;
                $product_stock->save();

                // input ke product_stock_histories
                $psh = new ProductStockHistory;
                $psh->outlet_id = $this->outlet_id;
                $psh->user_id = $this->user_id;
                $psh->product_id = $sales_detail->product_id;
                $psh->inventory_id = null;
                $psh->document_number = $sales->sale_code;
                $psh->history_date = now();
                $psh->stock_change = $sales_detail->qty;
                $psh->stock_before = $product_stock->stock_current - $sales_detail->qty;
                $psh->stock_after = $product_stock->stock_current;
                $psh->action_type = 'plus';
                $psh->desc = 'Void / ' . $sales->sale_code;

                $psh->save();

                //update sales details
                $sales_detail->status = 'void';
                $sales_detail->save();
            }

            DB::commit();
            $success = true;
            $message = 'Transaksi berhasil dibatalkan';
            $data = null;
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    public function voidItem($request): array
    {
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;

        DB::beginTransaction();
        try {
            //             kalau void item berarti :
            // - table sales_details status diganti void
            // - adjust sales table nominal_amount dkk
            // - product stock dikembalikan
            // - product stock histori di adjust

            // pertama, cek dulu apakah itu adalah item satu-satunya yg statusnya draft dengan sales_id yang sama
            $sales_detail = SalesDetail::find($request->sales_detail_id);
            $sales_details = SalesDetail::where('sales_id', $request->sales_id)
                ->where('status', '!=', 'void')
                ->get();

            // jika iya, maka langsung ganti juga sales table menjadi void
            if ($sales_details->count() == 1) {
                $sales = Sales::find($request->sales_id);
                $sales->status = 'void';
                $sales->note = $request->void_message ?? 'Batal Pesan';
                $sales->save();
            }

            // update sales_details
            $sales_detail->status = 'void';
            $sales_detail->save();

            // update sales
            $sales = Sales::find($request->sales_id);
            if (!$sales) {
                throw new Exception('Data penjualan tidak ditemukan.');
            }
            $sales->nominal_amount -= $sales_detail->subtotal;
            $sales->final_amount -= $sales_detail->subtotal;
            $sales->save();

            // update product stock
            $product_stock = ProductStock::where([
                ['product_id', $sales_detail->product_id],
                ['outlet_id', $this->outlet_id],
            ])->first();
            $product_stock->stock_current += $sales_detail->qty;
            $product_stock->save();

            // input ke product_stock_histories
            $psh = new ProductStockHistory;
            $psh->outlet_id = $this->outlet_id;
            $psh->user_id = $this->user_id;
            $psh->product_id = $sales_detail->product_id;
            $psh->inventory_id = null;
            $psh->document_number = $sales->sale_code;
            $psh->history_date = now();
            $psh->stock_change = $sales_detail->qty;
            $psh->stock_before = $product_stock->stock_current - $sales_detail->qty;
            $psh->stock_after = $product_stock->stock_current;
            $psh->action_type = 'plus';
            $psh->desc = 'Void Item / ' . $sales->sale_code; // ini adalah flow ketika cart tingal satu item lalu delete item (bukan void)

            $psh->save();

            DB::commit();
            $success = true;
            $message = 'Item berhasil dibatalkan';
            $data = [
                'is_last_item' => $sales_details->count() == 1 ? true : false,
            ];
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    public function baseTemp($request): array
    {
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;

        DB::beginTransaction();
        try {
            DB::commit();
            $success = true;
            $message = 'Transaksi berhasil disimpan';
            $data = null;
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    /**
     * buat ngeluarin yang dibutuhin :) maping sales detail
     *
     * @param  mixed  $item
     * @return void
     */
    public function mapItemSalesDetail($item)
    {
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $sales_details = DB::table('sales_details')
            ->leftJoin('products', 'sales_details.product_id', '=', 'products.id')
            ->select('sales_details.*', 'products.name as product_name', 'products.code as product_code', 'products.capital_price as capital_price')
            ->where('sales_details.sales_id', $item->id)
            ->where('status', '!=', 'void')
            ->get();

        $sales_details->map(function ($item) {
            $product = Product::find($item->product_id);
            $product_stock = ProductStock::where([
                ['product_id', $item->product_id],
                ['outlet_id', $this->outlet_id],
            ])->first();

            $variantPrices = ProductPrice::where('product_id', $product->id)
                ->with('sellingPrice:id,name')
                ->get(['id', 'product_id', 'price', 'type', 'selling_price_id']);

            if ($variantPrices->count() > 1) {
                $product->variant_prices = $variantPrices->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => optional($variant->sellingPrice)->name,
                        'price' => $variant->price,
                        'type' => $variant->type,
                    ];
                });
            } elseif ($variantPrices->count() === 1 && $variantPrices->first()->type !== 'utama') {
                $product->variant_prices = $variantPrices->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => optional($variant->sellingPrice)->name,
                        'price' => $variant->price,
                        'type' => $variant->type,
                    ];
                });
            } else {
                $product->variant_prices = [];
            }

            $stock_current = ($product_stock->stock_current ?? 0) + $item->qty;

            $unit_collection = ProductUnit::where('base_unit_id', $product->product_unit_id)
                ->orWhere('id', $product->product_unit_id)
                ->get(['base_unit_id', 'id', 'name', 'symbol', 'conversion_rate'])
                ->map(function ($row) use ($product) {
                    $row->selected = $row->id == $product->product_unit_id;

                    return $row;
                });

            $item->sales_id                 = $item->sales_id;
            $item->sales_detail_id          = $item->id;
            $item->id                       = $item->product_id;
            $item->barcode                  = $product->barcode;
            $item->product_unit_id          = $item->unit_id;
            $item->selected_variant_id      = $item->product_price_id;
            $item->code                     = $product->code;
            $item->name                     = $product->name;
            $item->capital_price            = $item->hpp;
            $item->price                    = $item->price;
            $item->stock_current            = $stock_current;
            $item->unit_symbol              = $item->unit_symbol;
            $item->desc                     = null;
            $item->is_support_qty_decimal   = $product->is_support_qty_decimal;
            $item->is_bundle                = $product->is_bundle;
            $item->is_main_stock            = $product->is_main_stock;
            $item->discount_collection      = null;
            $item->tiered_prices            = null;
            $item->variant_prices           = $product->variant_prices;
            $item->new_price                = $item->final_price;
            $item->quantity                 = floatval($item->qty);
            $item->is_tier                  = false;
            $item->subtotal                 = $item->final_price * $item->qty;
            $item->focused                  = false;
            $item->unit_collection          = $unit_collection;

            return $item;
        });

        return $sales_details;
    }

    /**
     * menyimpan ke sales table
     *
     * @param  mixed  $request
     * @param  mixed  $status
     */
    public function storeToSales($request, string $status = 'draft', $payment_method_id = 1): array
    {
        $payment_method = PaymentMethod::find($payment_method_id);

        if ($payment_method->name == 'TEMPO' && $request->customer_id == null) {
            return [
                'success' => false,
                'message' => 'Pembayaran tempo harus menambahkan data pelanggan!',
                'data' => null,
            ];
        }
        DB::beginTransaction();
        try {

            // input ke sales
            $sale = new Sales;
            $sale->outlet_id = $this->outlet_id;
            $sale->cashier_machine_id = $request->cashier_machine_id;
            $sale->user_id = $this->user_id;
            $sale->payment_method_id = $payment_method_id ?? 1;
            $sale->customer_id = $request->customer_id;
            // $sale->journal_number_id = $request->journal_number_id;
            $sale->sale_code = saleCode();
            $sale->ref_code = $request->ref_code;
            $sale->customer_id = $request->customer_id;
            $sale->sale_date = now();
            $sale->nominal_amount = $request->nominal_amount;
            $sale->discount_amount = $request->discount_amount ?? 0;
            $sale->discount_type = $request->discount_type ?? 'nominal';
            $sale->transaction_fees = ($status == 'draft') ? 0 : (int) $request->transaction_fees;
            $sale->final_amount = $request->final_amount;
            $sale->nominal_pay = $request->nominal_pay;
            $sale->nominal_change = $request->nominal_change;
            $sale->is_retur = false;
            $sale->status = $status;

            $sale->bank_id = $request->bank_id;
            $sale->note = $request->note;

            $sale->creator_user_id = $request->creator_user_id;
            $sale->cashier_user_id = $request->cashier_user_id;
            $sale->receipt_code = generateSafeId();
            $sale->due_date = $request->tempo_due_date;
            $sale->save();

            $success = true;
            $message = 'Transaksi berhasil disimpan';
            $data = [
                'id' => $sale->id, // NOTE:pikikan lagi kalau mau mengubah ini, karena ini diterapkan di front end
                'sale_code' => $sale->sale_code,
                'sale_date' => $sale->sale_date,
                'nominal_change' => $sale->nominal_change,
                'receipt_url' => url('r/' . $sale->receipt_code),
                'orderId' => $sale->sale_code,
                'orderTotal' => 'Rp' . number_format($sale->final_amount),
                'customerName' => $sale->customer?->name == 'Walk-in-customer' ? 'Pelanggan Setia' : $sale->customer->name,
                'customerPhone' => formatToWhatsappPhone($sale->customer?->phone),
                'outletName' => $sale->outlet?->name,
                'outletImageUrl' => ($sale->outlet?->outlet_image && Storage::exists('images/' . $sale->outlet->outlet_image)) ? url('storage/images/' . $sale->outlet->outlet_image) : null,
                'powered_by' => config('app.powered_by'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            $success = false;
            $message = 'Transaksi gagal';
            $data = [
                'warning' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * menyimpan ke sales_details table dan beberapa yang terkait termasuk product stock dan product stock histories
     *
     * @param  mixed  $sale
     * @param  mixed  $request
     */
    public function storeToSalesDetail($sale, $request, string $status = 'draft'): array
    {

        // is_main_stock

        // return responseAPI(false, '', $request->all());
        DB::beginTransaction();
        try {
            $total_profit = 0;
            $total_hpp = 0;
            foreach ($request->sales_details as $sales_detail) {
                // dd($sales_detail["is_main_stock"] == 1);
                if ($sales_detail['new_price'] != false || $sales_detail['new_price'] != 0) {
                    $new_price = $sales_detail['new_price'];
                } else {
                    $new_price = $sales_detail['price'];
                }

                $selectedUnitId = $sales_detail['selected_unit_id'] ?? 1;

                $getCrs = ProductUnit::where('id', $selectedUnitId)->first();
                $conversion_rate = 1 / ($getCrs?->conversion_rate ?? 1);
                $real_qty = $sales_detail['quantity'] * $conversion_rate;

                Log::info('conversion rate: ' . $conversion_rate);

                Log::info('unit_symbol: ' . $sales_detail['unit_symbol']);
                // $bundleId = $sales_detail['is_bundle'] == 1 || $sales_detail['is_bundle'] == true ? $sales_detail['id'] : null;

                $sd                         = new SalesDetail;
                $sd->sales_id               = $sale['id'];
                $sd->outlet_id              = $this->outlet_id;
                $sd->user_id                = $this->user_id;
                $sd->product_id             = $sales_detail['id'];
                $sd->product_price_id       = $sales_detail['selected_variant_id'] ?? $this->__getMainProductPriceId($sales_detail['id']);
                $sd->is_bundle              = $sales_detail['is_bundle'] == 1 || $sales_detail['is_bundle'] == true ? true : false;
                $sd->is_item_bundle         = false;
                $sd->parent_sales_detail_id = null;
                $sd->product_name           = $sales_detail['name'];
                $sd->price                  = $sales_detail['price'];
                $sd->hpp                    = $sales_detail['capital_price'];
                $sd->discount               = $sales_detail['discount'] ?? (0 ?? 0);
                $sd->qty                    = $real_qty;
                $sd->unit_id                = $sales_detail['product_unit_id'];
                $sd->unit_symbol            = $sales_detail['unit_symbol'];
                $sd->final_price            = $new_price;
                $sd->subtotal               = $sales_detail['subtotal'];
                $sd->profit                 = ($new_price - $sales_detail['capital_price']) * ($real_qty ?? 1);
                $sd->is_tiered              = $sales_detail['is_tier'] ?? false;
                $sd->save();

                // cek apakah ada product bundel
                if (isset($sales_detail['is_bundle']) && $sales_detail['is_bundle']) {
                    if (count($sales_detail['bundle_items']) > 0) {
                        foreach ($sales_detail['bundle_items'] as $bundle_item) {
                            $product_stock = ProductStock::where([
                                ['product_id', $bundle_item['product_id']],
                                ['outlet_id', $this->outlet_id],
                            ])->first();

                            $product = Product::with('productPrice', 'productUnit')->where([
                                ['id', $bundle_item['product_id']],
                                ['outlet_id', $this->outlet_id],
                            ])->first();

                            $bundle = ProductBundle::where('product_id', $bundle_item['product_id'])->first();

                            // jika stok kosong, berika pesan error untuk semua produk yang tidak ada
                            if (! $product_stock) {
                                return responseAPI(false, 'Stok Item Paket Tidak Tersedia', $product_stock);
                            }

                            // ini kalo ngurangi stok utama
                            if ($sales_detail['is_main_stock'] == 1) {
                                $psh = new ProductStockHistory;
                                $psh->outlet_id = $this->outlet_id;
                                $psh->user_id = $this->user_id;
                                $psh->product_id = $bundle_item['product_id'];
                                $psh->inventory_id = null;
                                $psh->document_number = $sale['sale_code'];
                                $psh->history_date = now();
                                $psh->stock_change = $real_qty * $bundle_item['qty'];
                                $psh->stock_before = $product_stock->stock_current;
                                $psh->stock_after = $product_stock->stock_current - ($real_qty * $bundle_item['qty']);
                                $psh->action_type = 'minus';
                                $psh->desc = $status == 'draft' ? 'Draft Paket / ' . $sale['sale_code'] : 'Penjualan Paket / ' . $sale['sale_code'];
                                $psh->save();

                                $product_stock->stock_current = $product_stock->stock_current - ($bundle_item['qty'] * $real_qty);
                                $product_stock->save();
                            }

                            // input data bundle item ke sales detail
                            $sdb = new SalesDetail;
                            $sdb->sales_id = $sale['id'];
                            $sdb->outlet_id = $this->outlet_id;
                            $sdb->user_id = $this->user_id;
                            $sdb->product_id = $product->id;
                            $sdb->product_price_id = $sales_detail['selected_variant_id'] ?? $this->__getMainProductPriceId($sales_detail['id']);
                            $sdb->is_bundle = false;
                            $sdb->is_item_bundle = true;
                            $sdb->parent_sales_detail_id = $sd->id;
                            $sdb->product_name = $product->name;
                            $sdb->price = optional($product->productPrice->first())->price ?? 0;
                            $sdb->hpp = $product->capital_price;
                            $sdb->discount = $product->discount ?? 0;
                            $sdb->qty = $bundle_item['qty'] * $real_qty;
                            $sdb->unit_id = $product->productUnit->id;
                            $sdb->unit_symbol = $product->productUnit->symbol;
                            $sdb->final_price = 0;
                            $sdb->subtotal = 0;
                            $sdb->profit = 0;
                            $sdb->is_tiered = false;

                            // dd($sdb);

                            $sdb->save();
                        }
                    }
                }

                if (! isset($sales_detail['is_bundle']) || ! $sales_detail['is_bundle'] || ! $sales_detail['is_main_stock']) {
                    // NOTE: nampaknya belum ada handler ini jika stok kosong.
                    // pastikan jangan sampai masuk ke transaksi jika stok kosong. handle dlu dr api untuk product stock
                    $product_stock = ProductStock::where([
                        ['product_id', $sales_detail['id']],
                        ['outlet_id', $this->outlet_id],
                    ])->first();

                    // input ke product_stock_histories
                    $psh = new ProductStockHistory;
                    $psh->outlet_id = $this->outlet_id;
                    $psh->user_id = $this->user_id;
                    $psh->product_id = $sales_detail['id'];
                    $psh->inventory_id = null;
                    $psh->document_number = $sale['sale_code'];
                    $psh->history_date = now();
                    $psh->stock_change = $real_qty;
                    $psh->stock_before = $product_stock->stock_current;
                    $psh->stock_after = $product_stock->stock_current - $real_qty;
                    $psh->action_type = 'minus';
                    $psh->desc = $status == 'draft' ? 'Draft / ' . $sale['sale_code'] : 'Penjualan / '. $sale['sale_code'];
                    $psh->save();

                    // update product_stocks
                    $product_stock->stock_current = $product_stock->stock_current - $real_qty;
                    $product_stock->save();
                }

                // kalkulasi hpp
                $product = Product::find($sales_detail['id']);
                $total_hpp += $product->capital_price * $sales_detail['quantity'];

                // kalkukasi profit
                $total_profit += $sd->profit;
            }

            $success = true;
            $message = 'Transaksi berhasil disimpan';
            $data = [
                'total_profit' => $total_profit,
                'total_hpp' => $total_hpp,
            ];

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            $success = false;
            $message = 'Transaksi gagal';
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

    /**
     * menyimpan ke cashflow table
     *
     * @param  mixed  $sale
     * @param  mixed  $sales_detail
     * @param  mixed  $request
     */
    public function storeToCashflow($sale, $sales_detail, $request): array
    {
        DB::beginTransaction();
        try {
            // input ke cashflow
            $cashflow = new Cashflow;
            $cashflow->outlet_id = $this->outlet_id;
            $cashflow->user_id = $this->user_id;
            $cashflow->code = IdGenerator::generate([
                'table' => 'cashflows',
                'field' => 'code',
                'length' => 11,
                'prefix' => 'CF' . date('ym') . '-',
                'reset_on_prefix_change' => true,
            ]);
            $cashflow->transaction_code = $sale['sale_code'];
            $cashflow->transaction_date = $sale['sale_date'];
            $cashflow->type = 'in';
            $cashflow->amount = $request->final_amount;
            $cashflow->profit = $sales_detail['total_profit'];
            $cashflow->total_hpp = $sales_detail['total_hpp'];
            $cashflow->desc = 'Penjualan';
            $cashflow->save();

            $success = true;
            $message = 'Transaksi berhasil disimpan';
            $data = [
                'cashflow_id' => $cashflow->id,
            ];

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    public function voidNonDraft($request): array
    {
        $this->outlet_id = auth()->user()->outlets[0]?->id;
        $this->user_id = auth()->user()->id;
        // return responseAPI(false, 'Tidak bisa dibatalkan', $request->product_detail['discount_collection']);
        DB::beginTransaction();
        try {
            if ($request->type == 'item') {
                // create new sales
                $sales = new Sales;
                $sales->outlet_id = $this->outlet_id;
                $sales->cashier_machine_id = $request->cashier_machine_id ?? 1;
                $sales->user_id = $this->user_id;
                $sales->payment_method_id = $request->payment_method_id ?? 1;
                $sales->sale_code = saleCode();
                $sales->ref_code = null;
                $sales->customer_id = $request->customer_id;
                $sales->sale_date = now();
                $sales->nominal_amount = $request->product_detail['price'] * $request->product_detail['quantity'];
                $sales->discount_amount = $request->product_detail['discount_collection']['amount'] ?? 0;
                $sales->discount_type = $request->discount_type['discount_collection']['discount_type'] ?? 'nominal';
                $sales->transaction_fees = $request->transaction_fees ?? 0;
                $sales->final_amount = $request->product_detail['new_price'] * $request->product_detail['quantity'];
                $sales->nominal_pay = $request->nominal_pay ?? 0;
                $sales->nominal_change = $request->nominal_change ?? 0;
                $sales->is_retur = false;
                $sales->status = 'void';
                $sales->note = $request->void_message ?? 'Batal Pesan';

                $sales->creator_user_id = $this->user_id;
                $sales->cashier_user_id = $this->user_id;
                $sales->save();

                // input ke product_stock_histories
                // $psh = new ProductStockHistory();
                // $psh->outlet_id = $this->outlet_id;
                // $psh->user_id =   $this->user_id;
                // $psh->product_id = $request->product_id;
                // $psh->inventory_id = null;
                // $psh->document_number = $sales->sale_code;
                // $psh->history_date = now();
                // $psh->stock_change = $request->product_detail['quantity'];
                // $psh->stock_before =  $request->product_detail['stock_current'] + $request->product_detail['quantity'];
                // $psh->stock_after = $request->product_detail['stock_current'];
                // $psh->action_type = 'plus';
                // $psh->desc = 'Void Item Penjualan';

                // $psh->save();

                // update product stock
                // $product_stock = ProductStock::where('product_id', $request->product_id)->first();
                // $product_stock->stock_current += $request->product_detail['quantity'];
                // $product_stock->save();

                // create new sales detail
                $sales_detail = new SalesDetail;
                $sales_detail->sales_id = $sales->id;
                $sales_detail->outlet_id = $this->outlet_id;
                $sales_detail->user_id = $this->user_id;
                $sales_detail->product_id = $request->product_id;
                $sales_detail->product_name = $request->product_detail['name'];
                $sales_detail->price = $request->product_detail['price'];
                $sales_detail->hpp = $request->product_detail['capital_price'];
                $sales_detail->discount = $request->product_detail['discount_collection']['amount'] ?? 0;
                $sales_detail->qty = $request->product_detail['quantity'];
                $sales_detail->final_price = $request->product_detail['new_price'];
                $sales_detail->subtotal = $request->product_detail['new_price'] * $request->product_detail['quantity'];
                $sales_detail->profit = ($request->product_detail['new_price'] - $request->product_detail['capital_price']) * $request->product_detail['quantity'];
                $sales_detail->is_tiered = $request->product_detail['is_tier'] ?? false;
                $sales_detail->status = 'void';

                $sales_detail->save();
            } else {
                $payment_method = PaymentMethod::find((int) $request->payment_method_id);
                // create new sales
                $sales = new Sales;
                $sales->outlet_id = $this->outlet_id;
                $sales->cashier_machine_id = $request->cashier_machine_id ?? 1;
                $sales->user_id = $this->user_id;
                $sales->payment_method_id = $request->payment_method_id ?? 1;
                $sales->sale_code = saleCode();
                $sales->ref_code = null;
                $sales->customer_id = $request->customer_id;
                $sales->sale_date = now();
                $sales->nominal_amount = 0;
                $sales->discount_amount = 0;
                $sales->discount_type = 'nominal';
                $sales->transaction_fees = $payment_method->transaction_fees ?? 0;
                $sales->final_amount = 0;
                $sales->nominal_pay = 0;
                $sales->nominal_change = 0;
                $sales->is_retur = false;
                $sales->status = 'void';
                $sales->note = $request->void_message ?? 'Batal Pesan';

                $sales->creator_user_id = $this->user_id;
                $sales->cashier_user_id = $this->user_id;
                $sales->save();

                $final_amount = 0;
                // foreach product_detail
                foreach ($request->product_detail as $product_detail) {
                    // input ke product_stock_histories
                    // $psh = new ProductStockHistory();
                    // $psh->outlet_id = $this->outlet_id;
                    // $psh->user_id =   $this->user_id;
                    // $psh->product_id = $product_detail['id'];
                    // $psh->inventory_id = null;
                    // $psh->document_number = $sales->sale_code;
                    // $psh->history_date = now();
                    // $psh->stock_change = $product_detail['quantity'];
                    // $psh->stock_before =  $product_detail['stock_current'] + $product_detail['quantity'];
                    // $psh->stock_after = $product_detail['stock_current'];
                    // $psh->action_type = 'plus';
                    // $psh->desc = 'Void Item Penjualan';
                    // $psh->save();

                    // //update product stock
                    // $product_stock = ProductStock::where('product_id', $product_detail['id'])->first();
                    // $product_stock->stock_current += $product_detail['quantity'];
                    // $product_stock->save();

                    // create new sales detail
                    $sales_detail = new SalesDetail;
                    $sales_detail->sales_id = $sales->id;
                    $sales_detail->outlet_id = $this->outlet_id;
                    $sales_detail->user_id = $this->user_id;
                    $sales_detail->product_id = $product_detail['id'];
                    $sales_detail->product_name = $product_detail['name'];
                    $sales_detail->price = $product_detail['price'];
                    $sales_detail->hpp = $product_detail['capital_price'];
                    $sales_detail->discount = $product_detail['discount_collection']['amount'] ?? 0;
                    $sales_detail->qty = $product_detail['quantity'];
                    $sales_detail->final_price = $product_detail['new_price'];
                    $sales_detail->subtotal = $product_detail['new_price'] * $product_detail['quantity'];
                    $sales_detail->profit = ($product_detail['new_price'] - $product_detail['capital_price']) * $product_detail['quantity'];
                    $sales_detail->is_tiered = $product_detail['is_tier'] ?? false;
                    $sales_detail->status = 'void';
                    $sales_detail->save();

                    $final_amount += $sales_detail->subtotal;
                }

                $sales->nominal_amount = $final_amount;
                $sales->final_amount = $final_amount;
                $sales->save();
            }

            $success = true;
            $message = 'Transaksi berhasil dibatalkan';
            $data = null;

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            $success = false;
            $message = 'Transaksi gagal';
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

    private function __getMainProductPriceId($productId)
    {
        $mainPrice = ProductPrice::where('product_id', $productId)
            ->where('type', 'utama')
            ->first();
        return $mainPrice?->id;
    }
}
