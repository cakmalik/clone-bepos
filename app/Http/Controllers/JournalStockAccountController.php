<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\JournalAccount;
use App\Models\JournalSetting;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Models\JournalCategoryProduct;

class JournalStockAccountController extends Controller
{

    public function index()
    {
        $journal_category_product = JournalCategoryProduct::with(
            'journal_settings_trans',
            'journal_settings_buy',
            'journal_settings_invoice',
            'category',
            'journal_settings_trans.debit_account',
            'journal_settings_trans.credit_account',
            'journal_settings_buy.debit_account',
            'journal_settings_buy.credit_account',
            'journal_settings_invoice.debit_account',
            'journal_settings_invoice.credit_account'
        )
            ->get();
        return view('pages.accounting.journal_account_stock.index', ['title' => 'Set Akun Barang', 'journal_category_product' => $journal_category_product]);
    }


    public function create()
    {

        $category = ProductCategory::whereDoesntHave('journal_set')->orderBy('name')->get();
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();

        return view('pages.accounting.journal_account_stock.create', ['category' => $category, 'jurnal_account' => $jurnal_account]);
    }


    public function store(Request $request)
    {

        DB::beginTransaction();

        $request->validate([
            'category_id' => 'required',
            'sales_debit_account_id' => 'required',
            'sales_credit_account_id' => 'required',
            'purchase_debit_account_id' => 'required',
            'purchase_credit_account_id' => 'required',
            'inv_debit_account_id' => 'required',
            'inv_credit_account_id' => 'required',
        ], [], [
            'category_id' => 'Nama Kategori ',
            'sales_debit_account_id' => 'Debit Penjualan ',
            'sales_credit_account_id' => 'Kredit Penjualan ',
            'purchase_debit_account_id' => 'Debit Pembelian ',
            'purchase_credit_account_id' => 'Kredit Pembelian ',
            'inv_debit_account_id' => 'Debit INV.PO ',
            'inv_credit_account_id' => 'Kredit INV.PO ',

        ]);

        try {
            $category = ProductCategory::findOrFail($request->category_id);

            $category_name = Str::replace(' ', '_', $category->name);

            $trans =  JournalSetting::create([
                'name' => 'SET_' .  $category_name . '_TRANS',
                'debit_account_id' => $request->sales_debit_account_id,
                'credit_account_id' => $request->sales_credit_account_id,
            ]);


            $buy =  JournalSetting::create([
                'name' => 'SET_' .  $category_name . '_BELI',
                'debit_account_id' => $request->purchase_debit_account_id,
                'credit_account_id' => $request->purchase_credit_account_id,
            ]);


            $inv =  JournalSetting::create([
                'name' => 'SET_' .  $category_name . '_INVOICE_PO',
                'debit_account_id' => $request->inv_debit_account_id,
                'credit_account_id' => $request->inv_credit_account_id,
            ]);



            JournalCategoryProduct::create([
                'product_category_id' => $request->category_id,
                'journal_setting_trans_id' => $trans->id,
                'journal_setting_buy_id' => $buy->id,
                'journal_setting_invoice_id' => $inv->id
            ]);



            DB::commit();
            return redirect('/accounting/journal_stock_account')->withSuccess('Sukses di Simpan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Simpan!');
        }
    }

    public function edit($id)
    {
        $category = ProductCategory::All();
        $jurnal_account = JournalAccount::orderBy('name', 'asc')->with('journalAccountType')->get();

        $journal_category_product = JournalCategoryProduct::where('id', $id)->with('journal_settings_trans', 'journal_settings_buy', 'journal_settings_invoice', 'category', 'journal_settings_trans.debit_account', 'journal_settings_trans.credit_account', 'journal_settings_buy.debit_account', 'journal_settings_buy.credit_account', 'journal_settings_invoice.debit_account', 'journal_settings_invoice.credit_account')->first();

        return view('pages.accounting.journal_account_stock.edit', ['title' => 'Edit Set Akun Barang', 'jurnal_account' => $jurnal_account, 'category' => $category, 'journal_category_product' => $journal_category_product]);
    }


    public function update(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'category_id' => 'required',
            'sales_debit_account_id' => 'required',
            'sales_credit_account_id' => 'required',
            'purchase_debit_account_id' => 'required',
            'purchase_credit_account_id' => 'required',
            'inv_debit_account_id' => 'required',
            'inv_credit_account_id' => 'required',
        ], [], [
            'category_id' => 'Nama Kategori ',
            'sales_debit_account_id' => 'Debit Penjualan ',
            'sales_credit_account_id' => 'Kredit Penjualan ',
            'purchase_debit_account_id' => 'Debit Pembelian ',
            'purchase_credit_account_id' => 'Kredit Pembelian ',
            'inv_debit_account_id' => 'Debit INV.PO ',
            'inv_credit_account_id' => 'Kredit INV.PO ',

        ]);

        try {

            $journal_category_product = JournalCategoryProduct::where('id', $request->id)->with('journal_settings_trans', 'journal_settings_buy', 'journal_settings_invoice', 'category', 'journal_settings_trans.debit_account', 'journal_settings_trans.credit_account', 'journal_settings_buy.debit_account', 'journal_settings_buy.credit_account', 'journal_settings_invoice.debit_account', 'journal_settings_invoice.credit_account')->first();

            $category = ProductCategory::findOrFail($request->category_id);

            $category_name = Str::replace(' ', '_', $category->name);

            $trans = JournalSetting::where('id', $journal_category_product->journal_setting_trans_id)->first();
            JournalSetting::where('id', $journal_category_product->journal_setting_trans_id)->update([
                'name' => 'SET_' .    $category_name . '_TRANS',
                'debit_account_id' => $request->sales_debit_account_id,
                'credit_account_id' => $request->sales_credit_account_id,
            ]);
            $transs = JournalSetting::where('id', $journal_category_product->journal_setting_trans_id)->first();


            $buy =  JournalSetting::where('id', $journal_category_product->journal_setting_buy_id)->first();
            JournalSetting::where('id', $journal_category_product->journal_setting_buy_id)->update([
                'name' => 'SET_' .   $category_name . '_BELI',
                'debit_account_id' => $request->purchase_debit_account_id,
                'credit_account_id' => $request->purchase_credit_account_id,
            ]);
            $buys =  JournalSetting::where('id', $journal_category_product->journal_setting_buy_id)->first();



            $inv =  JournalSetting::where('id', $journal_category_product->journal_setting_invoice_id)->first();
            JournalSetting::where('id', $journal_category_product->journal_setting_invoice_id)->update([
                'name' => 'SET_' .   $category_name . '_INVOICE_PO',
                'debit_account_id' => $request->inv_debit_account_id,
                'credit_account_id' => $request->inv_credit_account_id,
            ]);
            $invs =  JournalSetting::where('id', $journal_category_product->journal_setting_invoice_id)->first();


            $journal_category_product =  JournalCategoryProduct::where('id', $request->id)->first();
            JournalCategoryProduct::where('id', $request->id)->update([
                'product_category_id' => $request->category_id,
            ]);
            $journal_category_products =  JournalCategoryProduct::where('id', $request->id)->first();



            DB::commit();
            return redirect('/accounting/journal_stock_account')->withSuccess('Sukses di Update!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Update!');
        }
    }
}
