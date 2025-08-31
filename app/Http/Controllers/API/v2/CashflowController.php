<?php

namespace App\Http\Controllers\API\v2;

use App\Models\Cashflow;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashflowRequest;
use App\Http\Requests\UpdateCashflowRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\CashflowClose;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Sales;

class CashflowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataCashflow()
    {
        try {
            $outlet_id = getOutletActive()->id;
            $cashflow = Cashflow::whereNull('cashflow_close_id')
                ->when($outlet_id, function ($q, $s) {
                    $q->where('outlet_id', (int)$s);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            //ambil semua data sales berdasarkan trancaction_code ( jika ada ) dari cashflow
            $salesData = Sales::whereIn('sale_code', $cashflow->pluck('transaction_code')->toArray())->get();

            $mapCashflow = $cashflow->map(function ($item) {
                return [
                    'id' => $item->id,
                    'transaction_code' => $item->transaction_code ?? '-',
                    'type' => $item->type,
                    'amount' => $item->amount,
                    'desc' => $item->desc,
                    'created_at' => $item->created_at->format('d-m-Y H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Kasir sudah dibuka',
                'modal' => $cashflow->where('type', 'modal')->sum('amount'),
                'pemasukan' => $cashflow->where('type', 'in')->sum('amount'),
                'pengeluaran' => $cashflow->where('type', 'out')->sum('amount'),
                'total' => $cashflow->where('type', 'modal')->sum('amount') + $cashflow->where('type', 'in')->sum('amount') - $cashflow->where('type', 'out')->sum('amount'),
                'total_sales' => $salesData->sum('final_amount'),
                'data' => $mapCashflow
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
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
    public function store(Request $request)
    {

        try {
            $data = $request->all();
            $data['code'] = autoCode('cashflows', 'code', 'CSFL-' . date('Y-m-'), 7);
            $data['outlet_id'] = getOutletActive()->id;
            $data['user_id'] = auth()->user()->id;

            $id = Cashflow::create($data)->id;
            $res = Cashflow::find($id);
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
            exit;
        }
    }

    public function cashflowInOut(Request $request)
    {
        try {
            $data = $request->all();
            $data['code'] = autoCode('cashflows', 'code', 'CSFL-' . date('Y-m-'), 7);
            $data['outlet_id'] = getOutletActive()->id;
            $data['user_id'] = auth()->user()->id;

            $id = Cashflow::create($data)->id;
            $res = Cashflow::find($id);
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
            exit;
        }
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
        try {

            $outlet_id = getOutletActive()->id;
            $cashflow = Cashflow::where('type', 'modal')->whereNull('cashflow_close_id')
                ->when($outlet_id, function ($q, $s) {
                    $q->where('outlet_id', (int)$s);
                })
                ->first();

            if (!$cashflow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada kasir yang dibuka',
                    'data' => null
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Kasir sudah dibuka',
                'data' => $cashflow
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function CashflowClose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'real_amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $outlet_id = getOutletActive()->id;
        $cashflowOpen = Cashflow::where('type', 'modal')->whereNull('cashflow_close_id')
            ->when($outlet_id, function ($q, $s) {
                $q->where('outlet_id', (int)$s);
            })
            ->first();

        if (!$cashflowOpen) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada kasir yang dibuka',
                'data' => null
            ], 200);
        }

        $cashflowData = Cashflow::whereNull('cashflow_close_id')
            ->when($outlet_id, function ($q, $s) {
                $q->where('outlet_id', (int)$s);
            })
            ->get();

        try {
            //create cashflowClose
            $data = $request->all();
            $data['outlet_id'] = getOutletActive()->id;
            $data['user_id'] = auth()->user()->id;
            $data['capital_amount'] = $cashflowData->where('type', 'modal')->whereNull('cashflow_close_id')->sum('amount');
            $data['income_amount'] = $cashflowData->where('type', 'in')->whereNull('cashflow_close_id')->sum('amount');
            $data['expense_amount'] = $cashflowData->where('type', 'out')->whereNull('cashflow_close_id')->sum('amount');
            $data['date'] = date('Y-m-d H:i:s');
            $data['profit_amount'] = $cashflowData->where('type', 'in')->whereNull('cashflow_close_id')->sum('amount') + $cashflowData->where('type', 'modal')->whereNull('cashflow_close_id')->sum('amount') - $cashflowData->where('type', 'out')->whereNull('cashflow_close_id')->sum('amount');
            $data['real_amount'] = $request->real_amount;
            $data['difference'] = $data['real_amount'] - $data['profit_amount'];
            $data['close_type'] = 'manual';
            $data['desc'] = $request->desc ?? 'tutup kasir manual';

            CashflowClose::create($data);

            //update cashflow table field cashflow_close_id all data date now
            $latestCashflowCloseId = CashflowClose::latest()->first()->id;

            // Loop through $cashflowData collection and update cashflow_close_id
            foreach ($cashflowData as $cashflow) {
                $cashflow->update(['cashflow_close_id' => $latestCashflowCloseId]);
            }

            $latestCashflowClose = CashflowClose::latest()->first();

            $data = [
                'name' => Auth::user()->users_name,
                'income' => $latestCashflowClose->income_amount,
                'expense' => $latestCashflowClose->expense_amount,
                'modal' =>  $latestCashflowClose->capital_amount,
                'date_open' => $cashflowData->where('type', 'modal')->first()->created_at->format('d-m-Y H:i'),
                'date_close' => $latestCashflowClose->created_at->format('d-m-Y H:i'),
                'total' => $latestCashflowClose->profit_amount,
                'real_amount' => $latestCashflowClose->real_amount,
                'difference' => $latestCashflowClose->difference,
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
            exit;
        }
    }

    public function CashflowCloseHistory()
    {
        try {
            $outlet_id = getOutletActive()->id;
            $cashflowClose = CashflowClose::with('cashflows')->where('outlet_id', $outlet_id)->get();

            //mapping data
            $data = $cashflowClose->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->user->users_name,
                    'income' => $item->income_amount,
                    'expense' => $item->expense_amount,
                    'modal' =>  $item->capital_amount,
                    // 'date_open' => Carbon::parse($item->cashflows->where('type', 'modal')->first()->created_at)->translatedFormat('d F Y h:i A'),
                    'date_close' => $item->created_at->format('d-m-Y H:i:s'),
                    'total' => $item->profit_amount,
                    'real_amount' => $item->real_amount,
                    'difference' => $item->difference,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
            exit;
        }
    }

    public function CashflowCloseHistoryDetail(Request $request)
    {

        $cashflowCloseId = $request->input('cashflow_close_id');
        $cashflowClose = CashflowClose::with('cashflows')->where('id', $cashflowCloseId)->first();

        if (!$cashflowClose) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data' => null
            ], 200);
        }

        try {
            $riwayatCash = $cashflowClose->cashflows->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'amount' => $item->amount,
                    'desc' => $item->desc,
                    'date' => $item->created_at->format('d-m-Y H:i:s'),
                ];
            });

            $data = [
                'id' => $cashflowClose->id,
                'name' => $cashflowClose->user->users_name,
                'income' => $cashflowClose->income_amount,
                'expense' => $cashflowClose->expense_amount,
                'modal' =>  $cashflowClose->capital_amount,
                'date_open' => $cashflowClose->cashflows->where('type', 'modal')->first()->created_at->format('d-m-Y H:i'),
                'date_close' => $cashflowClose->created_at->format('d-m-Y H:i'),
                'total' => $cashflowClose->profit_amount,
                'real_amount' => $cashflowClose->real_amount,
                'difference' => $cashflowClose->difference,
                'history_cashflow' => $riwayatCash
            ];

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
            exit;
        }
    }
}
