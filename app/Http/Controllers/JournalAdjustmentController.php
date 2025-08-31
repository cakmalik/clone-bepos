<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\UserOutlet;
use App\Models\JournalType;
use App\Models\StockOpname;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\JournalNumber;
use App\Models\JournalAccount;
use App\Models\StockOpnameDetail;
use App\Models\JournalTransaction;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\DB;

class JournalAdjustmentController extends Controller
{
    public function index()
    {
        $adjustment = StockOpname::where(['journal_number_id' => NULL, 'status' => 'adjustment'])->with('user')->get();
        return view('pages.accounting.journal_adjustment.index', ['title' => 'Jurnal Adjustment', 'adjustment' => $adjustment]);
    }

    public function create($id)
    {

        $adjustment = StockOpname::where('id', $id)->withSum('adjustment_detail', 'adjustment_nominal_value')->first();

        $opname = StockOpname::where('code', $adjustment->ref_code)->first();
        $opnameDetail = StockOpnameDetail::where('ref_code', $adjustment->code)->with('product')->get();
        $jurnal_account = JournalAccount::orderby('name', 'asc')->with('journalAccountType')->get();

        $journal_number = JournalNumber::where('code', $adjustment->code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();


        if ($journal_number != null) {
            $total_debit = JournalTransaction::where('journal_number_id', $journal_number->id)->where('type', 'debit')->sum('nominal');
            $total_kredit = JournalTransaction::where('journal_number_id', $journal_number->id)->where('type', 'credit')->sum('nominal');
        } else {
            $total_debit = 0;
            $total_kredit = 0;
        }

        return view('pages.accounting.journal_adjustment.create', ['title' => 'Tambah Jurnal Adjustment', 'adjustment' => $adjustment, 'jurnal_account' => $jurnal_account, 'journal_number' => $journal_number, 'total_debit' => $total_debit, 'total_kredit' => $total_kredit, 'opnameDetail' => $opnameDetail]);
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        $request->validate([
            'journal_account_debit' => 'required',
            'journal_account_kredit' => 'required',
            'nominal' => 'required',
            'description' => 'required'
        ], [], [
            'journal_account_debit' => 'Debit',
            'journal_account_kredit' => 'Kredit',
            'nominal' => 'Nominal',
            'description' => 'Deskripsi'
        ]);

        $adjustment = StockOpname::where('id', $request->id)->withSum('adjustment_detail', 'adjustment_nominal_value')->first();
        $journal_type = JournalType::where('name', 'PENYESUAIAN')->first();
        $journal_number_old = JournalNumber::where('code', $adjustment->code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();
        $outlet = UserOutlet::where('user_id', Auth()->user()->id)->first();
        try {
            if (!$journal_number_old) {
                $journal_number = JournalNumber::create([
                    'code' => $adjustment->code,
                    'journal_type_id' => $journal_type->id,
                    'date' => Carbon::now(),
                    'outlet_id' => $outlet->id,
                    'user_id' => Auth()->user()->id,
                    'is_done' => false,
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
                        'journal_number_id' => $journal_number_old->id,
                        'type' => 'debit',
                        'journal_account_id' => $request->journal_account_debit,
                        'nominal' => str_replace(str_split('Rp.'), '', $request->nominal),
                        'description' => $request->description,
                    ]);
                }
                if ($request->journal_account_kredit) {
                    JournalTransaction::create([
                        'code' => Carbon::now()->format('YmdHis') . '-KREDIT',
                        'journal_number_id' => $journal_number_old->id,
                        'type' => 'credit',
                        'journal_account_id' => $request->journal_account_kredit,
                        'nominal' => str_replace(str_split('Rp.'), '', $request->nominal),
                        'description' => $request->description,
                    ]);
                }
            }



            $adj = StockOpname::where('id', $request->id)->withSum('adjustment_detail', 'adjustment_nominal_value')->first();
            $so = StockOpname::where('code', $adj->ref_code)->first();
            $stockOpnameDetail = StockOpnameDetail::where('stock_opname_id', $so->id)->where('qty_selisih', '!=', 0)->with('product')->get();






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

        $adjustment = StockOpname::where('id', $request->id)->withSum('adjustment_detail', 'adjustment_nominal_value')->first();
        $journal_number_old = JournalNumber::where('code', $adjustment->code)->where('is_done', false)->with('journalTransaction', 'journalTransaction.journalAccount')->first();
        try {


            $adj = StockOpname::where('id', $request->id)->withSum('adjustment_detail', 'adjustment_nominal_value')->first();
            $so = StockOpname::where('code', $adj->ref_code)->first();
            $stockOpnameDetail = StockOpnameDetail::where('stock_opname_id', $so->id)->where('qty_selisih', '!=', 0)->with('product')->get();

            if ($adj->inventory_id) {
                $inventory_id = $adj->inventory_id;
                $outlet_id = NULL;
            } else {
                $inventory_id = NULL;
                $outlet_id = $adj->outlet_id;
            }

            StockOpname::where('id', $request->id)->update([
                'journal_number_id' => $journal_number_old->id
            ]);

            JournalNumber::where('id', $journal_number_old->id)->update([
                'is_done' => true
            ]);

            foreach ($stockOpnameDetail as $sod) {

                if ($request->inventory_id) {
                    $product_stock = ProductStock::where('product_id', $sod->product_id)->where('inventory_id', $inventory_id)->first();
                } else {
                    $product_stock = ProductStock::where('product_id', $sod->product_id)->where('outlet_id', $outlet_id)->first();
                }

                if ($product_stock) {
                    ProductStock::where('id', $product_stock->id)->update([
                        'stock_current' =>  $sod->qty_system + $sod->qty_adjustment,
                    ]);


                    $ps_after = ProductStock::where('id', $product_stock->id)->first();

                    if ($product_stock->stock_current < $ps_after->stock_current) {
                        $action_type = 'PLUS';
                        $description = 'PENAMBAHAN';
                    } else {
                        $action_type = 'MINUS';
                        $description = 'PENGURANGAN';
                    }

                    ProductStockHistory::create([
                        'document_number' => $adj->code,
                        'history_date' => Carbon::now(),
                        'action_type' => $action_type,
                        'user_id' => Auth()->user()->id,
                        'product_id' => $product_stock->product_id,
                        'inventory_id' => $product_stock->inventory_id ?? NULL,
                        'outlet_id' => $product_stock->outlet_id ?? NULL,
                        'stock_change' => $sod->qty_adjustment ?? 0,
                        'stock_before' => $product_stock->stock_current,
                        'stock_after' =>  $ps_after->stock_current ?? 0,
                        'desc' => 'Adjustment NO : ' . $adj->code . ' ' . $description  . ' Stok Opname No: ' . $adj->ref_code
                    ]);
                } else {
                    ProductStock::create([
                        'product_id' => $sod->product_id,
                        'inventory_id' => $inventory_id ?? NULL,
                        'outlet_id' => $outlet_id ?? NULL,
                        'stock_current' => $sod->qty_system + $sod->qty_adjustment,
                    ]);



                    ProductStockHistory::create([
                        'document_number' => $adj->code,
                        'history_date' => Carbon::now(),
                        'action_type' => 'PLUS',
                        'user_id' => Auth()->user()->id,
                        'product_id' =>  $sod->product_id,
                        'inventory_id' => $product_stock->inventory_id ?? NULL,
                        'outlet_id' => $product_stock->outlet_id ?? NULL,
                        'stock_change' => $sod->qty_adjustment ?? 0,
                        'stock_before' => 0,
                        'stock_after' => $sod->qty_adjustment ?? 0,
                        'desc' => 'Adjustment NO : ' . $adj->code   . ' Stok Opname No: ' . $adj->ref_code
                    ]);
                }
            }



            DB::commit();
            return redirect('accounting/journal_adjustment')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }



    public function destroy($id)
    {
        $JT = JournalTransaction::find($id);
        $code =  str_replace('-DEBIT', '', $JT->code);
        $kredit = $code . '-KREDIT';
        JournalTransaction::where('code', $JT->code)->delete();
        JournalTransaction::where('code', $kredit)->delete();

        return redirect()->back()->withSuccess('Sukses di Hapus!');
    }
}
