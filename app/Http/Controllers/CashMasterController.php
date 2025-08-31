<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\CashMaster;
use Illuminate\Http\Request;
use App\Models\JournalAccount;
use App\Models\JournalSetting;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CashMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response |
     */
    public function index(Request $request)
    {


        if ($request->ajax()) {
            $CashMaster = CashMaster::with('journal_setting.debit_account', 'journal_setting.credit_account')
                ->where(function ($query) use ($request) {
                    if ($request->cash_type_filter != 'SEMUA') {
                        $query->where('cash_type', $request->cash_type_filter);
                    }
                })->get();

            return DataTables::of($CashMaster)
                ->addIndexColumn()
                ->addColumn('action', function ($CashMaster) {
                    return '<a href="/accounting/cash_master/' . $CashMaster->id . '/edit" class="btn btn-success btn-sm py-2 px-3"><li class="fas fa-edit"></li></a>
                                <a class="btn btn-danger btn-sm py-2 px-3" onclick="cashMaster(' . $CashMaster->id . ')"><li class="fas fa-trash"></li></a>';
                })->rawColumns(['action'])->make(true);
        }

        return view('pages.accounting.cash_master.index', ['title' => 'Master BKK/BKM']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();
        return view('pages.accounting.cash_master.create', ['title' => 'Buat Master BKK/BKM', 'jurnal_account' => $jurnal_account]);
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
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'cash_type' => 'required',
        ], [], [
            'code' => 'Kode BK',
            'name' => 'Nama Item BK',
            'debit_account_id' => 'Jurnal Akun Debit',
            'credit_account_id' => 'Jurnal Akun Kredit',
            'cash_type' => 'BKK/BKM'
        ]);

        try {

            $JS = JournalSetting::create([
                'name' => $request->name,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
            ]);

            CashMaster::create([
                'code' => $request->code,
                'name' => $request->name,
                'cash_type' => $request->cash_type,
                'journal_setting_id' => $JS->id,
            ]);



            DB::commit();
            return redirect('/accounting/cash_master')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CashMaster  $cashMaster
     * @return \Illuminate\Http\Response
     */
    public function show(CashMaster $cashMaster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CashMaster  $cashMaster
     * @return \Illuminate\Http\Response
     */
    public function edit(CashMaster $cashMaster)
    {
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();
        $cashMaster = CashMaster::where('id', $cashMaster->id)->with('journal_setting.debit_account', 'journal_setting.credit_account')->first();

        $cash_type = ['KAS-MASUK', 'KAS-KELUAR'];

        return view('pages.accounting.cash_master.edit', ['title' => 'Edit Master BKK/BKM', 'jurnal_account' => $jurnal_account, 'cashMaster' => $cashMaster, 'cash_type' => $cash_type]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CashMaster  $cashMaster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CashMaster $cashMaster)
    {


        DB::beginTransaction();

        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'cash_type' => 'required',
        ], [], [
            'code' => 'Kode BK',
            'name' => 'Nama Item BK',
            'debit_account_id' => 'Jurnal Akun Debit',
            'credit_account_id' => 'Jurnal Akun Kredit',
            'cash_type' => 'BKK/BKM'
        ]);

        try {

            JournalSetting::where('id', $cashMaster->journal_setting_id)->update([
                'name' => $request->name,
                'debit_account_id' => $request->debit_account_id,
                'credit_account_id' => $request->credit_account_id,
            ]);


            CashMaster::where('id', $cashMaster->id)->update([
                'code' => $request->code,
                'name' => $request->name,
                'cash_type' => $request->cash_type,
                'journal_setting_id' => $cashMaster->journal_setting_id,
            ]);

            DB::commit();
            return redirect('accounting/cash_master')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CashMaster  $cashMaster
     * @return \Illuminate\Http\Response
     */
    public function destroy(CashMaster $cashMaster)
    {
        DB::beginTransaction();
        try {
            JournalSetting::destroy($cashMaster->journal_setting_id);
            CashMaster::destroy($cashMaster->id);

            DB::commit();
            return redirect()->back()->withSuccess('Sukses di Hapus!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Hapus!');
        }
    }
}
