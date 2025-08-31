<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\CashflowClose;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportLabaRugiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role->role_name === 'SUPERADMIN') {
            $outlet = Outlet::orderBy('name')->get();
            $users = User::whereHas('role', function ($query) {
                $query->where('role_name', 'KASIR');
            })
                ->get();
        } else {
            $outlet = Outlet::whereIn('id', getUserOutlet())->get();
            $users = User::whereHas('role', function ($query) {
                $query->where('role_name', 'KASIR');
            })
                ->join('user_outlets', 'users.id', 'user_id')
                ->where('user_outlets.outlet_id', getUserOutlet())
                ->get();
        }

        return view('pages.report.laba-rugi.index', [
            'outlet' => $outlet,
            'users' => $users
        ]);
    }


    public function print(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $outlet = $request->query('outlet');
        $user = $request->query('user');

        // Query untuk CashflowClose
        $cashflowClose = CashflowClose::query()
            ->select(
                DB::raw('SUM(profit_amount) as total_profit'),
                DB::raw('SUM(capital_amount) as total_capital'),
                DB::raw('SUM(income_amount) as total_income'),
                DB::raw('SUM(expense_amount) as total_expense'),
                DB::raw('SUM(real_amount) as total_real_amount')
            )
            ->when(auth()->user()->role->role_name !== 'SUPERADMIN', function ($query) {
                $query->whereIn('outlet_id', getUserOutlet());
            })
            ->when($start_date, fn($query) => $query->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($query) => $query->whereDate('date', '<=', $end_date))
            ->when($outlet, fn($query) => $query->where('outlet_id', $outlet))
            ->when($user, fn($query) => $query->where('user_id', $user))
            ->first();

        $cashflowClose->total_capital = floatval($cashflowClose->total_capital);
        $cashflowClose->total_amount = floatval($cashflowClose->total_amount);
        $cashflowClose->total_real_amount = floatval($cashflowClose->total_real_amount);
        $cashflowClose->total_expense = floatval($cashflowClose->total_expense);

        // Query untuk Cashflow
        $cashflow = Cashflow::query()
            ->select(
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('SUM(total_hpp) as total_hpp'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->when(auth()->user()->role->role_name !== 'SUPERADMIN', function ($query) {
                $query->whereIn('outlet_id', getUserOutlet());
            })
            ->where([
                ['type', 'in'],
                ['cashflow_close_id', '!=', null]
            ])
            ->when($start_date, fn($query) =>
                $query->where('transaction_date', '>=', Carbon::parse($start_date)->startOfDay()))
            ->when($end_date, fn($query) =>
                $query->where('transaction_date', '<=', Carbon::parse($end_date)->endOfDay()))
            ->when($outlet, fn($query) => $query->where('outlet_id', $outlet))
            ->when($user, fn($query) => $query->where('user_id', $user))
            ->first();

        $cashflow->total_amount = floatval($cashflow->total_amount);

        // Nama user atau default "Semua Kasir"
        $userName = $user ? User::find($user)->users_name ?? 'Semua Kasir' : 'Semua Kasir';

        // Nama outlet atau default "Semua Outlet"
        $outletName = $outlet ? Outlet::find($outlet)->name ?? 'Semua Outlet' : 'Semua Outlet';

        // Label untuk rentang tanggal
        $label = $start_date === $end_date
            ? Carbon::parse($start_date)->translatedFormat('d M Y')
            : Carbon::parse($start_date)->translatedFormat('d M Y') . ' - ' . Carbon::parse($end_date)->translatedFormat('d M Y');


        // Alternatif hitung hpp penjualan
        $totalHpp = DB::table('sales_details as sd')
                ->select(
                    DB::raw('SUM(sd.hpp*qty) as subtotal_hpp'),
                )
                ->join('sales', 'sd.sales_id', 'sales.id')
                ->join('cashflows', 'sales.sale_code', 'cashflows.transaction_code')
                ->where([
                    ['type', 'in'],
                    ['cashflow_close_id', '!=', null]
                ])
                ->when($start_date, fn($query) =>
                    $query->where('sales.sale_date', '>=', Carbon::parse($start_date)->startOfDay()))
                ->when($end_date, fn($query) =>
                    $query->where('sales.sale_date', '<=', Carbon::parse($end_date)->endOfDay()))
                ->when($outlet, fn($query) => $query->where('sales.outlet_id', $outlet))
                ->when($user, fn($query) => $query->where('sales.user_id', $user))
                ->first();

        $totalHpp->subtotal_hpp = floatval($totalHpp->subtotal_hpp);

        // Return ke view dengan data yang diperlukan
        return view('pages.report.laba-rugi.print', [
            'title'         => 'Laporan Laba Rugi',
            'totalHpp'      =>  $totalHpp,
            'cashflow'      => $cashflow,
            'cashflowClose' => $cashflowClose,
            'userName'      => $userName,
            'outletName'    => $outletName,
            'company'       => profileCompany(),
            'label'         => $label,
        ]);
    }
}
