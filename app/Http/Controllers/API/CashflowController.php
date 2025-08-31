<?php

namespace App\Http\Controllers\API;

use App\Models\Cashflow;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashflowRequest;
use App\Http\Requests\UpdateCashflowRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class CashflowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreCashflowRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCashflowRequest $request)
    {
        $data = $request->all();
        $data['code'] = autoCode('cashflows', 'code', 'CSFL-'.date('Y-m-'), 7);
        try {
            $id = Cashflow::create($data)->id;
            $res = Cashflow::find($id);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
            exit;
        }
        return response()->json([
            'status' => 'success',
            'data' => $res
        ], 200);
    }

    /**
     * Display
     * $res = Cashflow::find($id);he specified resource.
     *
     * @param  \App\Models\Cashflow  $cashflow
     * @return \Illuminate\Http\Response
     */
    public function show(Cashflow $cashflow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cashflow  $cashflow
     * @return \Illuminate\Http\Response
     */
    public function edit(Cashflow $cashflow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCashflowRequest  $request
     * @param  \App\Models\Cashflow  $cashflow
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCashflowRequest $request, Cashflow $cashflow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cashflow  $cashflow
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cashflow $cashflow)
    {
        //
    }

    public function checkIsOpen(Request $request)
    {
        $cashflow = Cashflow::where('type', 'modal')->whereNull('cashflow_close_id')
            ->when($request->input('outlet'), function ($q, $s) {
                $q->where('outlet_id', (int)$s);
            })
            ->first();
        return response()->json([
            'status' => 'success',
            'data' => $cashflow
        ], 200);
    }
}
