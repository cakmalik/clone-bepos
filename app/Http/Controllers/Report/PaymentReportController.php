<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Sales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentReportController extends Controller
{
    public function index(Request $request)
    {
        try {

            $outlets = Outlet::all();
            $customers = Customer::all();
            $cashiers = User::select('users.id', 'users_name')
                ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                ->where('roles.role_name', 'Kasir')
                ->get();

            return view('pages.report.payment.index', compact('outlets', 'customers', 'cashiers'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function print(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $outlet_id = $request->query('outlet_id');
        $customer_id = $request->query('customer_id');
        $cashier_user_id = $request->query('cashier_user_id');

        $sales = Sales::with(['creator', 'customer', 'paymentMethod'])
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('sale_date', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('sale_date', '<=', $end_date);
            })
            ->when($outlet_id, function ($query) use ($outlet_id) {
                return $query->where('outlet_id', $outlet_id);
            })
            ->when($customer_id, function ($query) use ($customer_id) {
                if ($customer_id == 'walk-in-customer') {
                    return $query->where('customer_id', null);
                } else {
                    return $query->where('customer_id', $customer_id);
                }
            })
            ->when($cashier_user_id, function ($query) use ($cashier_user_id) {
                return $query->where('cashier_user_id', $cashier_user_id);
            })
            ->get();

        $paymentMethod = [];
        $total = 0;

        foreach ($sales as $sale) {
            if (!isset($paymentMethod[$sale->paymentMethod->name])) {
                $paymentMethod[$sale->paymentMethod->name] = 0;
            }
            $paymentMethod[$sale->paymentMethod->name] += $sale->final_amount;
            $total += $sale->final_amount;
        }

        return view('pages.report.payment.print')->with([
            'profileCompany' => profileCompany(),
            'sales' => $sales,
            'paymentMethod' => $paymentMethod,
            'total' => $total,
        ]);
    }
}
