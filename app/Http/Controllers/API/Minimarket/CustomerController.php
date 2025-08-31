<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::when($request->keyword, function ($query) use ($request) {
            $query->where('code', 'like', "%{$request->keyword}%")
                ->orWhere('name', 'like', "%{$request->keyword}%")
                ->orWhere('phone', 'like', "%{$request->keyword}%");
        })->select('id', 'name', 'phone', 'code', 'customer_category_id')->first();

        return responseAPI(true, 'List Data Customer', $customers);
    }

    //store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'village_code' => 'required',
            'district_code' => 'required',
        ]);

        if ($validator->fails()) {
            return responseAPI(false, 'Validation error', $validator->errors());
        }

        $city_code = DB::table('indonesia_districts')->where('code', $request->district_code)->first()->city_code;
        $province_code = DB::table('indonesia_cities')->where('code', $city_code)->first()->province_code;

        $config = [
            'table' => 'customers',
            'field' => 'code',
            'length' => 10,
            'prefix' => 'CUST-'
        ];

        DB::beginTransaction();
        try {
            $customer =  tap(Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'code' => IdGenerator::generate($config),
                'village_code' => $request->village_code,
                'district_code' => $request->district_code,
                'city_code' => $city_code,
                'province_code' => $province_code,
            ]), function ($customer) {
                unset($customer->created_at, $customer->updated_at);
            });

            // bisa juga pake unset kyk gini, lebih simple ggaes
            // unset($customer->created_at);
            // unset($customer->updated_at);

            $status = true;
            $message = 'Customer Berhasil Ditambahkan';
            $data = $customer;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $status = false;
            $message = 'Customer Gagal Ditambahkan';
            $data = $e->getMessage();
        }
        return responseAPI($status, $message, $data);
    }
}
