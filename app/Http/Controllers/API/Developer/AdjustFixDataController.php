<?php

namespace App\Http\Controllers\API\Developer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustFixDataController extends Controller
{
    public function wrongPurchaseDetail()
    {
        $purchaseDetails = PurchaseDetail::query()
            ->where('status', 'Purchase Requisition')
            ->get();


        DB::beginTransaction();

        try {

            $data = [];
            
            foreach($purchaseDetails as $purchase) {
                $product = Product::where('name', $purchase->product_name)->first();
    
                if ($product && $product->id != $purchase->product_id) {
                    $purchase->product_id = $product->id;
                    $purchase->save();

                    $data[] = $purchase;
                }
            }

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Data Updated!',
                'data'      => $data
            ]);
            
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success'   => false,
                'message'   => 'Data failed!'
            ], 500);

        }
    }


    public function productCategoryCode()
    {


        try {

            ProductCategory::query()->withTrashed()->update([
                'code' => '00000000'
            ]);

            $productCatgories = ProductCategory::withTrashed()->get();

            foreach($productCatgories as $value) {
                if ($value->is_parent_category == 1) {
                    $code = productCategoryCode();
                } else {
                    $code = productSubCategoryCode();
                }

                ProductCategory::withTrashed()->where('id', $value->id)->update([
                    'code'  => $code
                ]);

               
            }


            return response()->json([
                'success'   => true,
                'message'   => 'Data Updated!',
            ]);

        } catch (\Throwable $th) {
        
            return response()->json([
                'success'   => false,
                'message'   => 'Data failed!'
            ], 500);

        }
    }
}
