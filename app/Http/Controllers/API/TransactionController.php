<?php

namespace App\Http\Controllers\API;

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
        // return response()->json(['sales_id' => $request->sales_id]);
        $validated = $request->validate([
            'sales_id' => 'nullable|exists:sales,id',
            'outlet_id' => 'required|exists:outlets,id',
            'cashier_machine_id' => 'integer|nullable',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            //            'nominal_pay' => 'nullable|integer|min:0',
            'table_id' => 'nullable|exists:tables,id',
            //            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'split_parent_id' => 'nullable|exists:sales,id',
            'sales_details' => 'array',
            'sales_details.*.product_id' => 'required|exists:products,id',
            'sales_details.*.qty' => 'required|integer|min:0',
            'selling_price_id' => 'required|exists:selling_prices,id',
            'status' => 'required|in:draft,success',
        ]);

        DB::beginTransaction();
        try {
            $sale = $validated['sales_id'] ? Sales::find((int)$validated['sales_id']) : new Sales;
            $sale->sale_code = saleCode();
            $sale->sale_date = Carbon::now();
            $sale->cashier_machine_id = $validated['cashier_machine_id'] ?? 1;
            $sale->outlet_id = $validated['outlet_id'];
            $sale->user_id = $validated['user_id'];
            $sale->customer_id = $validated['customer_id'];
            $sale->nominal_pay = 0;
            $sale->table_id = $validated['table_id'];
            $sale->payment_method_id = 1;
            $sale->nominal_amount = 0;
            $sale->final_amount = 0;
            $sale->discount_amount = 0;
            $sale->nominal_change = 0;
            $sale->creator_user_id = Auth::user()->id;
            //            $sale->status = $validated['status'];
            $sale->status = 'success';
            $sale->save();

            $items = [];
            $totalAmount = 0;

            if ($validated['sales_id']) {
                foreach ($sale->salesDetails as $salesDetail) {
                    $this->productController->changeProductStock($salesDetail->product_id, -$salesDetail->qty);
                    $salesDetail->delete();
                }
            }

            foreach ($validated['sales_details'] as $item) {
                $oldSalesDetail = SalesDetail::where('sales_id', $validated['split_parent_id'] ?? null)->where('product_id', $item['product_id'])->first();
                $product = Product::where('id', $item['product_id'])->with(['prices' => function ($query) use ($validated) {
                    $query->where('selling_price_id', $validated['selling_price_id']);
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
                    $this->productController->changeProductStock($item['product_id'], $item['qty']);
                }
                $price = $product->prices[0]->price;
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

            $sale->nominal_amount = $totalAmount;
            $sale->final_amount = $totalAmount;

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

            //            if ($sale->status == 'success') {
            //                $sale->nominal_change = $sale->nominal_pay - $totalAmount;
            //                if ($sale->nominal_change < 0) {
            //                    DB::rollBack();
            //                    return response()->json([
            //                        'status' => 'error',
            //                        'message' => 'Kembalian kurang dari 0',
            //                    ]);
            //                }
            //                $this->cashflowStore($sale->id);
            //                $this->stockHistoryStore($sale->id);
            //            }

            $sale->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'sale_id' => $sale->id,
                ],
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal disimpan',
                'error' => $e->getMessage() . ' ' . $e->getLine()
            ]);
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
            $sale = Sales::where('id', $validated['sale_id'])->with('salesDetails')->first();
            // return response()->json([
            //     'data' => $sale
            // ], 400);
            $total_amount = $sale->salesDetails->sum('final_price');
            $nominal_change = $validated['nominal_pay'] - $total_amount;
            if ($nominal_change < 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pembayaran tidak cukup',
                ], 400);
            } else {
                $sale->nominal_change = $nominal_change;
                $sale->nominal_pay = $validated['nominal_pay'];
                $sale->payment_method_id = $validated['payment_method_id'];
                $sale->status = 'success';
                $sale->cashier_user_id = Auth::user()->id;
                $sale->save();

                $this->cashflowStore($sale->id);
                $this->stockHistoryStore($sale->id);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Pembayaran berhasil',
                    'nominal_change' => $nominal_change,
                    'sale_id' => $sale->id,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal dibayar',
                'error' => $e->getMessage() . ' ' . $e->getLine()
            ]);
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
            ]);
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

    // private function _isMatchNominal($final_amount, $sales_detail)
    // {
    //     $subtotal = 0;
    //     foreach ($sales_detail as $i) {
    //         $subtotal += $i['subtotal'];
    //     }
    //     return $subtotal == $final_amount;
    // }

    public function recentTransaction(Request $request)
    {
        try {
            $user = Auth::user();
            $outlet_id = $user->outlets?->first()->id;
            if ($request->input('outlet_id') || $outlet_id == null) {
                $search = $request->query('search');

                $recent_draft = Sales::where('status', 'draft')
                    ->where('outlet_id', '=', $request->outlet_id)
                    ->where('user_id', '=', $user->id)
                    ->when($request->input('date'), function ($query) use ($request) {
                        return $query->whereDate('created_at', $request->input('date'));
                    }, function ($query) {
                        return $query->whereDate('created_at', Carbon::today());
                    })
                    ->whereNot('sales_type', 'join-child')
                    ->when($search, function ($query) use ($search) {
                        return $query->leftJoin('customers', 'customers.id', 'sales.customer_id')->where('sale_code', 'like', '%' . $search . '%')->orWhere('customers.name', 'like', '%' . $search . '%');
                    })
                    ->with('customer', 'table', 'salesDetails', 'paymentMethod')->orderBy('sales.created_at', 'desc')->get();

                $recent_success = Sales::where('status', 'success')
                    ->where('outlet_id', '=', $request->outlet_id)
                    ->where('user_id', '=', $user->id)
                    ->when($request->input('date'), function ($query) use ($request) {
                        return  $query->whereDate('created_at', $request->input('date'));
                    }, function ($query) {
                        return $query->whereDate('created_at', Carbon::today());
                    })
                    ->whereNot('sales_type', 'join-child')
                    ->when($search, function ($query) use ($search) {
                        return $query->leftJoin('customers', 'customers.id', 'sales.customer_id')->where('sale_code', 'like', '%' . $search . '%')->orWhere('customers.name', 'like', '%' . $search . '%');
                    })
                    ->with('customer', 'table', 'salesDetails', 'paymentMethod')->orderBy('sales.created_at', 'desc')->get();
                $data = [
                    'draft' => $recent_draft,
                    'success' => $recent_success
                ];
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 200);
            }
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal diambil',
                'error' => $e->getMessage() . ' ' . $e->getLine()
            ]);
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

    public function show($id)
    {
        $id = (int)$id;
        $sal = Sales::where('id', $id)->with('salesDetails', 'customer', 'outlet', 'paymentMethod', 'table')->first();
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
}
