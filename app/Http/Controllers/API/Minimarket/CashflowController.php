<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use App\Models\CashflowClose;
use App\Models\Sales;
use App\Models\SalesDetail;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CashflowController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $outlet_id = auth()->user()->outlets[0]->id;

        $cs = Cashflow::whereNull('cashflow_close_id')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->where('type', 'modal')->first();
        if ($cs) {
            $data['modal'] = $cs->amount;
        } else {
            $data['modal'] = 0;
        }

        $data['in'] = (int) Cashflow::whereNull('cashflow_close_id')
            ->whereNull('transaction_code')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->where('type', 'in')->sum('amount');
        
            $data['out'] = (int) Cashflow::whereNull('cashflow_close_id')
            ->whereNull('transaction_code')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->where('type', 'out')->sum('amount');

        $data['sales'] = (int) Cashflow::whereNull('cashflow_close_id')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->whereNotNull('transaction_code')
            ->sum('amount');

        $data['retur'] = (int) Cashflow::whereNull('cashflow_close_id')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->where('desc', 'retur')
            ->where('type', 'out')
            ->sum('amount');

        $data['total'] = (int) $data['modal'] + (int) $data['in'] - (int) $data['out'] + (int) $data['sales'] - (int) $data['retur'];

        $openTime = Cashflow::where('type', 'modal')->whereNull('cashflow_close_id')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->first();

        if ($openTime) {
            $data['open_time'] = Carbon::parse($openTime->created_at)->format('d M Y, H:i:s');
        } else {
            $data['open_time'] = null;
        }

        return responseAPI(true, 'Data berhasil diambil', $data);
    }

    public function store(Request $request)
    {
        // TODO: Validasi jika sudah dibuka, maka tidak bisa menambahkan modal

        $user_id = auth()->user()->id;
        $outlet_id = auth()->user()->outlets[0]->id;
        $data = $request->all();
        $data['outlet_id'] = $outlet_id;
        $data['user_id'] = auth()->user()->id;
        $data['transaction_date'] = Carbon::now();
        $data['code'] = IdGenerator::generate([
            'table' => 'cashflows',
            'field' => 'code',
            'length' => 11,
            'prefix' => 'CF'.date('ym').'-',
            'reset_on_prefix_change' => true,
        ]);

        if ($data['type'] == 'modal') {
            $data['desc'] = 'Modal pembukaan kas';
        }

        try {
            Cashflow::create($data);

            return responseAPI(true, 'Berhasil menambahkan data');
        } catch (Exception $e) {
            return responseAPI(false, $e->getMessage(), null, 500);
        }
    }

    public function checkIsOpen()
    {
        $user_id = auth()->user()->id;
        $outlet_id = auth()->user()->outlets[0]->id;

        $cashflow = (bool) Cashflow::where('type', 'modal')->whereNull('cashflow_close_id')

            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->first();

        return responseAPI(true, 'Belum dibuka', $cashflow);
    }

    public function close(Request $request)
    {
        $user_id = auth()->user()->id;
        $outlet_id = auth()->user()->outlets[0]->id;
        $summary_data = $this->__getSummaryProductSold($outlet_id);

        $validated = Validator::make($request->all(), [
            'real_amount' => 'required', // uang cashdraw
            'capital_amount' => 'required', // modal awal
            'income_amount' => 'required', // kas masuk
            'expense_amount' => 'required', // kas keluar
            'profit_amount' => 'required', // TODO: Profit ini nanti dikalkulasi lagi pake fungsi, masih salah
        ]);

        if ($validated->fails()) {
            return responseAPI(false, $validated->errors()->first(), null, 400);
        }

        try {
            $modal = (int) Cashflow::where('type', 'modal')
                ->whereNull('cashflow_close_id')
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)
                ->sum('amount');

            $income = (int) Cashflow::where('type', 'in')
                ->whereNull('cashflow_close_id')
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)
                ->sum('amount');

            $cash_in = (int) Cashflow::where('type', 'in')
                ->whereNull('cashflow_close_id')
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)
                ->whereNull('transaction_code')
                ->sum('amount');

            $expense = (int) Cashflow::where('type', 'out')
                ->whereNull('cashflow_close_id')
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)
                ->sum('amount');

            $otherIncome = (int) Cashflow::where('type', 'in')
                ->whereNull('cashflow_close_id')
                ->where('transaction_code', null)
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)
                ->sum('amount');

            $cashflow_sales = Cashflow::with('transaction')
                ->whereNotNull('transaction_code')
                ->whereNull('cashflow_close_id')
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)
                ->get();

            // dd($otherIncome);

            // return response()->json($cashflow_sales[0]->transaction->payment_method_name = 'Cash');
            $subtotal_edc = 0;
            $subtotal_cash = 0;
            $subtotal_qris = 0;
            $subtotal_tempo = 0;

            foreach ($cashflow_sales as $cashflow) {
                $payment_method = strtolower($cashflow->transaction->payment_method_name);
                if ($payment_method == 'edc') {
                    $subtotal_edc += $cashflow->transaction->final_amount;
                } elseif ($payment_method == 'cash' && $cashflow->transaction->is_retur == 0) {
                    $subtotal_cash += $cashflow->transaction->final_amount;
                } elseif ($payment_method == 'qris') {
                    $subtotal_qris += $cashflow->transaction->final_amount;
                } elseif ($payment_method == 'tempo') {
                    $subtotal_tempo += $cashflow->transaction->final_amount;
                }
            }

            $cc = new CashflowClose;
            $cc->outlet_id = $outlet_id;
            $cc->user_id = $user_id;
            $cc->capital_amount = $modal;
            $cc->real_amount = $request->real_amount;
            $cc->income_amount = $income;

            $cc->expense_amount = $request->expense_amount;
            $cc->profit_amount = $request->profit_amount;
            $cc->difference = $request->real_amount - ($modal + $subtotal_cash + $otherIncome - $expense);
            $cc->date = Carbon::now();
            $cc->close_type = 'manual';
            $cc->save();

            $cashflows = Cashflow::whereNull('cashflow_close_id')
                ->where('outlet_id', $outlet_id)
                ->where('user_id', $user_id)->get();
            if ($cashflows->count() == 0) {
                return responseAPI(false, 'Belum ada cashflow yang dibuka');
            }
            foreach ($cashflows as $cashflow) {
                $cashflow->cashflow_close_id = $cc->id;
                $cashflow->save();
            }

            $data = [
                'user' => auth()->user()->users_name,
                'modal' => (int) $modal,
                'in' => (int) $income,
                'cash_in' => (int) $cash_in,
                'out' => (int) $expense,
                'real_amount' => $cc->real_amount,
                'total' => (int) $modal + (int) $income - (int) $expense,
                'difference' => $cc->difference,
                'close_time' => Carbon::parse($cc->date)->translatedFormat('d M Y, H:i'),
                'open_time' => Carbon::parse($cashflows[0]->created_at)->translatedFormat('d M Y, H:i'),
                'subtotal_edc' => $subtotal_edc,
                'subtotal_cash' => $subtotal_cash,
                'subtotal_qris' => $subtotal_qris,
                'subtotal_tempo' => $subtotal_tempo,
                'summary_data' => $summary_data,
            ];

            return responseAPI(true, 'Berhasil menutup kasir', $data);
        } catch (Exception $e) {
            return responseAPI(false, $e->getMessage().'-'.$e->getLine(), null);
        }
    }

    public function openTime()
    {
        $user_id = auth()->user()->id;
        $outlet_id = auth()->user()->outlets[0]->id;

        $cashflow = Cashflow::where('type', 'modal')->whereNull('cashflow_close_id')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->first();
        if ($cashflow) {
            return responseAPI(true, 'Berhasil mengambil data', $cashflow->created_at);
        } else {
            return responseAPI(false, 'Belum ada data');
        }
    }

    public function __getSummaryProductSold(int $outlet_id): ?array
    {
        $firstTrans = Cashflow::where('type', 'modal')
            ->where('outlet_id', $outlet_id)
            ->whereNull('cashflow_close_id')
            ->orderByDesc('created_at')
            ->first()?->created_at;

        if (! $firstTrans) {
            return null;
        }

        $startTime = Carbon::parse($firstTrans);
        $endTime = Carbon::now();

        $salesSummary = SalesDetail::select('product_id', 'product_name')
            ->selectRaw('SUM(qty) as total_qty, SUM(subtotal) as total_subtotal')
            ->where('status', 'success')
            ->where('outlet_id', $outlet_id)
            ->whereBetween('created_at', [$startTime, $endTime])
            ->groupBy('product_id', 'product_name')
            ->get();

        if (! $salesSummary) {
            return null;
        }

        Log::info($salesSummary);

        return [
            'product_sold' => $salesSummary,
        ];
    }
}
