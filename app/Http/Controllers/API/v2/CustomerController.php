<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function customers()
    {
       try{
        $getParam = request()->query('name');
        if ($getParam) {
            //urutkan by name
            $customers = Customer::where('name', 'like', '%' . $getParam . '%')->orderBy('name', 'asc')->get();
        } else {
            $customers = Customer::orderBy('name', 'asc')->get();
        }

            $data = [];
            foreach ($customers as $customer) {
                $data[] = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
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
                'data' => []
            ], 500);
        }
    }

    public function customersCreate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try{
            $code = autoCode('customers', 'code', 'CUST', 4);

            $customer = Customer::create([
                'code' => $code,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'date' => now(),
            ]);

            $data = [
                'id' => $customer->id,
                'name' => $customer->name,
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function customersUpdate(Request $request,$id)
    {
        try{
            $customer = Customer::find($id);

            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            $data = [
                'id' => $customer->id,
                'name' => $customer->name,
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function customersDelete($id)
    {
        try{
            $customer = Customer::find($id);
            $customer->delete();
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => []
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
