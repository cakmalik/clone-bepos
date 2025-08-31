<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Sales;
use App\Models\SalesPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Livewire\Inventory\StockOpname\Create;
use App\Models\Customer;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class DebtPaymentController extends Controller
{
    public function index(Request $request)
    {
        $sales = DB::table('sales')
            ->join('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->join('customers as c', 'sales.customer_id', 'c.id')
            ->leftJoin('sales_payments as sp', 'sales.id', 'sp.sale_id')
            ->select(
                'sales.id',
                'sales.sale_code',
                'sales.sale_date',
                'sales.due_date',
                'sales.final_amount',
                'c.name as customer',
                DB::raw('COALESCE(SUM(sp.nominal_payment), 0) as total_payment')
            )
            ->where('sales.payment_method_id', 4)
            ->where('sales.is_retur', false)
            ->where(function($query) use($request) {
                if ($request->customer != '') {
                    $query->where('customer_id', $request->customer);
                }


                if ($request->start_date != '' && $request->end_date) {
                    $query->where('sale_date', '>=', $request->start_date);
                    $query->where('sale_date', '<=', $request->end_date);

                }

                if ($request->payment_status != '') {
                    $query->where('sales.payment_status', $request->payment_status);
                }

            })
            ->groupBy('sales.id', 'c.name')
            ->orderBy('sale_date', 'desc')
            ->get()->map(function ($row) {
                $row->receivable = $row->final_amount - $row->total_payment;
                $row->receivable_idr = rupiah($row->final_amount - $row->total_payment);
                $row->sale_date = dateWithTime($row->sale_date);
                $row->due_date_formatted = $row->due_date ? dateStandar($row->due_date) : 'Atur Tanggal';
                $row->due_date = $row->due_date ? date('Y-m-d', strtotime($row->due_date)) : null;


                return $row;
            });

        
        $customers = Customer::orderBy('name')->get();
        

        return view('pages.sales.debt_payment.index', [
            'sales' => $sales,
            'customers' => $customers,
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }

    public function create($id)
    {
        $sales = Sales::query()
            ->with('customer')
            ->where('id', $id)
            ->first();

        $sales->sale_date = dateWithTime($sales->sale_date);
        $paymentMethods = PaymentMethod::where('is_active', true)->where('name', '!=', 'TEMPO')->get();
        $salesPayment = SalesPayment::where('sale_id', $id)->get();
        $totalPayment = SalesPayment::where('sale_id', $id)->sum('nominal_payment');

        return view('pages.sales.debt_payment.create', compact('sales', 'paymentMethods', 'salesPayment', 'totalPayment'));
    }

    public function store(Request $request)
    {
        
        DB::beginTransaction();

        try {

            SalesPayment::create([
                'sale_id' => $request->sales_id,
                'payment_method_id' => $request->payment_method,
                'user_id' => auth()->user()->id,
                'code' => time(),
                'nominal_payment' => rupiahToInteger($request->nominal_payment),
                'payment_date' => $request->payment_date.' '.date('H:i:s'),
                'description' => $request->description
            ]);

            $salesPayment = SalesPayment::where('sale_id', $request->sales_id)->sum('nominal_payment');
    
            $sales = Sales::where('id', $request->sales_id)->first();
            $sales->nominal_pay = $salesPayment;

            if ($salesPayment >= $sales->final_amount) {
                $sales->payment_status = PaymentStatus::PAID;
            } else {
                $sales->payment_status = PaymentStatus::INSTALLMENT;
            }

            $sales->save();

            DB::commit();
    
            return redirect()->route('debtPayment.index')->with('success', 'Data berhasil disimpan');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('error', 'Kesalahan di server. Gagal menyimpan!');

        }

        
    }

    public function updateDueDate(Request $request, $id)
    {
        $sale = Sales::findOrFail($id);
        $sale->due_date = $request->input('due_date');
        $sale->save();

        return redirect()->back()->with('success', 'Tanggal jatuh tempo berhasil diperbarui.');
    }

    public function print($id)
    {
        $sales = Sales::query()
            ->with('customer')
            ->where('id', $id)
            ->first();

        return view('pages.sales.debt_payment.print', compact('sales'));
    }

    public function nota($id)
    {
        $sales = Sales::query()
            ->with('customer')
            ->where('id', $id)
            ->first();

        $salesPayment = SalesPayment::where('sale_id', $id)->get();
        $totalPayment = SalesPayment::where('sale_id', $id)->sum('nominal_payment');
        
        $data = [
            'company'   => profileCompany(),
            'sales'     => $sales,
            'salesPayment' => $salesPayment,
            'totalPayment' => $totalPayment
        ];

        return view('pages.sales.debt_payment.nota', $data);
    }

    public function nota58($id)
    {
        $sales = Sales::query()
            ->with('customer')
            ->where('id', $id)
            ->first();

        $salesPayment = SalesPayment::where('sale_id', $id)->get();
        $totalPayment = SalesPayment::where('sale_id', $id)->sum('nominal_payment');
        
        $data = [
            'company'   => profileCompany(),
            'sales'     => $sales,
            'salesPayment' => $salesPayment,
            'totalPayment' => $totalPayment
        ];

        return view('pages.sales.debt_payment.nota58', $data);
    }

    public function nota80($id)
    {
        $sales = Sales::query()
            ->with('customer')
            ->where('id', $id)
            ->first();

        $salesPayment = SalesPayment::where('sale_id', $id)->get();
        $totalPayment = SalesPayment::where('sale_id', $id)->sum('nominal_payment');
        
        $data = [
            'company'   => profileCompany(),
            'sales'     => $sales,
            'salesPayment' => $salesPayment,
            'totalPayment' => $totalPayment
        ];

        return view('pages.sales.debt_payment.nota80', $data);
    }
}
