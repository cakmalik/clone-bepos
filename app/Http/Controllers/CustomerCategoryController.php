<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerCategory;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;

class CustomerCategoryController extends Controller
{
    public function index()
    {
        $dataCustomerCategories = CustomerCategory::all();
        return view('pages.customer.customer_category.index', compact('dataCustomerCategories'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        try {
            DB::beginTransaction();
            $customerCategory = new CustomerCategory;
            $customerCategory->code = customerCategoryCode();
            $customerCategory->name = $request->name;
            $customerCategory->slug = strtolower(str_replace(' ', '-', $request->name));
            $customerCategory->description = $request->description;
            $customerCategory->save();
            DB::commit();
            return redirect()->route('customerCategory.index')->with('success', 'Kategori Pelanggan Berhasil Dibuat');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('customerCategory.index')->with('error', 'Kategori Pelanggan Gagal Dibuat');
        }
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
        $request->validate([
            'name' => 'required',
            'desc' => 'nullable',
        ]);

        try {
            DB::beginTransaction();
            $customerCategory = CustomerCategory::find($id);
            $customerCategory->name = $request->name;
            $customerCategory->slug = strtolower(str_replace(' ', '-', $request->name));
            $customerCategory->description = $request->description;
            $customerCategory->save();
            DB::commit();
            return redirect()->route('customerCategory.index')->with('success', 'Kategori Pelanggan Berhasil Diperbarui');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('customerCategory.index')->with('error', 'Kategori Pelanggan Gagal Diperbarui');
        }
    }

    public function destroy($id)
    {
        $customerCategory = CustomerCategory::find($id);

        if (!$customerCategory) {
            return redirect()->route('customerCategory.index')->with('error', 'Kategori Pelanggan tidak ditemukan');
        }

        $isUsedUser = Customer::where('customer_category_id', $id)->exists();
        $isUsedProduct = ProductPrice::where('customer_category_id', $id)->exists();

        if ($isUsedUser || $isUsedProduct) {
            return redirect()->route('customerCategory.index')->with('error', 'Kategori ini sudah digunakan');
        }

        $customerCategory->delete();

        return redirect()->route('customerCategory.index')->with('success', 'Kategori Pelanggan Berhasil Dihapus');
    }
}
