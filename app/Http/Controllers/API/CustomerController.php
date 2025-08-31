<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\CustomerStoreRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cust = Customer::when($request->input('search'), function ($q, $search) {
            $q->where('name', 'LIKE', "%{$search}%");
        })->latest()->limit(5)->get(['id', 'name', 'phone', 'code', 'address']);
        return response()->json([
            'status' => 'success',
            'data' => $cust
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $data = $request->all();
        $data['code'] = customerCode();
        $id = Customer::create($data)->id;
        $data['id'] = $id;
        return response()->json([
            'data' => $data
        ], 200);
    }
}
