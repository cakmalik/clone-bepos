<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalAccountType;
use Illuminate\Http\Request;

class JournalAccountTypeController extends Controller
{
    public $transaction_type = ['debit', 'credit'];
    public $position = ['neraca', 'laba rugi'];

    public function index(Request $request)
    {
        $journal_account_types = JournalAccountType::orderBy('name')->get();
        return view('pages.accounting.journal_account_type.index', compact('journal_account_types'));
    }

    public function create()
    {
        return view('pages.accounting.journal_account_type.create', ['transaction_type' => $this->transaction_type]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required|max:255',
            'transaction_type' => 'required|in:debit,credit',
            'position' => 'required',
        ], [], [
            'name' => 'Nama',
            'transaction_type' => 'Tipe Transaksi',
            'position' => 'Posisi',
        ]);

        try {
            $journal_account_type = new JournalAccountType();
            $journal_account_type->name = $request->name;
            $journal_account_type->transaction_type = $request->transaction_type;
            $journal_account_type->position = $request->position;
            $journal_account_type->save();

            return redirect()->route('journal_account_type.index')
                ->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            return redirect()->route('journal_account_type.create')
                ->with('error', 'Data gagal disimpan');
        }
    }

    public function edit($id)
    {
        $journalAccountType = JournalAccountType::findOrFail($id);
        return view('pages.accounting.journal_account_type.edit', [
            'journalAccountType' => $journalAccountType,
            'transaction_type' => $this->transaction_type,
        ]);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'string|required|max:255',
            'transaction_type' => 'required|in:debit,credit',
            'position' => 'required',
        ], [], [
            'name' => 'Nama',
            'transaction_type' => 'Tipe Transaksi',
            'position' => 'Posisi',
        ]);
        try {
            $journal_account_type = JournalAccountType::findOrFail($id);
            $journal_account_type->name = $request->name;
            $journal_account_type->transaction_type = $request->transaction_type;
            $journal_account_type->position = $request->position;
            $journal_account_type->save();

            return redirect()->route('journal_account_type.index')
                ->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('journal_account_type.edit', $id)
                ->with('error', 'Data gagal diubah');
        }
    }

    public function destroy(JournalAccountType $journal_account_type)
    {
        try {
            $journal_account_type->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Data gagal dihapus',
            ], 500);
        }
    }
}
