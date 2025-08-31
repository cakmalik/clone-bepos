<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\TieredPrices;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TieredPricesController extends Controller
{

    public $can_manage_selling_price = false;
    public function __construct()
    {
        $this->can_manage_selling_price = ProfilCompany::canManageSellingPrice();
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $tieredPrices = DB::table('tiered_prices as tp')
                ->select(
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.barcode as barcode',
                    'tp.min_qty',
                    'tp.max_qty',
                    'tp.price',
                    'tp.updated_at'
                )
                ->join('products as p', 'tp.product_id', 'p.id')
                ->where('p.outlet_id', getOutletActive()->id)
                ->where([
                    ['tp.deleted_at', null],
                    ['p.deleted_at', null]
                ])
                ->where(function ($query) use ($request) {
                    if ($request->product != '') {
                        $query->where('p.name', 'LIKE', '%' . $request->product . '%');
                        $query->orWhere('p.barcode', 'LIKE', '%' . $request->product . '%');
                    }
                })
                ->orderBy('tp.updated_at', 'DESC')
                ->limit(1000)
                ->get();

            $previousProductName = null;
            foreach ($tieredPrices as $item) {
                $item->price = rupiah($item->price);
                $item->updated_at = Carbon::parse($item->updated_at)->format('d-m-Y H:i');

                if ($item->product_name == $previousProductName) {
                    $item->product_name = '';
                } else {
                    $previousProductName = $item->product_name;
                }
            }

            return DataTables::of($tieredPrices)
                ->addColumn('action', function ($row) {
                    return view(
                        'pages.tiered_prices.action_column',
                        ['row' => $row]
                    );
                })->rawColumns(['action'])->make(true);
        }

        return view('pages.tiered_prices.index', [
            'title' => 'Harga Bertingkat',
            'can_manage_selling_price' => $this->can_manage_selling_price
        ]);
    }


    public function create()
    {
        return view('pages.tiered_prices.create', ['title' => 'Buat Harga Bertingkat']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'min_qty_1' => 'required|integer',
            'max_qty_1' => 'required|integer',
        ]);


        $lastMaxQty = $request->input('max_qty_1');
        $firstPrice = $request->input('price_1');

        for ($i = 2; $request->has("min_qty_$i") && $request->has("max_qty_$i"); $i++) {
            $minQtyKey = "min_qty_$i";
            $maxQtyKey = "max_qty_$i";

            $request->validate([
                $minQtyKey => "required|integer|min:" . ($lastMaxQty + 1),
                $maxQtyKey => "required|integer|min:" . ($request->input($minQtyKey) + 1),
            ]);

            $lastMaxQty = $request->input($maxQtyKey);
        }

        for ($i = 1; $request->has("min_qty_$i") && $request->has("max_qty_$i"); $i++) {
            $minQty = $request->input("min_qty_$i");
            $maxQty = $request->input("max_qty_$i");
            $price = $request->input("price_$i");

            if ($maxQty <= $minQty) {
                return redirect()->back()->with(
                    'error',
                    'Max Qty harus lebih tinggi dari pada minimum qty.'
                );
            }

            if ($i > 1 && $price <= $firstPrice) {
                return redirect()->back()->with(
                    'error',
                    'Harga bertingkat diinput dari yang terendah ke yang paling tinggi!'
                );
            }
        }

        for ($i = 1; $request->has("min_qty_$i") && $request->has("max_qty_$i"); $i++) {

            TieredPrices::create([
                'product_id' => $validatedData['product_id'],
                'min_qty'    => $request->input("min_qty_$i"),
                'max_qty'    => $request->input("max_qty_$i"),
                'price'      => str_replace(str_split('Rp.'), '', $request->input("price_$i")),
                'user_id'    => Auth::id()
            ]);
        }

        return redirect('/tiered_prices')->with('success', 'Data saved successfully.');
    }

    public function edit($id)
    {
        $tp_first = TieredPrices::where('product_id', $id)
            ->orderBy('min_qty', 'asc')
            ->with('product')
            ->first();

        $tp  = TieredPrices::WhereNot('id', $tp_first->id)
            ->where('product_id', $id)
            ->with('product')->get();

        return view('pages.tiered_prices.edit', [
            'title' => 'Ubah Harga Bertingkat',
            'tp' => $tp,
            'tp_first' => $tp_first
        ]);
    }

    public function update(Request $request, TieredPrices $tieredPrices)
    {

        DB::beginTransaction();
        try {

            if ($request->row_current[0] != null && $request->row_update[0] == null) {
                $row_current = $request->row_current[0];
                $array_current =  explode(",", $row_current);

                TieredPrices::whereIn('id', $array_current)->forceDelete();

                foreach ($array_current as $r) {

                    $min_qty = 'min_qty_current_' . $r;
                    $max_qty = 'max_qty_current_' . $r;
                    $price = 'price_current_' . $r;


                    TieredPrices::create([
                        'product_id' => $request->product_id,
                        'min_qty' => $request->$min_qty,
                        'max_qty' => $request->$max_qty,
                        'price' => str_replace(str_split('Rp.'), '', $request->$price),
                        'user_id'    => Auth::id()
                    ]);
                }
            } else {
                $row_current = $request->row_current[0];
                $array_current =  explode(",", $row_current);
                TieredPrices::whereIn('id', $array_current)->forceDelete();

                $row_update = $request->row_update[0];
                $array_update =  explode(",", $row_update);

                foreach ($array_current as $r) {
                    $min_qty_current = 'min_qty_current_' . $r;
                    $max_qty_current = 'max_qty_current_' . $r;
                    $price_current = 'price_current_' . $r;


                    TieredPrices::create([
                        'product_id' => $request->product_id,
                        'min_qty' => $request->$min_qty_current,
                        'max_qty' => $request->$max_qty_current,
                        'price' => str_replace(str_split('Rp.'), '', $request->$price_current),
                        'user_id'    => Auth::id()
                    ]);
                }

                foreach ($array_update as $r) {
                    $min_qty_update = 'min_qty_' . $r;
                    $max_qty_update = 'max_qty_' . $r;
                    $price_update = 'price_' . $r;


                    TieredPrices::create([
                        'product_id' => $request->product_id,
                        'min_qty' => $request->$min_qty_update,
                        'max_qty' => $request->$max_qty_update,
                        'price' => str_replace(str_split('Rp.'), '', $request->$price_update),
                        'user_id'    => Auth::id()
                    ]);
                }
            }


            DB::commit();
            return redirect('/tiered_prices')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            TieredPrices::where('product_id', $id)->delete();
            DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'Sukses di Hapus !'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message'  => 'Gagal di Hapus!'
            ], 422);
        }
    }


    public function product_delete($id)
    {

        DB::beginTransaction();
        try {
            TieredPrices::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'Sukses di Hapus !'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message'  => 'Gagal di Hapus!'
            ], 422);
        }
    }
}
