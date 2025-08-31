<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Models\JournalAccount;
use App\Models\JournalAccountType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class JournalAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $journalAccount = JournalAccount::with('journalAccountType')->orderBy('created_at', 'DESC')->get();
        return view('pages.accounting.journal_account.index', ['journalAccount' =>   $journalAccount]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = JournalAccountType::all();
        return view('pages.accounting.journal_account.create', ['type' => $type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'journal_account_type_id' => 'required'
        ], [
            'code' => 'Kode',
            'name' => 'Nama',
            'journal_account_type_id' => 'Jenis Akun'
        ]);

        try {
            $journalAccount = new JournalAccount();
            $journalAccount->code = $request->code;
            $journalAccount->name = $request->name;
            $journalAccount->journal_account_type_id = $request->journal_account_type_id;
            $journalAccount->save();
            DB::commit();
            return redirect()->route('journal_account.index')->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('journal_account.create')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $journalAccount = JournalAccount::with('journalAccountType')->find($id);
        $type = JournalAccountType::all();
        return view('pages.accounting.journal_account.edit', ['journalAccount' => $journalAccount, 'type' => $type]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'journal_account_type_id' => 'required'
        ], [
            'code' => 'Kode',
            'name' => 'Nama',
            'journal_account_type_id' => 'Jenis Akun'
        ]);

        try {
            $journalAccount = JournalAccount::find($id);
            $journalAccount->code = $request->code;
            $journalAccount->name = $request->name;
            $journalAccount->journal_account_type_id = $request->journal_account_type_id;
            $journalAccount->save();
            DB::commit();
            return redirect()->route('journal_account.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('journal_account.edit')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(JournalAccount $journalAccount)
    {
        JournalAccount::destroy($journalAccount->id);
        return redirect()->back()->withSuccess('Sukses di Hapus!');
    }
}
