<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\JournalType;
use Illuminate\Http\Request;
use App\Models\JournalNumber;
use App\Models\InvoicePayment;
use App\Models\JournalAccount;
use App\Models\PurchaseInvoice;
use App\Models\JournalTransaction;
use App\Models\UserOutlet;
use Illuminate\Support\Facades\DB;

class JournalReturPurchaseController extends Controller
{
    public function index()
    {
        $purchase = Purchase::where('journal_number_id', NULL)->where('purchase_type', 'Purchase Retur')->with('user')->get();

        return view('pages.accounting.journal_retur_purchase.index', ['purchase' => $purchase]);
    }

    public function create($id)
    {
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();
        $purchase = Purchase::where('id', $id)->where('purchase_type', 'Purchase Retur')->with('user')->first();
        $nominal = Purchase::where('id', $id)->where('purchase_type', 'Purchase Retur')->withSum('purchaseDetails', 'subtotal')->first();

        $PO = Purchase::where('code', $purchase->ref_code)->where('purchase_type', 'Purchase Order')->first();

        $purchase_invoices = PurchaseInvoice::where('purchase_id', '!=', $PO->id)->where('journal_number_id', NULL)->with('purchase.supplier')->get();


        $journal_number = JournalNumber::where('code', $purchase->code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();

        if ($journal_number != null) {
            $total_debit = JournalTransaction::where('journal_number_id', $journal_number->id)->where('type', 'debit')->sum('nominal');
            $total_kredit = JournalTransaction::where('journal_number_id', $journal_number->id)->where('type', 'credit')->sum('nominal');
        } else {
            $total_debit = 0;
            $total_kredit = 0;
        }
        return view('pages.accounting.journal_retur_purchase.create', ['jurnal_account' => $jurnal_account, 'purchase' => $purchase, 'journal_number' => $journal_number, 'total_debit' =>  $total_debit, 'total_kredit' => $total_kredit, 'nominal' => $nominal, 'purchase_invoices' => $purchase_invoices]);
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

        $purchase = Purchase::where('id', $request->id)->where('purchase_type', 'Purchase Retur')->with('user')->first();
        $journal_type = JournalType::where('name', 'RETUR-BELI')->first();
        $journal_number2 = JournalNumber::where('code', $purchase->code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();
        $outlet = UserOutlet::where('user_id', Auth()->user()->id)->first();

        try {
            if (!$journal_number2) {
                $journal_number = JournalNumber::create([
                    'code' => $purchase->code,
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


    public function retur(Request $request)
    {


        DB::beginTransaction();

        try {

            $purchase = Purchase::where('id', $request->id)->where('purchase_type', 'Purchase Retur')->with('user', 'supplier')->first();
            $purchase_invoices = PurchaseInvoice::where('id', $request->purchase_invoice_id)->first();


            if ($request->retur_value == 2) {

                InvoicePayment::create([
                    'code' => $purchase_invoices->code,
                    'purchase_invoice_id' => $purchase_invoices->id,
                    'payment_date' => Carbon::now(),
                    'payment_type' => 'CASH',
                    'user_id' => Auth()->user()->id,
                    'description' => 'Alokasi Nilai Retur Mengurangi Utang ' . $purchase_invoices->code . '-' . $purchase->supplier->name,
                    'nominal_payment' => $request->nominal,

                ]);


                PurchaseInvoice::where('id', $request->purchase_invoice_id)->update([
                    'nominal_paid' => $purchase_invoices->nominal_paid + $request->nominal,

                ]);


                Purchase::where('id', $request->id)->update([
                    'journal_number_id' => $request->journal_number_id
                ]);
            }

            DB::commit();
            return redirect('/accounting/journal_retur_purchase')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }
}
