<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Cashflow;
use App\Models\UserOutlet;
use App\Models\SalesDetail;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\CashflowClose;
use App\Models\ProductBundle;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ReturSalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $retur = Sales::with('user', 'outlet')
                ->where([['is_retur', true], ['status', $request->status]])
                ->where(function ($query) use ($request) {
                    if ($request->start_date != '' && $request->end_date != '') {
                        $query->where([
                            ['sale_date', '>=', startOfDay($request->start_date)],
                            ['sale_date', '<=', endOfDay($request->end_date)]
                        ]);
                    }
                })
                ->when(auth()->user()->role->role_name !== 'SUPERADMIN', function ($query) {
                    $query->whereIn('outlet_id', getUserOutlet());
                })
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($row) {
                    $row->sale_date = Carbon::parse($row->sale_date)->format('d F Y H:i');
                    return $row;
                });

            return DataTables::of($retur)
                ->addIndexColumn()
                ->addColumn('sale_code', function ($row) {
                    $code_link = '<a href="retur-sales/' . $row->sale_code . '"  data-original-title="Show" >' . $row->sale_code . '</a>';
                    return $code_link;
                })
                ->addColumn('ref_code', function ($row) {
                    $code_link = '<a href="sales/' . $row->ref_code . '"  data-original-title="Show" >' . $row->ref_code . '</a>';
                    return $code_link;
                })
                ->addColumn('total_retur', function ($row) {
                    $total_retur = $row->nominal_amount;
                    return 'Rp ' . number_format($total_retur, 0, ',', '.');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'draft') {
                        $status = '<span class="badge badge-sm bg-orange-lt">DRAFT</span>';
                    } elseif ($row->status == 'success') {
                        $status = '<span class="badge badge-sm bg-green-lt">SUKSES</span>';
                    }

                    return $status;
                })
                ->addColumn('detail', function ($row) {
                    if ($row->status == 'draft') {
                        $detail = '<a href="retur-sales/' . $row->id . '/edit" class="btn btn-white btn-sm py-2 px-3">
                        <li class="fas fa-edit"></li></a>';
                        return $detail;
                    } else {
                        $detail = '<button class="btn btn-outline-primary btn-sm" id="btn-detail" data-id="' . $row->id . '" data-original-title="Show"><i class="ti ti-list"></i> Detail</button>';
                    }

                    return $detail;
                })
                ->rawColumns(['status', 'sale_code', 'ref_code', 'total_retur', 'detail'])
                ->make(true);
        }
        return view('pages.sales.retur_sales.index', ['title' => 'Retur Penjualan']);
    }

    public function create()
    {
        $query = DB::table('sales')->where('is_retur', true)->select(DB::raw('MAX(RIGHT(sale_code,4)) as codes'));
        $cd = '';

        if ($query->count() > 0) {
            foreach ($query->get() as $c) {
                $tmp = ((int) $c->codes) + 1;
                $cd = sprintf('%04s', $tmp);
            }
        } else {
            $cd = '00001';
        }

        $employee = UserOutlet::with('user', 'outlet')
            ->where('outlet_id', getOutletActive()->id)
            ->first();

        $sales = Sales::query()
            ->with('salesDetails', 'customer', 'user')
            ->where([
                ['outlet_id', $employee->outlet->id],
                ['is_retur', false],
                ['process_status', 'done'],
                ['final_amount', '>', 0],
                ['ref_code', null]
            ])
            ->orderBy('sale_date', 'desc')
            ->limit(1000)
            ->get();

        return view('pages.sales.retur_sales.create', [
            'code' => $cd,
            'employee' => $employee,
            'sales' => $sales,
        ]);
    }

    public function store(Request $request)
    {
        $sales_detail_ids = $request->sl_id;
        DB::beginTransaction();

        if (Sales::where('sale_code', $request->code)->exists()) {
            return redirect()->back()->withWarning('Kode Retur Penjualan Sudah Ada!');
        }

        try {
            $sales = Sales::find($request->sales_id);
            $subtotal = SalesDetail::whereIn('id', $sales_detail_ids)->sum('subtotal');

            $returStatus = $request->action === 'finish' ? 'success' : 'draft';

            // Buat transaksi retur
            $sl = new Sales();
            $sl->outlet_id = $sales->outlet_id;
            $sl->cashier_machine_id = $sales->cashier_machine_id;
            $sl->user_id = Auth()->user()->id;
            $sl->payment_method_id = $sales->payment_method_id;
            $sl->customer_id = $sales->customer_id;
            $sl->sale_code = $request->code;
            $sl->ref_code = $sales->sale_code;
            $sl->sale_date = Carbon::now();
            // $sl->unit_id = $sales->unit_id;
            // $sl->unit_symbol = $sales->unit_symbol;
            $sl->nominal_amount = 0;
            $sl->discount_amount = $sales->discount_amount;
            $sl->final_amount = 0;
            $sl->nominal_pay = 0;
            $sl->nominal_change = 0;
            $sl->is_retur = true;
            $sl->status = $returStatus;
            $sl->process_status = 'done';
            $sl->sales_parent_id = $sales->id;
            $sl->sales_type = $sales->sales_type;
            $sl->table_id = $sales->table_id;
            $sl->creator_user_id = $sales->creator_user_id;
            $sl->cashier_user_id = $sales->cashier_user_id;
            $sl->save();

            $saleDetails = SalesDetail::whereIn('id', $sales_detail_ids)->get();

            foreach ($saleDetails as $sd) {
                $qty = $sd->id . '_retur_qty_sales';

                SalesDetail::create([
                    'sales_id' => $sl->id,
                    'outlet_id' => $sales->outlet_id,
                    'user_id' => Auth()->user()->id,
                    'product_id' => $sd->product_id,
                    'product_name' => $sd->product_name,
                    'is_bundle' => $sd->is_bundle,
                    'price' => $sd->price,
                    'discount' => $sd->discount,
                    'qty' => $request->$qty,
                    'unit_id' => $sd->unit_id,
                    'unit_symbol' => $sd->unit_symbol,
                    'final_price' => $sd->final_price,
                    'subtotal' => (int) $request->$qty * (int) $sd->final_price,
                    'profit' => 0,
                    'process_status' => 'done',
                    'handler_user_id' => $sd->handler_user_id,
                ]);

                $sales_detail = SalesDetail::where('id', $sd->id)->first();
                $sales_detail->status = 'retur';
                $sales_detail->is_retur = true;
                $sales_detail->qty_retur = $request->$qty;
                $sales_detail->save();

                // Tambahkan item bundle jika produk utama adalah bundle
                if ($sd->is_bundle) {
                    $bundleItems = SalesDetail::where([
                        ['sales_id', '=', $sales->id],
                        ['is_item_bundle', '=', true],
                        ['parent_sales_detail_id', '=', $sd->id]
                    ])->get();

                    foreach ($bundleItems as $item) {
                        SalesDetail::create([
                            'sales_id' => $sl->id,
                            'outlet_id' => $sales->outlet_id,
                            'user_id' => Auth()->user()->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'is_item_bundle' => true,
                            'parent_sales_detail_id' => $sales_detail->id,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'qty' => $request->$qty * ($item->qty / $sd->qty),
                            'unit_id' => $item->unit_id,
                            'unit_symbol' => $item->unit_symbol,
                            'final_price' => $item->final_price,
                            'subtotal' => (int) $request->$qty * (int) $item->final_price,
                            'profit' => 0,
                            'process_status' => 'done',
                            'handler_user_id' => $item->handler_user_id,
                        ]);

                        $sales_detail_bundle = SalesDetail::where('id', $item->id)->first();
                        $sales_detail_bundle->status = 'retur';
                        $sales_detail_bundle->is_retur = true;
                        $sales_detail_bundle->qty_retur = $request->$qty * ($item->qty / $sd->qty);
                        $sales_detail_bundle->save();
                    }
                }
            }

            $subtotal = SalesDetail::where('sales_id', $sl->id)->sum('subtotal');
            Sales::where('id', $request->sales_id)->update([
                'ref_code' => $sl->sale_code,
                'nominal_amount' => $subtotal,
                'final_amount' => $subtotal - $sales->discount_amount,
                'nominal_pay' => $subtotal - $sales->discount_amount,
            ]);

            if ($request->action === 'finish') {
                // Lakukan sebagian logika dari finish()
                $sales_retur = Sales::where('id', $sl->id)->with('salesDetails')->first();
                $sales_ori = Sales::where('id', $sales_retur->sales_parent_id)->first();

                $sales_retur->nominal_amount = $sales_retur->salesDetails->sum('subtotal');
                $sales_retur->final_amount = $sales_retur->nominal_amount;
                $sales_retur->status = 'success';
                $sales_retur->save();

                // Hitung ulang subtotal dan profit pada sales ori
                $total_nominal_sales_ori = 0;
                foreach ($sales_ori->salesDetails as $sd) {
                    $sd->subtotal = $sd->qty * $sd->final_price;
                    $sd->profit = ($sd->is_item_bundle ? 0 : ($sd->final_price - Product::find($sd->product_id)->capital_price) * $sd->qty);
                    $sd->save();

                    $total_nominal_sales_ori += $sd->subtotal;
                }

                $sales_ori->nominal_amount = $total_nominal_sales_ori;
                $sales_ori->final_amount = $total_nominal_sales_ori - $sales_ori->discount_amount;
                $sales_ori->nominal_pay = $sales_ori->final_amount;
                $sales_ori->status = 'success';
                $sales_ori->save();

                // Tambahkan stok dan histori
                foreach ($sales_retur->salesDetails as $sd) {
                    $product_stock = ProductStock::where('product_id', $sd->product_id)
                        ->where('outlet_id', $sales_retur->outlet_id)
                        ->first();

                    if ($product_stock) {
                        $stock_before = $product_stock->stock_current;
                        $new_stock = $stock_before + $sd->qty;
                        $product_stock->update(['stock_current' => $new_stock]);

                        ProductStockHistory::create([
                            'document_number' => $sales_retur->sale_code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'PLUS',
                            'product_id' => $product_stock->product_id,
                            'outlet_id' => $product_stock->outlet_id,
                            'stock_change' => $sd->qty,
                            'stock_before' => $stock_before,
                            'stock_after' => $new_stock,
                            'desc' => 'Retur: ' . $sales_retur->sale_code . ' melakukan PENAMBAHAN QTY',
                            'user_id' => getUserIdLogin(),
                        ]);
                    }
                }

                // Buat cashflow
                Cashflow::create([
                    'outlet_id' => $sales_retur->outlet_id,
                    'user_id' => $sales_ori->user_id,
                    'code' => IdGenerator::generate([
                        'table' => 'cashflows',
                        'field' => 'code',
                        'length' => 11,
                        'prefix' => 'CF' . date('ym') . '-',
                        'reset_on_prefix_change' => true,
                    ]),
                    'transaction_code' => $sales_retur->sale_code,
                    'transaction_date' => Carbon::now(),
                    'type' => 'out',
                    'amount' => $sales_retur->final_amount,
                    'total_hpp' => 0,
                    'profit' => null,
                    'desc' => 'Retur',
                ]);
            }

            DB::commit();
            return redirect($request->action === 'draft'
                ? '/retur-sales/' . $sl->id . '/edit'
                : '/retur-sales/' . $sl->sale_code
            )->withSuccess($request->action === 'draft' ? 'Disimpan sebagai Draf!' : 'Retur selesai diproses!');

        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal disimpan!');
        }
    }


    public function edit($id)
    {
        $sales_retur = Sales::where('id', $id)->with('outlet', 'user', 'salesDetails.product')->first();
        $sales_ori = Sales::where('id', $sales_retur->sales_parent_id)->first();

        return view('pages.sales.retur_sales.edit', ['sales' => $sales_retur, 'sales_ori' => $sales_ori]);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        $sales_retur = Sales::where('id', $request->id)->first();
        $sales_ori = Sales::where('id', $sales_retur->sales_parent_id)->first();

        try {
            // sales detail retur
            $saleDetailretur = SalesDetail::where('sales_id', $request->id)
                ->with('product')
                ->get();

            foreach ($saleDetailretur as $sd) {
                $product_id = $sd->id . '_product_id';
                $qty = $sd->id . '_retur_qty';
                $subtotal = $sd->id . '_subtotal';

                $old_qty = $sd->qty;
                $new_qty = (int) $request->$qty;
                $diff_qty = abs($new_qty - $old_qty);

                if ($new_qty == $old_qty) {
                    // jika qty baru sama dengan qty lama
                } elseif ($new_qty < $old_qty) {
                    // jika qty baru kurang dari qty lama
                    SalesDetail::where('id', $sd->id)->update([
                        'qty' => $new_qty,
                        'subtotal' => $request->$subtotal,
                    ]);

                    $sales_detail_ori = SalesDetail::where('product_id', $request->$product_id)
                        ->where('sales_id', $sales_ori->id)
                        ->first();

                    $sales_detail_ori->qty += $diff_qty;
                    $sales_detail_ori->qty_retur -= $diff_qty;
                    $sales_detail_ori->subtotal = ($sales_detail_ori->qty + $diff_qty) * $sales_detail_ori->final_price;
                    $sales_detail_ori->save();
                } else {
                    // jika qty baru lebih dari qty lama
                    SalesDetail::where('id', $sd->id)->update([
                        'qty' => $new_qty,
                        'subtotal' => $request->$subtotal,
                    ]);

                    $sales_detail_ori = SalesDetail::where('product_id', $request->$product_id)
                        ->where('sales_id', $sales_ori->id)
                        ->first();

                    $sales_detail_ori->qty -= $diff_qty;
                    $sales_detail_ori->qty_retur += $diff_qty;
                    $sales_detail_ori->subtotal = ($sales_detail_ori->qty - $diff_qty) * $sales_detail_ori->final_price - $sales_detail_ori->discount;
                    $sales_detail_ori->save();
                }

            }

            DB::commit();
            return redirect()->back()->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        $salesRetur = Sales::findOrFail($id);
        $sales_ori = Sales::where('id', $salesRetur->sales_parent_id)->first();

        try {
            foreach ($sales_ori->salesDetails as $sod) {
                if ($sod->qty < 0) {
                    $sod->qty = 0;
                }
                $sod->qty_retur = 0;
                $sod->subtotal = $sod->qty * $sod->final_price;
                $sod->status = 'success';
                $sod->is_retur = false;
                $sod->save();
            }

            // Kalkulasi ulang total sales
            $sales_ori->nominal_amount = $sales_ori->salesDetails->sum('subtotal');
            $sales_ori->final_amount = $sales_ori->nominal_amount - $sales_ori->discount_amount;
            $sales_ori->nominal_pay = $sales_ori->final_amount;
            $sales_ori->ref_code = null;
            $sales_ori->save();

            foreach ($salesRetur->salesDetails as $srd) {
                $srd->forceDelete();
            }
            $salesRetur->forceDelete();

            DB::commit();
            return redirect('/retur-sales')->withSuccess('Retur Berhasil Dibatalkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/retur-sales')->withWarning('Retur Gagal Dibatalkan!');
        }
    }

    public function getData(Request $request)
    {
        $sales = Sales::with('salesDetails.product')->findOrFail($request->id);

        return response()->json([
            'sale_code' => $sales->sale_code,
            'response' => $sales->salesDetails,
        ]);
    }

    public function getDetailSales(Request $request)
    {
        $saleDetail = SalesDetail::whereIn('id', $request->sl_id)
            ->with('product')
            ->whereNot('status')
            ->get();

        return response()->json([
            'response' => $saleDetail,
        ]);
    }

    public function finish(Request $request)
    {
        $return_id = $request->id;
        $sales_retur = Sales::where('id', $return_id)->with('salesDetails')->first();
        $sales_ori = Sales::where('id', $sales_retur->sales_parent_id)->first();



        DB::beginTransaction();

        try {
            //update subtotal sales retur nominal amount dan final_amount
            $sales_retur->nominal_amount = $sales_retur->salesDetails->sum('subtotal');
            $sales_retur->final_amount = $sales_retur->nominal_amount;
            $sales_retur->status = 'success';
            $sales_retur->save();

            //kalkulasi ulang sales_detail
            $total_nominal_sales_ori = 0;
            foreach ($sales_ori->salesDetails as $sd) {
                $sd->subtotal = $sd->qty * $sd->final_price;
                $sd->save();

                $total_nominal_sales_ori += $sd->subtotal;
            }

            //update nominal sales ori
            $sales_ori->nominal_amount = $total_nominal_sales_ori;
            $sales_ori->final_amount = $total_nominal_sales_ori - $sales_ori->discount_amount;
            $sales_ori->nominal_pay = $total_nominal_sales_ori - $sales_ori->discount_amount;
            $sales_ori->status = 'success';
            $sales_ori->save();

            $hppRetur = 0;

            foreach ($sales_retur->salesDetails as $sd) {
                $last_stock_history = ProductStockHistory::where('product_id', $sd->product_id)
                    ->where('outlet_id', $sales_retur->outlet_id)
                    ->where('document_number', $sales_retur->ref_code)
                    ->first();

                if (!$last_stock_history) {
                    continue;
                }

                $product_stock = ProductStock::where('product_id', $sd->product_id)
                    ->where('outlet_id', $sales_retur->outlet_id)
                    ->first();

                if (!$product_stock) {
                    continue;
                }

                $stock_before = $product_stock->stock_current;
                $new_stock = $stock_before + $sd->qty;

                $product_stock->update([
                    'stock_current' => $new_stock,
                ]);

                // Buat histori stok untuk retur
                ProductStockHistory::create([
                    'document_number' => $sales_retur->sale_code,
                    'history_date' => Carbon::now(),
                    'action_type' => 'PLUS',
                    'product_id' => $product_stock->product_id,
                    'outlet_id' => $product_stock->outlet_id,
                    'stock_change' => $sd->qty,
                    'stock_before' => $stock_before,
                    'stock_after' => $new_stock,
                    'desc' => 'Retur: ' . $sales_retur->sale_code . ' melakukan PENAMBAHAN QTY',
                    'user_id' => getUserIdLogin(),
                ]);

                $product = Product::find($sd->product_id);
                $hppRetur += $product->capital_price * $sd->qty;
            }

            // //update cashflow
            // $cf = Cashflow::where('transaction_code', $sales_retur->ref_code)->first();

            // if ($cf) {
            //     $cf->amount -= $sales_retur->final_amount;
            //     $cf->total_hpp -= $hppRetur;
            //     $cf->profit = $cf->amount - $cf->total_hpp;

            //     $cf->save();
            // }


            // kalkulasi ulang profit
            foreach ($sales_retur->salesDetails as $sd) {
                $sd->profit = 0;
                $sd->save();
            }

            foreach ($sales_ori->salesDetails as $sd) {
                $product = Product::find($sd->product_id);

                //jika is_item_bundle maka 0 saja
                if ($sd->is_item_bundle) {
                    $sd->profit = 0;
                } else {
                    $sd->profit = ($sd->final_price - $product->capital_price) * $sd->qty;
                }


                $sd->save();
            }

            // buat cashflow
            Cashflow::create([
                'outlet_id' => $sales_retur->outlet_id,
                'user_id' => $sales_ori->user_id,
                'code' => IdGenerator::generate([
                    'table' => 'cashflows',
                    'field' => 'code',
                    'length' => 11,
                    'prefix' => 'CF' . date('ym') . '-',
                    'reset_on_prefix_change' => true,
                ]),
                'transaction_code' => $sales_retur->sale_code,
                'transaction_date' => Carbon::now(),
                'type' => 'out',
                'amount' => $sales_retur->final_amount,
                'total_hpp' => 0,
                'profit' => null,
                'desc' => 'Retur',
            ]);

            DB::commit();
            return redirect('/retur-sales')->withSuccess('Sukses di Simpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->withError($e->getMessage());
        }
    }

    public function detail($id)
    {
        $retur = Sales::with('salesDetails', 'user', 'outlet')
            ->findOrFail($id);

        $total = $retur->salesDetails->sum('subtotal');

        $refund_reason = $retur->refund_reason;

        return view('pages.sales.retur_sales.detail', compact('retur', 'total', 'refund_reason'));
    }

    public function show($code)
    {
        $transaction = Sales::query()
            ->where('sale_code', $code)
            ->with(['salesDetails' => function ($query) {
                $query->where('status', '!=', 'void');
            }, 'customer', 'outlet'])
            ->first();
        $data = [
            'transaction' => $transaction,
        ];

        return view('pages.sales.retur_sales.show', $data);
    }

    public function receipt($code)
    {
        $transaction = Sales::query()
            ->where('sale_code', $code)
            ->with(['salesDetails' => function ($query) {
                $query->where('status', '!=', 'void');
            }, 'customer', 'outlet', 'paymentMethod'])
            ->first();

        $data = [
            'transaction' => $transaction,
            'company' => profileCompany(),
        ];

        return view('pages.sales.retur_sales.receipt', $data);
    }
}
