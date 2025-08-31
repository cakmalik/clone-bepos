<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('pages.payment_method.index', compact('paymentMethods'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name_payment' => 'required',
            'transaction_fees' => 'required',
        ]);

        if (PaymentMethod::where('name', $request->name_payment)->first()) {
            return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran sudah ada');
        }

        try {
            DB::beginTransaction();

            $paymentMethod = new PaymentMethod();
            $paymentMethod->name = $request->name_payment;
            $paymentMethod->transaction_fees = $request->transaction_fees;
            $paymentMethod->save();
            DB::commit();
            return redirect()->route('paymentMethod.index')->with('success', 'Metode Pembayaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran gagal ditambahkan');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_payment' => 'required',
            'transaction_fees' => 'required',
        ]);

        if (PaymentMethod::where('name', $request->name_payment)->first() && $request->name_payment != PaymentMethod::find($id)->name) {
            return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran sudah ada');
        }

        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethod::find($id);
            $paymentMethod->name = $request->name_payment;
            $paymentMethod->transaction_fees = $request->transaction_fees;
            $paymentMethod->save();
            DB::commit();
            return redirect()->route('paymentMethod.index')->with('success', 'Metode Pembayaran berhasil diubah');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran gagal diubah');
        }
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        
        DB::beginTransaction();
        try {


            if ($paymentMethod->status == 'active') {
                return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran tidak bisa dihapus, status aktif!');
            }

            if ($paymentMethod->sale) {
                return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran tidak bisa dihapus, sudah digunakan transaksi!');
            }

            $paymentMethod->delete();
            DB::commit();
            return redirect()->route('paymentMethod.index')->with('success', 'Metode Pembayaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('paymentMethod.index')->with('error', 'Metode Pembayaran gagal dihapus');
        }
    }

    public function toggleStatus($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethodName = $paymentMethod->name;
        $paymentMethod->is_active = !$paymentMethod->is_active;
        $paymentMethod->save();

        return redirect()->back()->with('success', 'Metode Pembayaran ' . $paymentMethodName . ' ' . ($paymentMethod->is_active ? 'Aktif' : 'Non Aktif'));
    }

}
