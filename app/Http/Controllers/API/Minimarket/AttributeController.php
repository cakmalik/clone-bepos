<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Models\User;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    public function tables()
    {
        dd('tables');
    }

    public function customers()
    {
        dd('customers');
    }

    public function outlets()
    {
        dd('outlets');
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
                'data' => []
            ], 500);
        }
    }

    public function getSuperiors()
    {
        $data = User::whereHas('role', function ($query) {
            $query->where('role_name', 'SUPERADMIN');
            $query->orWhere('role_name', 'ADMIN');
            $query->orWhere('role_name', 'SUPERVISOR');
        })->get(['users_name', 'email']);

        return responseAPI(true, 'berhasill', $data);
    }

    public function validateSuperior(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'pin' => 'required|numeric'
        ]);

        if ($validate->fails()) {
            return responseAPI(false, $validate->errors()->first(), null, 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return responseAPI(false, 'Email tidak ditemukan', null, 404);
        }

        if ($user->decryptedPin() === (int)$request->pin) {
            return responseAPI(true, 'PIN valid', null, 200);
        } else {
            return responseAPI(false, 'PIN tidak valid', null, 401);
        }
    }

    public function searchAddress(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'q' => 'required|string' //q nya kecamatan aja
        ]);

        if ($validate->fails()) {
            return responseAPI(false, $validate->errors()->first(), null, 400);
        }

        $kecamatan = \Indonesia::search($request->q)->paginateVillages(50);
        return responseAPI(false, 'Berhasil', $kecamatan);
    }

    public function banks()
    {
        $banks = DB::table('banks')->get()->map(function ($bank) {
            return [
                'label' => $bank->name,
                'value' => $bank->id
            ];
        });
        return responseAPI(true, 'Berhasil', $banks);
    }

    public function getPaymentMethodFees($payment_method_name){
        $payment_method = PaymentMethod::firstOrCreate(['name' => $payment_method_name], ['name' => $payment_method_name, 'transaction_fees' => 0]);
        return responseAPI(true, 'Berhasil', $payment_method->transaction_fees);
    }

    public function getAllPaymentMethods()
    {
        $paymentMethods = PaymentMethod::all(['id', 'name', 'is_active', 'transaction_fees', 'image']);

        $paymentMethods = $paymentMethods->map(function ($method) {
            $method->image = $method->image ? url('payment-methods/' . $method->image) : null;
            return $method;
        });

        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
        ]);
    }



    

}
