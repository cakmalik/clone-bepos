<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\CashflowClose;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashflowCloseRequest;
use App\Http\Requests\UpdateCashflowCloseRequest;
use App\Models\Cashflow;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashflowCloseController extends Controller
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
    public function create(Request $request)
    {
        try {
            $data['modal'] = (int)Cashflow::outlet($request->outlet_id)->where('type', 'modal')->sum('amount');
            $data['in'] =
                (int)Cashflow::outlet($request->outlet_id)->where('type', 'in')->sum('amount');
            $data['out'] = Cashflow::outlet($request->outlet_id)->where('type', 'out')->sum('amount');
            $data['profit'] =
                (int)Cashflow::outlet($request->outlet_id)->sum('profit');
            $data['total'] = $data['modal'] + $data['in'] - $data['out'];
            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
            exit;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCashflowCloseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCashflowCloseRequest $request)
    {

        $data = $request->all();
        $data['date'] = Carbon::now();
        try {
            $id = CashflowClose::create($data)->id;
            // update all cashflow_close_id
            Cashflow::outlet($request->outlet_id)->update([
                'cashflow_close_id' => $id
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
            exit;
        }
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CashflowClose  $cashflowClose
     * @return \Illuminate\Http\Response
     */
    public function show(CashflowClose $cashflowClose)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CashflowClose  $cashflowClose
     * @return \Illuminate\Http\Response
     */
    public function edit(CashflowClose $cashflowClose)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCashflowCloseRequest  $request
     * @param  \App\Models\CashflowClose  $cashflowClose
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCashflowCloseRequest $request, CashflowClose $cashflowClose)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CashflowClose  $cashflowClose
     * @return \Illuminate\Http\Response
     */
    public function destroy(CashflowClose $cashflowClose)
    {
        //
    }
}
