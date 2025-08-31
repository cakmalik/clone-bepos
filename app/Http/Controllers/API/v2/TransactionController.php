<?php

namespace App\Http\Controllers\API\v2;

use App\Models\Product;
use App\Models\ProductStockHistory;
use Carbon\Carbon;
use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\SellingPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\StoreCashflowRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Requests\API\{SalesDetailStoreRequest, SalesStoreRequest};
use App\Models\Cashflow;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $productController;

    public function __construct(ProductController $productController)
    {
        $this->productController = $productController;
    }

    public function tes(Request $r)
    {
        SellingPrice::create(['name' => $r->nama]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_id' => 'nullable|exists:sales,id',
            'outlet_id' => 'required|exists:outlets,id',
            'cashier_machine_id' => 'integer|nullable',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'table_id' => 'nullable|exists:tables,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'status' => 'required|in:draft,success',
            'sales_details' => 'array',
            'sales_details.*.product_id' => 'required|exists:products,id',
            'sales_details.*.qty' => 'required|integer|min:0',
            'sales_details.*.price' => 'required|integer|min:0',
        ]);
    
        DB::beginTransaction();
        try {
            $sale = $validated['sales_id'] ? Sales::find($validated['sales_id']) : new Sales;
            $sale->sale_code = $validated['sales_id'] ? $sale->sale_code : saleCode();
            $sale->sale_date = Carbon::now();
            $sale->cashier_machine_id = $validated['cashier_machine_id'] ?? 1;
            $sale->outlet_id = $validated['outlet_id'];
            $sale->user_id = $validated['user_id'];
            $sale->customer_id = $validated['customer_id'];
            $sale->nominal_pay = 0;
            $sale->table_id = $validated['table_id'];
            $sale->payment_method_id = $validated['payment_method_id'] ?? 1;
            $sale->nominal_amount = 0;
            $sale->final_amount = 0;
            $sale->discount_amount = $request->input('discount_amount', 0);
            $sale->discount_type = $request->input('discount_type', 'nominal');
            $sale->nominal_change = 0;
            $sale->creator_user_id = Auth::user()->id;
            $sale->status = 'draft';
            $sale->process_status = 'waiting';
            $sale->save();
    
            $items = [];
            $totalAmount = 0;
    
            if ($validated['sales_id']) {
                foreach ($sale->salesDetails as $salesDetail) {
                    // $this->productController->changeProductStock($salesDetail->product_id, $salesDetail->qty);
                    $salesDetail->delete();
                }
            }
    
            foreach ($validated['sales_details'] as $item) {
                $oldSalesDetail = SalesDetail::where('sales_id', $validated['split_parent_id'] ?? null)->where('product_id', $item['product_id'])->first();
                $product = Product::where('id', $item['product_id'])->with(['prices' => function ($query) use ($validated) {
                    $query->where('type', 'utama');
                }])->first();
    
                if ($oldSalesDetail) {
                    if ($oldSalesDetail->qty < $item['qty']) {
                        // throw error if the previous sales detail quantity is less than the new one
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Kuantitas produk ' . $product->name . ' melebihi batas',
                        ]);
                    } else if ($oldSalesDetail->qty == $item['qty']) {
                        // delete the old sales detail
                        $oldSalesDetail->delete();
                    } else {
                        // update the old qty
                        $oldSalesDetail->qty -= $item['qty'];
    
                        $oldFinalPrice = $oldSalesDetail->qty * $oldSalesDetail->price;
    
                        $oldSalesDetail->final_price = $oldFinalPrice;
                        $oldSalesDetail->subtotal = $oldFinalPrice;
                        $oldSalesDetail->profit = ($oldFinalPrice - $product->capital_price) * $oldSalesDetail->qty;
                        $oldSalesDetail->save();
                    }
                } else {
                    // $this->productController->changeProductStock($item['product_id'], $item['qty']);
                }
                // $price = $product->prices[0]->price;
                $price = $item['price'];
                $final_price = $price * $item['qty'];
                $items[] = [
                    'sales_id' => $sale->id,
                    'outlet_id' => $sale->outlet_id,
                    'user_id' => $sale->user_id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'discount' => 0,
                    'qty' => $item['qty'],
                    'final_price' => $final_price,
                    'subtotal' => $final_price,
                    'profit' => $final_price - ($product->capital_price * $item['qty']),
                ];
                $totalAmount += $final_price;
            }
    
            // Apply discount
            $discountAmount = $sale->discount_amount;
            $discountType = $sale->discount_type;
    
            if ($discountType === 'percentage') {
                $discountValue = $totalAmount * ($discountAmount / 100);
                $sale->final_amount = $totalAmount - $discountValue;
            } else if ($discountType === 'nominal') {
                if ($discountAmount >= $totalAmount) {
                    // Jika diskon nominal lebih besar atau sama dengan harga total, maka set diskon menjadi 0.
                    $sale->final_amount = 0;
                } else {
                    $sale->final_amount = $totalAmount - $discountAmount;
                }
            }
    
            $sale->nominal_amount = $totalAmount;
    
            if ($validated['split_parent_id'] ?? null) {
                $oldSale = Sales::find($validated['split_parent_id']);
                $oldTotalAmount = SalesDetail::where('sales_id', $validated['split_parent_id'])->sum('final_price');
                $oldSale->final_amount = $oldTotalAmount;
                $oldSale->nominal_amount = $oldTotalAmount;
                $oldSale->sales_type = 'split-parent';
                $oldSale->save();
    
                $sale->sales_type = 'split-child';
            } else {
                $sale->sales_type = 'single';
            }
    
            SalesDetail::insert($items);
    
            $sale->save();
    
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'sale_id' => $sale->id,
                    'sale_code' => $sale->sale_code,
                ],
            ],200);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function detailRecentTransaction(Request $request)
    {
        try{
            if ($request->input('outlet_id')) {
                $sale_code = $request->query('sale_code');
                $outlet_id = $request->query('outlet_id');

                $recent_success = Sales::where('sale_code', $sale_code)
                ->where('status', 'draft')
                ->where('outlet_id', $outlet_id)
                ->where('sales_type', '!=', 'join-child')
                ->with(['customer', 'table', 'paymentMethod', 'user', 'outlet', 'salesDetails' => function ($query) {
                    $query->select('sales_details.id', 'sales_id', 'product_name', 'sales_details.product_id', 'price', 'discount', 'qty', 'final_price', 'subtotal', 'profit', 'process_status');
                }])
                ->orderBy('created_at', 'desc')
                ->first();

            // Mapping data produk
            $salesDetails = [];
            foreach ($recent_success->salesDetails as $salesDetail) {
                $product = Product::find($salesDetail->product_id);
                $stock = $product->productStock()->where('outlet_id', $outlet_id)->value('stock_current');
            
                $salesDetails[] = [
                    'product_id' => $salesDetail->product_id,
                    'product_name' => $salesDetail->product_name,
                    'price' => $salesDetail->price,
                    'qty' => $salesDetail->qty,
                    'stock_current' => $stock,
                    'subtotal' => $salesDetail->subtotal,
                ];
            }

            $total_item = count($recent_success->salesDetails);

            $data = [
                'sale_id' => $recent_success->id, 
                'sale_code' => $recent_success->sale_code,
                'outlet_name' => $recent_success->outlet->name,
                'user_name' => $recent_success->user->users_name,
                'payment_method' => $recent_success->paymentMethod->name,
                'journal_number_id' => $recent_success->journal_number_id,
                'customer_name' => $recent_success->customer->name,
                'sale_date' => $recent_success->sale_date,
                'nominal_amount' => (int)str_replace(['Rp', ' ', ','], '', $recent_success->nominal_amount),
                'discount_amount' => $recent_success->discount_amount,
                'discount_type' => $recent_success->discount_type,
                'final_amount' => $recent_success->final_amount,
                'nominal_pay' => $recent_success->nominal_pay,
                'nominal_change' => $recent_success->nominal_change,
                'payment_status' => $recent_success->status,
                'table_name' => $recent_success->table->name ?? null,
                'total_item' => $total_item,
                'sales_details' => $salesDetails,
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data,
            ], 200);

            }
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => null
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function deleteTransaction(Request $request)
    {
        try {
            $sale_id = $request->input('sale_id');
            $outlet_id = $request->input('outlet_id');
    
            // Fetch the Sales data with the specified sale code and status 'draft'
            $sale = Sales::where('id', $sale_id)
                ->where('status', 'draft')
                ->where('outlet_id', $outlet_id)
                ->with('salesDetails') // Eager load the SalesDetails relationship
                ->first();
    
            if (!$sale) {
                return response()->json([
                    'success' => true,
                    'message' => 'transaksi tidak di temukan atau bukan draft.',
                    'data' => null,
                ], 404);
            }
    
            DB::beginTransaction();
            try {
                // Delete SalesDetails and add product quantities back to the stock
                foreach ($sale->salesDetails as $salesDetail) {
                    $product_id = $salesDetail->product_id;
                    $qty = $salesDetail->qty;
    
                    // Add the product quantity back to the original stock
                    // $this->productController->addProductStock($product_id, $qty);
    
                    // Delete the SalesDetail
                    $salesDetail->delete();
                }
    
                // Delete the Sales data itself
                $sale->delete();
    
                DB::commit();
    
                return response()->json([
                    'success' => true,
                    'message' => 'draft berhasil di hapus.',
                    'data' => null,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete the sale.',
                    'data' => null,
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'nominal_pay' => 'required|integer|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sales::where('id', $validated['sale_id'])->whereNotIn('status', ['success', 'cancel'])->with('salesDetails')->first();
            //jika status nya sudah success maka tidak bisa melakukan pembayaran kirim response error sudah dibayar
            if(!$sale){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan atau sudah dibayar',
                    'data' => null
                ], 500);
            }
            $total_amount_product = $sale->salesDetails->sum('final_price');
            //perhitungan discount
            if($sale->discount_type == 'nominal'){
                // $total_amount = $total_amount_product - $sale->discount_amount;
                if($sale->discount_amount >= $total_amount_product){
                    $total_amount = 0;
                }else{
                    $total_amount = $total_amount_product - $sale->discount_amount;
                }
            }else{
                $total_amount = $total_amount_product - ($total_amount_product * ($sale->discount_amount / 100));
            }
            $nominal_change = $validated['nominal_pay'] - $total_amount;
            if ($nominal_change < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak cukup',
                    'data' => null
                ], 400);
            }

            $sale->nominal_change = $nominal_change;
            $sale->nominal_pay = $validated['nominal_pay'];
            $sale->payment_method_id = $validated['payment_method_id'];
            $sale->status = 'success';
            $sale->process_status = 'done';
            $sale->cashier_user_id = Auth::user()->id;
            $sale->save();

            $this->cashflowStore($sale->id);
            $this->stockHistoryStore($sale->id);
            // kurangi stock
            foreach ($sale->salesDetails as $salesDetail) {
                $this->productController->changeProductStock($salesDetail->product_id, $salesDetail->qty);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil',
                'data' => [
                    'sale_id' => $sale->id,
                    'sale_code' => $sale->sale_code,
                    'nominal_change' => $nominal_change,
                ]
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan pembayaran',
                'data' => null
            ], 500);
        }
    }

    public function joinBill(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'cashier_machine_id' => 'integer|nullable',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'nominal_pay' => 'nullable|integer|min:0',
            'table_id' => 'nullable|exists:tables,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'merge_sales_id' => 'array',
            'merge_sales_id.*' => 'integer|exists:sales,id',
            'selling_price_id' => 'required|exists:selling_prices,id',
            //            'status' => 'required|in:draft,success',
        ]);

        DB::beginTransaction();
        try {
            $sale = new Sales;
            $sale->sale_code = saleCode();
            $sale->sale_date = Carbon::now();
            $sale->cashier_machine_id = $validated['cashier_machine_id'] ?? 1;
            $sale->outlet_id = $validated['outlet_id'];
            $sale->user_id = $validated['user_id'];
            $sale->customer_id = $validated['customer_id'];
            $sale->nominal_pay = $validated['nominal_pay'];
            $sale->table_id = $validated['table_id'];
            $sale->payment_method_id = $validated['payment_method_id'];
            $sale->creator_user_id = Auth::user()->id;
            $sale->nominal_amount = 0;
            $sale->final_amount = 0;
            $sale->discount_amount = 0;
            $sale->nominal_change = 0;
            //            $sale->status = $validated['status'];
            $sale->status = 'success';
            $sale->sales_type = 'join-parent';
            $sale->save();

            $items = [];
            $totalAmount = 0;

            Sales::whereIn('id', $validated['merge_sales_id'])->update([
                'sales_type' => 'join-child',
            ]);

            $oldSalesDetails = SalesDetail::whereIn('sales_id', $validated['merge_sales_id'])->get()->groupBy('product_id');
            foreach ($oldSalesDetails as $product_id => $salesDetails) {
                $product = Product::where('id', $product_id)->with(['prices' => function ($query) use ($validated) {
                    $query->where('selling_price_id', $validated['selling_price_id']);
                }])->first();

                $price = $product->prices[0]->price;
                $qty = $salesDetails->sum('qty');
                $final_price = $price * $qty;
                $items[] = [
                    'sales_id' => $sale->id,
                    'outlet_id' => $sale->outlet_id,
                    'user_id' => $sale->user_id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'discount' => 0,
                    'qty' => $qty,
                    'final_price' => $final_price,
                    'subtotal' => $final_price,
                    'profit' => $final_price - ($product->capital_price * $qty),
                ];
                $totalAmount += $final_price;
            }

            $sale->nominal_amount = $totalAmount;
            $sale->final_amount = $totalAmount;

            SalesDetail::insert($items);

            if ($sale->status == 'success') {
                $sale->nominal_change = $sale->nominal_pay - $totalAmount;
                if ($sale->nominal_change < 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Kembalian kurang dari 0',
                    ]);
                }
                $this->cashflowStore($sale->id);
                $this->stockHistoryStore($sale->id);
            }

            $sale->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal disimpan',
            ], 500);
        }
    }

    private function cashflowStore($sale_id)
    {
        $data = Sales::find($sale_id);
        $profit = $data->salesDetails->sum('profit');
        $data_cashflow = [
            "outlet_id" => $data->outlet_id,
            "user_id" => $data->user_id,
            "code" => autoCode('cashflows', 'code', 'CSFL-' . date('Y-m-'), 7),
            "transaction_code" => $data->sale_code,
            "type" => "in",
            "amount" => $data->final_amount,
            "profit" => $profit,
            "desc" => "Transaksi penjualan",
            "cashflow_close_id" => null
        ];
        return Cashflow::create($data_cashflow);
    }

    // public function recentTransaction(Request $request)
    // {
    //     try {
    //         if ($request->input('outlet_id')) {
    //             $search = $request->query('search');
    //             $recent_draft = Sales::where('status', 'draft')
    //                 ->where('outlet_id', '=', $request->outlet_id)
    //                 ->when($request->input('date'), function ($query) use ($request) {
    //                     return $query->whereDate('sales.created_at', $request->input('date'));
    //                 }, function ($query) {
    //                     return $query->whereDate('sales.created_at', Carbon::today());
    //                 })
    //                 ->whereNot('sales_type', 'join-child')
    //                 ->when($search, function ($query) use ($search) {
    //                     return $query->leftJoin('customers', 'customers.id', 'sales.customer_id')->where('sales.sale_code', 'like', '%' . $search . '%')->orWhere('customers.name', 'like', '%' . $search . '%');
    //                 })
    //                 ->with('customer', 'table', 'salesDetails', 'paymentMethod')->orderBy('sales.created_at', 'desc')->get();
    //             $recent_success = Sales::where('status', 'success')
    //                 ->where('outlet_id', '=', $request->outlet_id)
    //                 ->when($request->input('date'), function ($query) use ($request) {
    //                     return  $query->whereDate('sales.created_at', $request->input('date'));
    //                 }, function ($query) {
    //                     return $query->whereDate('sales.created_at', Carbon::today());
    //                 })
    //                 ->whereNot('sales_type', 'join-child')
    //                 ->when($search, function ($query) use ($search) {
    //                     return $query->leftJoin('customers', 'customers.id', 'sales.customer_id')->where('sales.sale_code', 'like', '%' . $search . '%')->orWhere('customers.name', 'like', '%' . $search . '%');
    //                 })
    //                 ->with('customer', 'table', 'salesDetails', 'paymentMethod')->orderBy('sales.created_at', 'desc')->get();
    //             $data = [
    //                 'draft' => $recent_draft,
    //                 'success' => $recent_success
    //             ];
    //             return response()->json([
    //                 'status' => 'success',
    //                 'data' => $data
    //             ], 200);
    //         }
    //         return response()->json([
    //             'status' => 'success',
    //             'data' => []
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function recentTransaction(Request $request)
    {
        try {
            if ($request->input('outlet_id')) {
                $search = $request->query('search');
                $recent_success = Sales::Where('status', 'draft')
                    ->where('outlet_id', '=', $request->outlet_id)
                    ->when($request->input('date'), function ($query) use ($request) {
                        return  $query->whereDate('sales.created_at', $request->input('date'));
                    }, function ($query) {
                        return $query->whereDate('sales.created_at', Carbon::today());
                    })
                    ->whereNot('sales_type', 'join-child')
                    ->when($search, function ($query) use ($search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('sales.sale_code', 'like', '%' . $search . '%')
                                  ->orWhereHas('customer', function ($query) use ($search) {
                                      $query->where('name', 'like', '%' . $search . '%');
                                  })
                                  ->orWhereHas('table', function ($query) use ($search) {
                                      $query->where('name', 'like', '%' . $search . '%');
                                  });
                        });
                    })
                    ->with(['customer', 'table', 'salesDetails' => function ($query) {
                        $query->select('id', 'sales_id', 'product_name', 'price', 'discount', 'qty', 'final_price', 'subtotal', 'profit', 'process_status');
                    }])
                    ->orderBy('sales.created_at', 'desc')
                    ->get();

                    //jika data nya kosong maka tampilkan data kosong
                    if(count($recent_success) == 0){
                        $responseDataKosong = [
                            'formatted_date' => date('d F Y', strtotime($request->query('search'))),
                            'total_orders' => 0,
                            'orders' => null,
                        ];
                        return response()->json([
                            'success' => true,
                            'message' => 'data kosong / tidak ditemukan',
                            'data' => $responseDataKosong
                        ],200);
                    }
                    
                $formattedDate = date('d F Y', strtotime($recent_success[0]['sale_date']));
                $totalOrders = count($recent_success);
                $mappedData = [];

                foreach ($recent_success as $order) {
                    $orderNumber = $order['sale_code'];

                    $salesDetails = $order->salesDetails;
                    $orderedItems = $salesDetails->pluck('product_name')->implode(', ');

                    $orderDateTime = date('d-m-Y H:i', strtotime($order['sale_date']));

                    $totalAmount = 'Rp ' . number_format($order['final_amount'], 0, ',', '.');

                    $table = $order->table ? $order->table['name'] : 'N/A';

                    $status = $order->status;

                    $mappedData[] = [
                        'sale_id' => $order['id'], // tambahan untuk detail
                        'order_number' => $orderNumber,
                        'ordered_items' => $orderedItems,
                        'order_datetime' => $orderDateTime,
                        'total_amount' => $totalAmount,
                        'meja' => $table,
                        'status' => $status,
                    ];
                }

                $responseData = [
                    'formatted_date' => $formattedDate,
                    'total_orders' => $totalOrders,
                    'orders' => $mappedData,
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'success',
                    'data' => $responseData,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => null
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function detailTransaction(Request $request)
    {
        try{
            if ($request->input('outlet_id')) {
                $sale_code = $request->query('sale_code');
                $outlet_id = $request->query('outlet_id');

                $recent_success = Sales::where('sale_code', $sale_code)
                    ->where('outlet_id', $outlet_id)
                    ->where('sales_type', '!=', 'join-child')
                    ->with(['customer', 'table', 'paymentMethod', 'user', 'outlet', 'salesDetails' => function ($query) {
                        $query->select('id', 'sales_id', 'product_name', 'price', 'discount', 'qty', 'final_price', 'subtotal', 'profit', 'process_status');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->first();

            // Mapping data produk
            $salesDetails = [];
            foreach ($recent_success->salesDetails as $salesDetail) {
                $salesDetails[] = [
                    'product_name' => $salesDetail->product_name,
                    'price' => $salesDetail->price,
                    'qty' => $salesDetail->qty,
                    // 'final_price' => $salesDetail->final_price,
                    'subtotal' => $salesDetail->subtotal,
                ];
            }

            $total_item = count($recent_success->salesDetails);

            $data = [
                'sale_code' => $recent_success->sale_code,
                'outlet_name' => $recent_success->outlet->name,
                'user_name' => $recent_success->user->users_name,
                'payment_method' => $recent_success->paymentMethod->name,
                'journal_number_id' => $recent_success->journal_number_id,
                'customer_name' => $recent_success->customer->name,
                'customer_phone' => $recent_success->customer->phone ?? null,
                'sale_date' => $recent_success->sale_date,
                'nominal_amount' => (int)str_replace(['Rp', ' ', ','], '', $recent_success->nominal_amount),
                'discount_amount' => $recent_success->discount_amount,
                'discount_type' => $recent_success->discount_type,
                'final_amount' => $recent_success->final_amount,
                'nominal_pay' => $recent_success->nominal_pay,
                'nominal_change' => $recent_success->nominal_change,
                'payment_status' => $recent_success->status,
                'table_name' => $recent_success->table->name ?? null,
                'total_item' => $total_item,
                'sales_details' => $salesDetails,
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data,
            ], 200);

            }
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => null
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function historyTransaction(Request $request)
    {
        try {
            if ($request->input('outlet_id')) {
                $search = $request->query('search');
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');

                $query = Sales::where('outlet_id', '=', $request->outlet_id)
                    ->where('status', 'success')
                    ->whereNot('sales_type', 'join-child');

                if ($startDate && $endDate) {
                    $query->whereDate('sales.created_at', '>=', $startDate)
                          ->whereDate('sales.created_at', '<=', $endDate);
                }

                if ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('sales.sale_code', 'like', '%' . $search . '%')
                              ->orWhereHas('customer', function ($query) use ($search) {
                                  $query->where('name', 'like', '%' . $search . '%');
                              })
                              ->orWhereHas('table', function ($query) use ($search) {
                                  $query->where('name', 'like', '%' . $search . '%');
                              });
                    });
                }

                $recent_success = $query->with(['customer', 'table', 'salesDetails' => function ($query) {
                        $query->select('id', 'sales_id', 'product_name', 'price', 'discount', 'qty', 'final_price', 'subtotal', 'profit', 'process_status');
                    }])
                    ->orderBy('sales.created_at', 'desc')
                    ->get();

                $mappedData = [];

                foreach ($recent_success as $order) {
                    $orderNumber = $order['sale_code'];

                    $salesDetails = $order->salesDetails;
                    $orderedItems = $salesDetails->pluck('product_name')->implode(', ');

                    $orderDateTime = date('d-m-Y H:i', strtotime($order['sale_date']));

                    $totalAmount = 'Rp ' . number_format($order['final_amount'], 0, ',', '.');

                    $table = $order->table ? $order->table['name'] : 'N/A';

                    $paymentMethod = $order->paymentMethod ? $order->paymentMethod['name'] : 'N/A';

                    $payment_status = $order->status;

                    $mappedData[] = [
                        'order_number' => $orderNumber,
                        'ordered_items' => $orderedItems,
                        'order_datetime' => $orderDateTime,
                        'total_amount' => $totalAmount,
                        'meja' => $table,
                        'payment_method' => $paymentMethod,
                        'payment_status' => $payment_status,
                    ];
                }

                return response()->json([
                    'success' => true,
                    'message' => 'success',
                    'data' => $mappedData,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'data kosong',
                'data' => null
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    public function edit(Sales $sales)
    {
        $sale = Sales::where('id', $sales->id)->with('salesDetails')->first();
        return response()->json([
            'status' => 'success',
            'data' => $sale,
        ], 200);
    }

    public function destroy(Sales $sales)
    {
        foreach ($sales->salesDetails as $salesDetail) {
            $this->productController->changeProductStock($salesDetail->product_id, -$salesDetail->qty);
        }
        $sales->delete();
        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public function show(Sales $sale)
    {
        $sal = $sale->load('salesDetails', 'customer', 'outlet', 'paymentMethod', 'table')->first();
        return response()->json([
            'status' => 'success',
            'data' => $sal
        ], 200);
    }

    public function stockHistoryStore($sale_id)
    {
        $sale = Sales::where('id', $sale_id)->with('salesDetails')->first();
        foreach ($sale->salesDetails as $salesDetail) {
            $product = Product::where('id', $salesDetail->product_id)->with('productStock')->first();
            $productStock = $product->productStock[0];
            ProductStockHistory::create([
                'document_number' => $sale->sale_code,
                'history_date' => $sale->sale_date,
                'outlet_id' => $sale->outlet_id,
                'user_id' => $sale->user_id,
                'product_id' => $salesDetail->product_id,
                'stock_change' => $salesDetail->qty,
                'stock_before' => $productStock->stock_current + $salesDetail->qty,
                'stock_after' => $productStock->stock_current,
                'desc' => 'penjualan ' . $product->name . ' sebanyak ' . $salesDetail->qty . ' unit',
                'action_type' => 'minus',
            ]);
        }
    }

    public function paymentMethod()
    {
        try {
            $data = DB::table('payment_methods')->get();
            $data = collect($data)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            });
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => null
            ], 500);
        }
    }
}
