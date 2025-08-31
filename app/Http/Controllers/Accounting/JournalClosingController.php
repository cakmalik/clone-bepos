<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalClosing;
use App\Models\JournalNumber;
use App\Models\JournalTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JournalClosingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;
            $journalTransaction = JournalTransaction::with('journalAccount', 'journalNumber.journalType', 'journalNumber.outlet')
                ->whereHas('journalNumber', function ($q) use ($startDate, $endDate) {
                    $q->whereNull('journal_closing_id')
                        ->whereDate('date', '<=', $endDate);
                    if ($startDate) {
                        $q->whereDate('date', '>=', $startDate);
                    }
                })
                ->orderBy('created_at', 'DESC')
                ->get();
            return DataTables::of($journalTransaction)
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.accounting.journal_closing.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $journalTransaction = JournalClosing::create([
                'code' => journalClosingCode(),
                'name' => $validated['name'],
                'date' => $validated['end_date'],
            ]);

            JournalNumber::whereNull('journal_closing_id')
                ->whereDate('date', '>=', $validated['start_date'])
                ->whereDate('date', '<=', $validated['end_date'])
                ->update(['journal_closing_id' => $journalTransaction->id]);

            DB::commit();
            return redirect()->route('journal_closing.index')
                ->with('success', 'Data Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('journal_closing.index')->with('error', 'Data Gagal Disimpan');
        }
    }
}
