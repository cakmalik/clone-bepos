<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use Exception;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\IndonesiaSubvillage;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\City;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class CustomerController extends Controller
{

    private function queryCustomer($request)
    {
        return Customer::select(
            'customers.*',
            'provinces.name as province_name',
            'cities.name as city_name',
            'districts.name as district_name',
            'villages.name as village_name',
            'customer_categories.name as category_name'
        )
            ->leftJoin('indonesia_provinces as provinces', 'customers.province_code', '=', 'provinces.code')
            ->leftJoin('indonesia_cities as cities', 'customers.city_code', '=', 'cities.code')
            ->leftJoin('indonesia_districts as districts', 'customers.district_code', '=', 'districts.code')
            ->leftJoin('indonesia_villages as villages', 'customers.village_code', '=', 'villages.code')
            ->leftJoin('customer_categories', 'customers.customer_category_id', '=', 'customer_categories.id') // customer category
            ->where(function ($query) use ($request) {
                if (!empty($request->city)) {
                    $query->where('customers.city_code', $request->city);
                }

                if (!empty($request->district)) {
                    $query->where('customers.district_code', $request->district);
                }

                if (!empty($request->village)) {
                    $query->where('customers.village_code', $request->village);
                }

                if (!empty($request->sub_village) && $request->sub_village !== 'null') {
                    $query->where('customers.sub_village', $request->sub_village);
                }
            })
            ->get();
    }


    public function index(Request $request)
    {

        if ($request->ajax()) {
            $customers = $this->queryCustomer($request);

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = ' <a href="/customer/' . $row->id . '/edit" class="btn btn-outline-dark">
                                <li class="fas fa-edit"></li>
                            </a>';

                    $btn = $btn . ' <a onclick="customerDelete(' . $row->id . ')" class="btn btn-outline-danger">
                            <li class="fas fa-trash"></li>
                        </a>';

                    return $btn;
                })->rawColumns(['action'])->make(true);
        }

        $cities = City::all();

        return view('pages.customer.customer.index', [
            'title'     => 'Pelanggan',
            'cities'    => $cities
        ]);
    }

    public function create()
    {
        $province = DB::table('indonesia_provinces')->get();
        $customer_category = DB::table('customer_categories')->get();
        return view('pages.customer.customer.create', ['province' => $province, 'customer_category' => $customer_category]);
    }


    public function store(Request $request)
    {
        // dd($request->all());

        DB::beginTransaction();

        $customer =  $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'nullable',
            'province_code' => 'nullable',
            'city_code' => 'nullable',
            'district_code' => 'nullable',
            'village_code' => 'nullable',
            'customer_category_id' => 'required'
        ], [], [
            'code' => 'Kode Customer',
            'name' => 'Nama Customer',
            'phone' => 'Nomor Telp',
            'address' => 'Alamat',
            'province_code' => 'Provinsi',
            'city_code' => 'Kota/Kabupaten',
            'district_code' => 'Kecamatan',
            'village_code' => 'Desa/Kelurahan',
            'customer_category_id' => 'Kategori Pelanggan'
        ]);


        if ($customer['address'] == null) {
            $customer['address'] = '-';
        }

        try {

            $customer['date'] = Carbon::now();
            $customer['sub_village'] = $request->sub_village;
            $customer['code'] = $request->code ?: customerCode();

            Customer::create($customer);

            if ($request->sub_village != '') {
                $subvillage = IndonesiaSubvillage::where([
                    ['village_code', $request->village_code],
                    ['name', $request->sub_village]
                ])->first();

                if (!$subvillage) {
                    IndonesiaSubvillage::create([
                        'village_code' => $request->village_code,
                        'name'         => $request->sub_village
                    ]);
                }
            }

            DB::commit();
            return redirect('/customer')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }


    public function edit($id)
    {
        $customer       = Customer::findOrFail($id);
        $provinces      = DB::table('indonesia_provinces')->get();
        $cities         = DB::table('indonesia_cities')->where('province_code', $customer->province_code)->get();
        $districts      = DB::table('indonesia_districts')->where('city_code', $customer->city_code)->get();
        $villages       = DB::table('indonesia_villages')->where('district_code', $customer->district_code)->get();
        $subvillages    = DB::table('indonesia_subvillages')->where('village_code', $customer->village_code)->get();
        $customer_category = DB::table('customer_categories')->get();
        $title          = 'Edit ' . $customer->name;

        return view('pages.customer.customer.edit', compact('customer', 'provinces', 'cities', 'districts', 'villages', 'subvillages', 'title', 'customer_category'));
    }


    public function update(Request $request, $id)
    {

        DB::beginTransaction();

        $data =  $request->validate([
            'code' => 'nullable',
            'name' => 'required',
            'phone' => 'required',
            'province_code' => 'nullable',
            'city_code' => 'nullable',
            'district_code' => 'nullable',
            'village_code' => 'nullable',
            'customer_category_id' => 'required'
        ], [], [
            'name' => 'Nama Customer',
            'phone' => 'Nomor Telp',
            'province_code' => 'Provinsi',
            'city_code' => 'Kota/Kabupaten',
            'district_code' => 'Kecamatan',
            'village_code' => 'Desa/Kelurahan',
            'customer_category_id' => 'Kategori Pelanggan'
        ]);

        try {


            $data['sub_village'] = $request->sub_village;
            $data['code'] = $request->code ?: customerCode();

            Customer::where('id', $id)->update($data);


            if ($request->sub_village != '') {
                $subvillage = IndonesiaSubvillage::where([
                    ['village_code', $request->village_code],
                    ['name', $request->sub_village]
                ])->first();

                if (!$subvillage) {
                    IndonesiaSubvillage::create([
                        'village_code' => $request->village_code,
                        'name'         => $request->sub_village
                    ]);
                }
            }

            DB::commit();
            return redirect('/customer')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Customer::destroy($id);
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

    public function getCity(Request $request)
    {
        $city = DB::table('indonesia_cities')->where('province_code', $request->province_code)->get();

        return response()->json([
            'response' => $city
        ]);
    }
    public function getDistrict(Request $request)
    {
        $district = DB::table('indonesia_districts')->where('city_code', $request->city_code)->get();

        return response()->json([
            'response' => $district
        ]);
    }
    public function getVillage(Request $request)
    {
        $village = DB::table('indonesia_villages')->where('district_code', $request->district_code)->get();

        return response()->json([
            'response' => $village
        ]);
    }

    public function getSubvillage(Request $request)
    {
        $subvillage = DB::table('indonesia_subvillages')
            ->where('village_code', $request->village_code)
            ->get();

        return response()->json([
            'response'  => $subvillage
        ]);
    }


    public function export(Request $request)
    {

        $customers = $this->queryCustomer($request);

        if ($request->type == 'pdf') {
            $pdf = PDF::loadView('export.customer_pdf', compact('customers'));
            return $pdf->stream('Report Customer.pdf');
        } else {
            return Excel::download(new CustomerExport($customers), 'Data Customer.xlsx');
        }
    }
}
