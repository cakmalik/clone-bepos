<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductStockReward;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use App\Models\StockMutationReward;
use App\Models\StockMutationRewardItems;
use Yajra\DataTables\Facades\DataTables;

class StockMutationRewardController extends Controller
{
    public function index()
{
    $rewards = StockMutationReward::all()->map(function ($reward) {
        $reward->formatted_date = Carbon::parse($reward->date)->format('d F Y H:i');
        return $reward;
    });

    return view('pages.stock_mutation_reward.index', ['title' => 'Mutasi Hadiah', 'reward' => $rewards]);
}

    public function create()
    {
        $type = ['MASUK', 'KELUAR'];
        return view('pages.stock_mutation_reward.create', ['title' => 'Buat Mutasi Hadiah', 'type' => $type]);
    }

    public function getProduct_reward(Request $request)
    {

        if ($request->type == 'MASUK') {
            $product = DB::table('products')
                ->select('products.*', 'product_stocks.stock_current as stok')
                ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
                ->where('product_stocks.outlet_id', getOutletActive()->id)
                ->whereIn('product_id', $request->items)
                ->get();
        } elseif ($request->type == 'KELUAR') {
            $product = DB::table('products')
                ->select('products.*', 'product_stock_rewards.qty as stok')
                ->join('product_stock_rewards', 'products.id', '=', 'product_stock_rewards.product_id')
                ->where('product_stock_rewards.outlet_id', getOutletActive()->id)
                ->whereIn('product_id', $request->items)
                ->get();
        } else {
            $product = [];
        }

        return response()->json($product);
    }


    public function show($id)
    {
        $reward = StockMutationRewardItems::with('product')->where('stock_mutation_reward_id', $id)->get();
        $outlet = StockMutationRewardItems::with('outlet')->where('stock_mutation_reward_id', $id)->first();
        $type = StockMutationReward::where('id', $id)->first();
        return view('pages.stock_mutation_reward.show', ['title' => 'Detail Mutasi Hadiah', 'reward' => $reward, 'outlet' => $outlet, 'type' => $type]);
    }


    public function store(Request $request)
    {

        DB::beginTransaction();

        $request->validate([
            'type' => 'required'
        ], [], [
            'type' => 'Tipe'
        ]);

        try {

            if ($request->type == "MASUK") {
                $product = Product::whereIn('id', $request->product_reward)->get();

                $query = DB::table('stock_mutation_rewards')->where('type', 'MASUK')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
                $cd = "";

                if ($query->count() > 0) {
                    foreach ($query->get() as $c) {
                        $tmp = ((int)$c->codes) + 1;
                        $cd = sprintf("%04s", $tmp);
                    }
                } else {
                    $cd = "0001";
                }

                $reward =  StockMutationReward::create([
                    'code' => 'MR-MASUK-' . date('y') . '-' . date('m') . '-' . date('d') . '-' . $cd,
                    'date' => Carbon::now(),
                    'type' => 'MASUK',
                    'description' => 'MUTASI STOK DARI PRODUK STOK KE STOK REWARD',
                ]);



                foreach ($product as $pd) {
                    $product_name = $pd->id . '_name';
                    $qty_current = $pd->id . '_qty_current';
                    $qty = $pd->id . '_qty';

                    $product_stock_reward = ProductStockReward::where('product_id', $pd->id)->where('outlet_id', getOutletActive()->id)->first();

                    $product_stock = ProductStock::where('product_id', $pd->id)->where('outlet_id', getOutletActive()->id)->first();

                    StockMutationRewardItems::create([
                        'stock_mutation_reward_id' => $reward->id,
                        'product_id' => $pd->id,
                        'outlet_id' => getOutletActive()->id,
                        'date' => Carbon::now(),
                        'qty' => $request->$qty
                    ]);

                    if ($product_stock_reward) {
                        ProductStockReward::where('product_id', $pd->id)->where('outlet_id', $pd->outlet_id)->update([
                            'qty' => $product_stock_reward->qty + $request->$qty
                        ]);

                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'PLUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => $product_stock_reward->qty,
                            'stock_after' => $product_stock_reward->qty + $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok Reward',
                        ]);

                        ProductStock::where('product_id', $pd->id)->where('outlet_id', $pd->outlet_id)->update([
                            'stock_current' => $product_stock->stock_current - $request->$qty
                        ]);

                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'MINUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => $product_stock->stock_current,
                            'stock_after' => $product_stock->stock_current - $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok',
                        ]);
                    } else {
                        ProductStockReward::create([
                            'product_id' => $pd->id,
                            'outlet_id' => getOutletActive()->id,
                            'date' => Carbon::now(),
                            'qty' => $request->$qty
                        ]);


                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'PLUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => 0,
                            'stock_after' =>  $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok Reward',
                        ]);

                        ProductStock::where('product_id', $pd->id)->where('outlet_id', $pd->outlet_id)->update([
                            'stock_current' => $product_stock->stock_current - $request->$qty
                        ]);


                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'MINUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => $product_stock->stock_current,
                            'stock_after' => $product_stock->stock_current - $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok',
                        ]);
                    }
                }
            } else {

                $product = Product::whereIn('id', $request->product_reward)->get();
                $query = DB::table('stock_mutation_rewards')->where('type', 'KELUAR')->select(DB::raw('MAX(RIGHT(code,4)) as codes'));
                $cd = "";

                if ($query->count() > 0) {
                    foreach ($query->get() as $c) {
                        $tmp = ((int)$c->codes) + 1;
                        $cd = sprintf("%04s", $tmp);
                    }
                } else {
                    $cd = "0001";
                }

                $reward =  StockMutationReward::create([
                    'code' => 'MR-KELUAR-' . date('y') . '-' . date('m') . '-' . date('d') . '-' . $cd,
                    'date' => Carbon::now(),
                    'type' => 'KELUAR',
                    'description' => 'MUTASI STOK DARI STOK REWARD KE PRODUK STOK',
                ]);


                foreach ($product as $pd) {
                    $product_name = $pd->id . '_name';
                    $qty_current = $pd->id . '_qty_current';
                    $qty = $pd->id . '_qty';

                    $product_stock_reward = ProductStockReward::where('product_id', $pd->id)->where('outlet_id', getOutletActive()->id)->first();

                    $product_stock = ProductStock::where('product_id', $pd->id)->where('outlet_id', getOutletActive()->id)->first();

                    StockMutationRewardItems::create([
                        'stock_mutation_reward_id' => $reward->id,
                        'product_id' => $pd->id,
                        'outlet_id' => getOutletActive()->id,
                        'date' => Carbon::now(),
                        'qty' => $request->$qty
                    ]);

                    if ($product_stock_reward) {
                        ProductStockReward::where('product_id', $pd->id)->where('outlet_id', $pd->outlet_id)->update([
                            'qty' => $product_stock_reward->qty - $request->$qty
                        ]);

                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'MINUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => $product_stock_reward->qty,
                            'stock_after' => $product_stock_reward->qty - $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok Reward',
                        ]);


                        ProductStock::where('product_id', $pd->id)->where('outlet_id', $pd->outlet_id)->update([
                            'stock_current' => $product_stock->stock_current + $request->$qty
                        ]);

                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'PLUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => $product_stock->stock_current,
                            'stock_after' => $product_stock->stock_current + $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok',
                        ]);
                    } else {
                        ProductStockReward::create([
                            'product_id' => $pd->id,
                            'outlet_id' => getOutletActive()->id,
                            'date' => Carbon::now(),
                            'qty' => $request->$qty
                        ]);

                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'MINUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => $product_stock_reward->qty,
                            'stock_after' =>  $product_stock_reward->qty - $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok Reward',
                        ]);


                        ProductStock::where('product_id', $pd->id)->where('outlet_id', $pd->outlet_id)->update([
                            'stock_current' => $product_stock->stock_current + $request->$qty
                        ]);

                        ProductStockHistory::create([
                            'document_number' => $reward->code,
                            'history_date' => Carbon::now(),
                            'action_type' => 'PLUS',
                            'user_id' => Auth()->user()->id,
                            'product_id' => $pd->id,
                            'inventory_id' => NULL,
                            'stock_change' => $request->$qty,
                            'stock_before' => 0,
                            'stock_after' => $request->$qty,
                            'desc' => 'Mutasi Hadiah ' . $reward->code . ' di ' . 'outlet ' . getOutletActive()->name . ' Produk Stok',
                        ]);
                    }
                }
            }



            DB::commit();
            return redirect('stock_mutation_reward')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
            // return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }


    public function get_product_reward(Request $request)
    {

        if ($request->type == 'MASUK') {
            $product = DB::table('products')
                ->select('products.*', 'product_stocks.stock_current as stok')
                ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
                ->where('product_stocks.outlet_id', getOutletActive()->id)
                ->get();
        } elseif ($request->type == 'KELUAR') {

            $product = DB::table('products')
                ->select('products.*', 'product_stock_rewards.qty as stok')
                ->join('product_stock_rewards', 'products.id', '=', 'product_stock_rewards.product_id')
                ->where('product_stock_rewards.outlet_id', getOutletActive()->id)
                ->get();
        } else {
            $product = [];
        }

        return response()->json($product);
    }
}
