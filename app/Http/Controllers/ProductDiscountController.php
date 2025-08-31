<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\ProductDiscount;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $product = $request->product;
            $discountType = $request->discount_type;

            $productDiscount = ProductDiscount::with(['product.productPriceUtama']);

            if ($startDate || $endDate || $product || $discountType) {

                if ($startDate) {
                    $productDiscount->where('start_date', '>=', $startDate);
                }

                if ($endDate) {
                    $productDiscount->where('expired_date', '<=', $endDate);
                }

                if ($product) {
                    $productDiscount->where('product_id', $product);
                }

                if ($discountType) {
                    $productDiscount->where('discount_type', $discountType);
                }
            }

            $productDiscount = $productDiscount->get()->map(function ($row) {
                $row->barcode = $row->product->barcode;
                $row->product_name = $row->product->name;
                $row->price = rupiah($row->product->productPriceUtama->price);
                if ($row->discount_type == 'NOMINAL') {
                    $row->after_discount = rupiah($row->product->productPriceUtama->price - $row->amount);
                    $row->amount = rupiah($row->amount);
                } else {
                    $row->after_discount = rupiah($row->product->productPriceUtama->price - ($row->product->productPriceUtama->price * ($row->amount / 100)));
                    $row->amount = $row->amount . '%';
                }
                return $row;
            });

            return DataTables::of($productDiscount)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $action_link = '<a href="/ProductDiscount/' . $row->id . '/edit" class="btn btn-outline-dark"><i class="fas fa-edit"></i></a>
                    <a href="#" class="btn btn-outline-danger" onclick="productDiscountDelete(' . $row->id . ')"><i class="fas fa-trash"></i></a>';


                    return $action_link;
                })
                ->rawColumns(['action'])
                ->make(true);
        }



        $discountType = ['NOMINAL', 'PERCENT'];
        return view('pages.product_discount.index', [
            'title' => 'Produk Diskon',
            'discount_type' => $discountType
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $discountType = ['NOMINAL', 'PERCENT'];
        return view('pages.product_discount.create', [
            'title' => 'Buat Produk Diskon',
            'discount_type' => $discountType
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
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'product_id' => 'required',
                'discount_type' => 'required',
                'start_date' => 'required',
                'expired_date' => 'required',
                'amount' => 'required',
            ], [], [
                'product_id' => 'Nama Produk',
                'discount_type' => 'Tipe Diskon',
                'start_date' => 'Mulai Tanggal',
                'expired_date' => 'Sampai Tanggal',
                'amount' => 'Jumlah',
            ]);
            if ($request->discount_type == 'NOMINAL') {
                $data['amount'] = str_replace(str_split('Rp.'), '', $request->amount);
            } else {
                $data['amount'] = $request->amount;
            }
            ProductDiscount::create($data);
            DB::commit();
            return redirect('ProductDiscount')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductDiscount  $productDiscount
     * @return \Illuminate\Http\Response
     */
    public function show(ProductDiscount $productDiscount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductDiscount  $productDiscount
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $discountType = ['NOMINAL', 'PERCENT'];
        $productDiscount = ProductDiscount::with('product.productPriceUtama')->where('id', $id)->first();


        if ($productDiscount->discount_type == 'NOMINAL') {

            $finalHarga = $productDiscount->product->productPriceUtama->price - $productDiscount->amount;
            $amount = rupiah($productDiscount->amount);
        } else {

            $finalHarga = $productDiscount->product->productPriceUtama->price - ($productDiscount->product->productPriceUtama->price * ($productDiscount->amount / 100));

            $amount = $productDiscount->amount;
        }


        return view('pages.product_discount.edit', [
            'title' => 'Ubah Produk Diskon',
            'productDiscount' => $productDiscount,
            'discount_type' => $discountType,
            'final_harga' => $finalHarga,
            'amount' => $amount
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductDiscount  $productDiscount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductDiscount $productDiscount)
    {


        DB::beginTransaction();
        try {
            $data = $request->validate([
                'product_id' => 'required',
                'discount_type' => 'required',
                'start_date' => 'required',
                'expired_date' => 'required',
                'amount' => 'required',
            ], [], [
                'product_id' => 'Nama Produk',
                'discount_type' => 'Tipe Diskon',
                'start_date' => 'Mulai Tanggal',
                'expired_date' => 'Sampai Tanggal',
                'amount' => 'Jumlah',
            ]);
            if ($request->discount_type == 'NOMINAL') {
                $data['amount'] = str_replace(str_split('Rp.'), '', $request->amount);
            } else {
                $data['amount'] = $request->amount;
            }
            ProductDiscount::where('id', $request->id)->update($data);
            DB::commit();
            return redirect('ProductDiscount')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductDiscount  $productDiscount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            ProductDiscount::destroy($id);
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

    public function getPriceProduct(Request $request)
    {
        $productPrice = ProductPrice::where('product_id', $request->product_id)->where('type', 'utama')->first();

        return response()->json($productPrice);
    }
}
