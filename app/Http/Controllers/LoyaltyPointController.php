<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use App\Models\LoyaltyPlusQty;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\LoyaltyClaimProduct;
use Illuminate\Support\Facades\Log;
use App\Models\LoyaltyClaimDiscount;

class LoyaltyPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = LoyaltyClaimProduct::with('product')->get();
        $discounts = LoyaltyClaimDiscount::get();
        $loyaltySettings = LoyaltyPoint::all();
        $loyaltyData = LoyaltyPlusQty::all();
        return view('pages.loyalty.index', compact('loyaltySettings', 'loyaltyData', 'products', 'discounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        $loyaltySetting = LoyaltyPoint::findOrFail($request->id);
        $loyaltySetting->update(['is_active' => $request->status]);
        return response()->json(['message' => 'Status updated successfully']);
    }
    public function updateLoyalty(Request $request)
    {
        try {
            $loyaltyId = $request->input('loyalty_id');
            $minTransaction = $request->input('min_transaction');
            $pointPlus = $request->input('point_plus');
            $appliesMultiply = $request->input('applies_multiply');
            $loyaltyPlusQty = LoyaltyPlusQty::find($loyaltyId);
            if ($loyaltyPlusQty) {
                $loyaltyPlusQty->min_transaction = $minTransaction;
                $loyaltyPlusQty->point_plus = $pointPlus;
                $loyaltyPlusQty->applies_multiply = $appliesMultiply;
                $loyaltyPlusQty->save();
                return response()->json(['message' => 'Loyalty updated successfully']);
            }
            return response()->json(['error' => 'Loyalty not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating loyalty: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function searchProductLoyalty(Request $request)
    {
        $products = Product::select('id', 'name')
            ->where('name', 'LIKE', '%' . $request->q . '%')
            ->limit(50)
            ->orderBy('name')
            ->get()->map(function ($row) {
                $row->text = $row->name;

                return $row;
            });

        return response()->json($products);
    }
    //Product
    function getClaimProduct()
    {
        return view('pages.loyalty.penukaran.createClaimProduct');
    }
    public function editViewProduct($id)
    {
        $data = LoyaltyClaimProduct::find($id);
        return view('pages.loyalty.penukaran.updateClaimProduct', compact('data'));
    }
    public function editProsesProduct(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'amount' => 'required|numeric',
            'qty' => 'required|integer',
        ]);
        $loyalty = LoyaltyClaimProduct::find($id);
        $loyalty->update([
            'product_id' => $request->input('product_id'),
            'previous_score' => $request->input('amount'),
            'qty' => $request->input('qty'),
        ]);
        return response()->json(['success' => 'Loyalty berhasil diperbarui!']);
    }
    public function creteClaimProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'amount' => 'required|numeric',
            'qty' => 'required|integer',
        ]);
        LoyaltyClaimProduct::create([
            'product_id' => $request->input('product_id'),
            'previous_score' => $request->input('amount'),
            'qty' => $request->input('qty'),
        ]);
        return response()->json(['message' => 'Data has been saved successfully']);
    }
    public function destroyClaimProduct($id)
    {
        DB::beginTransaction();
        try {
            LoyaltyClaimProduct::destroy($id);
            DB::commit();
            return response()->json([
                'message' => 'Sukses di Hapus',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal di Hapus',
            ], 422);
        }
    }
    //Discount
    public function editViewDiscount($id)
    {
        $data = LoyaltyClaimDiscount::find($id);
        return view('pages.loyalty.penukaran.updateClaimDiscount', compact('data'));
    }
    public function editProsesDiscount(Request $request, $id)
    {
        $request->validate([
            'discount_type' => 'required|in:PERCENT,NOMINAL',
            'amount' => 'required',
            'discount' => 'required',
        ]);
        $loyalty = LoyaltyClaimDiscount::find($id);
        $loyalty->discount_type = $request->input('discount_type');
        $loyalty->previous_score = $request->input('amount');
        $loyalty->value = $request->input('discount');
        $loyalty->save();

        return response()->json(['success' => $loyalty]);
    }
    function getClaimDiscount()
    {
        return view('pages.loyalty.penukaran.createClaimDiscount');
    }
    public function creteClaimDiscount(Request $request)
    {
        $request->validate([
            'discount_type' => 'required|in:PERCENT,NOMINAL',
            'amount' => 'required|numeric',
            'discount' => 'required|numeric',
        ]);
        LoyaltyClaimDiscount::create([
            'discount_type' => $request->input('discount_type'),
            'previous_score' => $request->input('amount'),
            'value' => $request->input('discount'),
        ]);
        return response()->json(['message' => 'Data has been saved successfully']);
    }
    public function destroyClaimDiscount($id)
    {
        DB::beginTransaction();
        try {
            LoyaltyClaimDiscount::destroy($id);
            DB::commit();
            return response()->json([
                'message' => 'Sukses di Hapus',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal di Hapus',
            ], 422);
        }
    }
}
