<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalType;
use Illuminate\Http\Request;

class JournalTypeController extends Controller
{
    public function index(Request $request)
    {
        $journalTypes = JournalType::orderBy('name')->get();
        return view('pages.accounting.journal_type.index', compact('journalTypes'));
    }

    public function create()
    {
        return view('pages.accounting.journal_type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required|max:255',
        ], [], [
            'name' => 'Nama'
        ]);

        try {
            $journalType = new JournalType();
            $journalType->name = $request->name;
            $journalType->save();

            return redirect()->route('journal_type.index')
                ->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            return redirect()->route('journal_type.create')
                ->with('error', 'Data gagal disimpan');
        }
    }

    public function edit($id)
    {
        $journalType = JournalType::findOrFail($id);
        return view('pages.accounting.journal_type.edit', [
            'journalType' => $journalType,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'string|required|max:255',
        ], [], [
            'name' => 'Nama'
        ]);

        try {
            $journalType = JournalType::findOrFail($id);
            $journalType->name = $validated['name'];
            $journalType->save();

            return redirect()->route('journal_type.index')
                ->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('journal_type.edit', $id)
                ->with('error', 'Data gagal diubah');
        }
    }

    public function destroy(JournalType $journalType)
    {
        try {
            $journalType->delete();

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
