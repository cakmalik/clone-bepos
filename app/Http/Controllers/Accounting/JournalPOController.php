<?php

namespace App\Http\Controllers\Accounting;

use Exception;
use Carbon\Carbon;
use App\Models\JournalType;
use Illuminate\Http\Request;
use App\Models\JournalNumber;
use App\Models\InvoicePayment;
use App\Models\JournalAccount;
use App\Models\PurchaseInvoice;
use App\Models\JournalTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserOutlet;

class JournalPOController extends Controller
{
    public function index()
    {
        $purchaseInvices = PurchaseInvoice::where('is_done', true)->with('invoicePayments', 'purchase.supplier')->get();

        return view('pages.accounting.journal_po.index', ['purchaseInvices' => $purchaseInvices]);
    }

    public function create($id)
    {
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();
        $invoicePayment = InvoicePayment::where('id', $id)->with('purchase_invoice_new')->first();

        $debit = JournalAccount::where('name', 'HUTANG TOP')->first();
        return view('pages.accounting.journal_po.create', ['jurnal_account' => $jurnal_account, 'debit' => $debit, 'invoicePayment' => $invoicePayment]);
    }


    public function store(Request $request)
    {

        DB::beginTransaction();
        $journal_type = JournalType::where('name', 'PEMBELIAN')->first();
        $outlet = UserOutlet::where('user_id', Auth()->user()->id)->first();
        try {
            $journal_number = JournalNumber::create([
                'code' => Carbon::now()->format('YmdHis'),
                'journal_type_id' => $journal_type->id,
                'date' => Carbon::now(),
                'outlet_id' =>  $outlet->id,
                'user_id' => Auth()->user()->id,
                'is_done' => false
            ]);

            if ($request->journal_account_debit) {
                JournalTransaction::create([
                    'code' => Carbon::now()->format('YmdHis') . '-DEBIT',
                    'journal_number_id' => $journal_number->id,
                    'type' => 'debit',
                    'journal_account_id' => $request->journal_account_debit,
                    'nominal' => str_replace(str_split('Rp. '), '', $request->nominal),
                    'description' => $request->description,
                ]);
            }
            if ($request->journal_account_kredit) {
                JournalTransaction::create([
                    'code' => Carbon::now()->format('YmdHis') . '-KREDIT',
                    'journal_number_id' => $journal_number->id,
                    'type' => 'credit',
                    'journal_account_id' => $request->journal_account_kredit,
                    'nominal' => str_replace(str_split('Rp. '), '', $request->nominal),
                    'description' => $request->description,
                ]);
            }

            InvoicePayment::where('id', $request->invoice_id)->update([
                'journal_number_id' => $journal_number->id
            ]);

            DB::commit();
            return redirect('/accounting/journal_po')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }
}
