<?php

namespace App\Http\Controllers\Accounting;

use Carbon\Carbon;
use App\Models\Outlet;
use App\Models\JournalType;
use Illuminate\Http\Request;
use App\Models\JournalNumber;
use App\Models\JournalAccount;
use Yajra\DataTables\DataTables;
use App\Models\JournalTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProfilCompany;

class JournalTransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;
            $journalTransaction = JournalTransaction::with('journalAccount', 'journalNumber.journalType', 'journalNumber.outlet')
                ->whereHas('journalNumber', function ($q) use ($startDate, $endDate) {
                    $q->whereDate('date', '>=', $startDate)
                        ->whereDate('date', '<=', $endDate);
                })
                ->orderBy('created_at', 'DESC')
                ->get();
            return DataTables::of($journalTransaction)
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.accounting.journal_transaction.index');
    }

    public function create()
    {


        $journalAccounts = JournalAccount::all();
        $journalTypes = JournalType::all();
        $outlets = Outlet::all();
        $activeOutlet = getOutletActive();
        return view('pages.accounting.journal_transaction.create', [
            'journalAccounts' => $journalAccounts,
            'journalTypes' => $journalTypes,
            'outlets' => $outlets,
            'activeOutlet' => $activeOutlet,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'date',
            'outlet_id' => 'required|exists:outlets,id',
            'journal_type_id' => 'required|exists:journal_types,id',
            'journal_debit_account_id' => 'required|exists:journal_accounts,id',
            'journal_credit_account_id' => 'required|exists:journal_accounts,id',
            'nominal' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $journalNumber = new JournalNumber();
            $journalNumber->journal_type_id = $validated['journal_type_id'];
            $journalNumber->code = journalNumberCode();
            $journalNumber->date = $validated['date'] . ' ' . Carbon::now()->format('H:i:s');
            $journalNumber->user_id = auth()->user()->id;
            $journalNumber->outlet_id = $validated['outlet_id'];
            $journalNumber->save();

            JournalTransaction::create([
                'code' => Carbon::now()->format('YmdHis') . '-DEBIT',
                'journal_account_id' => $validated['journal_debit_account_id'],
                'journal_number_id' => $journalNumber->id,
                'type' => 'debit',
                'description' => $validated['description'],
                'nominal' => $validated['nominal'],
            ]);

            JournalTransaction::create([
                'code' => Carbon::now()->format('YmdHis') . '-KREDIT',
                'journal_account_id' => $validated['journal_credit_account_id'],
                'journal_number_id' => $journalNumber->id,
                'type' => 'credit',
                'description' => $validated['description'],
                'nominal' => $validated['nominal'],
            ]);
            DB::commit();
            return redirect()->route('journal_transaction.index')->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Data gagal disimpan');
        }
    }

    public function journalTransactionPrint(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Handle error if startDate or endDate is null
        if (!$startDate || !$endDate) {
            return redirect()->back()->withInput()->with('error', 'Tanggal awal atau akhir tidak valid');
        }

        // Handle error if startDate is greater than endDate
        if ($startDate > $endDate) {
            return redirect()->back()->withInput()->with('error', 'Tanggal awal harus lebih kecil atau sama dengan tanggal akhir');
        }

        $journalTransaction = JournalTransaction::with('journalAccount', 'journalNumber.journalType', 'journalNumber.outlet')
            ->whereHas('journalNumber', function ($q) use ($startDate, $endDate) {
                $q->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('pages.accounting.journal_transaction.preview', [
            'journalTransaction' => $journalTransaction,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function previewData(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;


        $journalTransaction = JournalTransaction::with('journalAccount', 'journalNumber.journalType', 'journalNumber.outlet', 'journalNumber.inventory')
            ->whereHas('journalNumber', function ($q) use ($startDate, $endDate) {
                $q->where('date', '>=', startOfDay($startDate))
                    ->where('date', '<=', endOfDay($endDate));
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $company = ProfilCompany::where('status', 'active')->first();

        return view('pages.accounting.journal_transaction.print', [
            'transaction' => $journalTransaction,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company' => $company,
        ]);
    }
}
