<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index()
    {
        try {
            $discounts = Discount::all();

            $data = [];
            foreach ($discounts as $discount) {
                $data[] = [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'nilai' => $discount->type == 'percentage' ? $discount->value . '%' : rupiah($discount->value),
                    'status' => $discount->status,
                ];
            }

            return view('pages.discount.index', compact('data'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function create()
    {
        return view('pages.discount.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'discount_name' => 'required',
            'discount_type' => 'required',
            'value' => 'required',
            'status' => 'required',
        ]);

        try {
            Discount::create([
                'name' => $request->discount_name,
                'type' => $request->discount_type,
                'value' => $request->value,
                'status' => $request->status,
            ]);

            return redirect()->route('discount.index')->with('success', 'Berhasil menambahkan diskon');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = Discount::find($id);
            return view('pages.discount.edit', compact('data'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request,$id)
    {
        $validator = $request->validate([
            'discount_name' => 'required',
            'discount_type' => 'required',
            'value' => 'required',
            'status' => 'required',
        ]);

        try {
            Discount::where('id',$id)->update([
                'name' => $request->discount_name,
                'type' => $request->discount_type,
                'value' => $request->value,
                'status' => $request->status,
            ]);

            return redirect()->route('discount.index')->with('success', 'Berhasil mengubah diskon');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
           DB::beginTransaction();
              $discount = Discount::find($id);
                if($discount->status == 'active'){
                    return response()->json([
                        'message' => 'Gagal di Hapus, status diskon aktif',
                    ], 422);
                }
                Discount::destroy($id);
                DB::commit();
                return response()->json([
                    'message' => 'Sukses di Hapus',
                ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal di Hapus',
            ], 422);
        }
    }
}
