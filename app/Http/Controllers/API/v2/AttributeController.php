<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Discount;
use App\Models\Outlet;
use Illuminate\Support\Facades\Storage;

class AttributeController extends Controller
{
    public function tables()
    {
        try {
            $tables = Table::where('outlet_id', getOutletActive()->id)->get();

            $data = [];
            foreach ($tables as $table) {
                $status = '';
                if ($table->status === 'available') {
                    $status = 'Tersedia';
                } elseif ($table->status === 'unavailable') {
                    $status = 'Tidak Tersedia';
                } elseif ($table->status === 'booked') {
                    $status = 'Dipesan';
                } else {
                    $status = 'Digunakan';
                }

                $data[] = [
                    'id' => $table->id,
                    'name' => $table->name,
                    'status' => $status,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }



    public function outlets()
    {
        try {
            $outlets = Outlet::where('id', getOutletActive()->id)->get();

            $data = [];
            foreach ($outlets as $outlet) {
                $data[] = [
                    'id' => $outlet->id,
                    'name' => $outlet->name,
                    'address' => $outlet->address,
                    'phone' => $outlet->phone,
                    'outlet_image' => $outlet->outlet_image ? asset('storage/images/' .$outlet->outlet_image) : null,
                    'desc' => $outlet->desc,
                    'footer_notes' => $outlet->footer_notes,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function outletUpdateFooterNote(Request $request)
    {
        try{
            $outlet = Outlet::where('id', getOutletActive()->id)->first();

            $outlet->footer_notes = $request->footer_notes;
            $outlet->save();

            return response()->json([
                'success' => true,
                'message' => 'sukses merubah deskripsi footer',
                'data' => $outlet->footer_notes
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function statistic()
    {
        dd('statistic');
    }

    public function discount()
    {
        try {
            $discounts = Discount::where('status', 'active')->get();

            $data = [];
            foreach ($discounts as $discount) {
                $data[] = [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'value' => $discount->value,
                    'type' => $discount->type,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
