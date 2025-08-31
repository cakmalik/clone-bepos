<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Sales;
use App\Models\UserOutlet;
use App\Models\JournalType;
use Illuminate\Http\Request;
use App\Models\JournalNumber;
use App\Models\JournalAccount;
use App\Models\JournalTransaction;
use Illuminate\Support\Facades\DB;

class JournalReturSalesController extends Controller
{
    public function index()
    {
        $sales = Sales::with('customer', 'outlet')->where('is_retur', true)->where('journal_number_id', NULL)->where('status', 'success')->get();
        return view('pages.accounting.journal_retur_sales.index', ['title' => 'Retur Penjualan (Nilai Barang) Belum Masuk Jurnal', 'sales' => $sales]);
    }

    public function create($id)
    {
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();
        $sales = Sales::with('customer', 'outlet')->where('id', $id)->first();

        $journal_number = JournalNumber::where('code', $sales->sale_code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();

        if ($journal_number != null) {
            $total_debit = JournalTransaction::where('journal_number_id', $journal_number->id)->where('type', 'debit')->sum('nominal');
            $total_kredit = JournalTransaction::where('journal_number_id', $journal_number->id)->where('type', 'credit')->sum('nominal');
        } else {
            $total_debit = 0;
            $total_kredit = 0;
        }

        return view('pages.accounting.journal_retur_sales.create', ['title' => 'Jurnal Retur Penjualan', 'sales' => $sales, 'jurnal_account' => $jurnal_account, 'journal_number' => $journal_number, 'total_debit' => $total_debit, 'total_kredit' => $total_kredit]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'journal_account_debit' => 'required',
            'journal_account_kredit' => 'required',
            'nominal' => 'required',
        ], [], [
            'journal_account_debit' => 'Debit',
            'journal_account_kredit' => 'Kredit',
            'nominal' => 'Nominal',
        ]);

        $sales = Sales::with('customer', 'outlet')->where('id', $request->id)->first();
        $journal_type = JournalType::where('name', 'RETUR-JUAL')->first();
        $journal_number2 = JournalNumber::where('code', $sales->sale_code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();
        $outlet = UserOutlet::where('user_id', Auth()->user()->id)->first();

        try {
            if (!$journal_number2) {
                $journal_number = JournalNumber::create([
                    'code' => $sales->sale_code,
                    'journal_type_id' => $journal_type->id,
                    'date' => Carbon::now(),
                    'outlet_id' => $outlet->id,
                    'user_id' => Auth()->user()->id,
                    'is_done' => false
                ]);

                if ($request->journal_account_debit) {
                    JournalTransaction::create([
                        'code' => Carbon::now()->format('YmdHis') . '-DEBIT',
                        'journal_number_id' => $journal_number->id,
                        'type' => 'debit',
                        'journal_account_id' => $request->journal_account_debit,
                        'nominal' => str_replace(str_split('Rp.'), '', $request->nominal),
                        'description' => $request->description,
                    ]);
                }
                if ($request->journal_account_kredit) {
                    JournalTransaction::create([
                        'code' => Carbon::now()->format('YmdHis') . '-KREDIT',
                        'journal_number_id' => $journal_number->id,
                        'type' => 'credit',
                        'journal_account_id' => $request->journal_account_kredit,
                        'nominal' => str_replace(str_split('Rp.'), '', $request->nominal),
                        'description' => $request->description,
                    ]);
                }
            } else {

                if ($request->journal_account_debit) {
                    JournalTransaction::create([
                        'code' => Carbon::now()->format('YmdHis') . '-DEBIT',
                        'journal_number_id' => $journal_number2->id,
                        'type' => 'debit',
                        'journal_account_id' => $request->journal_account_debit,
                        'nominal' => str_replace(str_split('Rp.'), '', $request->nominal),
                        'description' => $request->description,
                    ]);
                }
                if ($request->journal_account_kredit) {
                    JournalTransaction::create([
                        'code' => Carbon::now()->format('YmdHis') . '-KREDIT',
                        'journal_number_id' => $journal_number2->id,
                        'type' => 'credit',
                        'journal_account_id' => $request->journal_account_kredit,
                        'nominal' => str_replace(str_split('Rp.'), '', $request->nominal),
                        'description' => $request->description,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }

    public function finish(Request $request)
    {
        DB::beginTransaction();

        try {

            Sales::where('id', $request->id)->update([
                'journal_number_id' => $request->journal_number_id
            ]);

            DB::commit();
            return redirect('/accounting/journal_retur_sales')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }
}
