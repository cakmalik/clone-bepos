<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use App\Models\CashflowClose;
use App\Models\PaymentMethod;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CashflowCloseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $start_date = $request->start_date ? Carbon::parse($request->start_date)->toDateString() : null;
            $end_date = $request->end_date ? Carbon::parse($request->end_date)->toDateString() : null;
            $outlet_id = $request->outlet_id;
            $roleName = auth()->user()->role->role_name ?? null;

            $cashflows = CashflowClose::with(['outlet', 'user'])
                ->withTrashed()
                ->when($roleName !== 'SUPERADMIN', function ($query) {
                    $query->whereIn('outlet_id', getUserOutlet());
                })
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('date', '<=', $end_date);
                })
                ->when($outlet_id, function ($query) use ($outlet_id) {
                    $query->where('outlet_id', $outlet_id);
                })
                ->orderBy('date', 'desc')
                ->get();

            return Datatables::of($cashflows)
                ->addIndexColumn()
                ->addColumn('preview_url', function ($item) {
                    return route('cashflowClose.print', $item->id);
                })
                ->addColumn('user_name', function ($item) {
                    return optional($item->user)->users_name ?? '-';
                })
                ->addColumn('outlet_name', function ($item) {
                    return optional($item->outlet)->name ?? '-';
                })
                ->rawColumns(['preview_url'])
                ->make(true);
        }

        $roleName = auth()->user()->role->role_name ?? null;
        $outlets = $roleName === 'SUPERADMIN'
            ? Outlet::all()
            : Outlet::whereIn('id', getUserOutlet())->get();

        return view('pages.cashflow_close.index', compact('outlets'));
    }


    public function print(Request $request, $id)
    {
        $cashflowClose = CashflowClose::findOrFail($id);
        return view('pages.cashflow_close.print', compact('cashflowClose'));
    }

    public function receipt(Request $request, $id)
    {
        $cashflowClose = CashflowClose::findOrFail($id);
        $cashflowClose->load('outlet', 'cashflows.transaction.paymentMethod', 'user');

        $groupedPayments = $cashflowClose->cashflows
            ->where('type', 'in')
            ->where('transaction_code', '!=', null)
            ->groupBy(fn($cashflow) => $cashflow->transaction->paymentMethod->name);

        $paymentSums = $groupedPayments->map(function ($cashflows) {
            return $cashflows->sum('amount');
        });

        $cashflowClose->capital_amount = floatval($cashflowClose->capital_amount);

        return view('pages.cashflow_close.receipt', compact('cashflowClose', 'paymentSums'));
    }
}
