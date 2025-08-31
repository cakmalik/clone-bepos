<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalClosing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function index()
    {
        $data = [
            'title'     => 'LABA RUGI',
            'periode'   => JournalClosing::orderBy('id')->get()
        ];
        return view('pages.accounting.profit_loss.index', $data);
    }

    public function previewProfitLossData(Request $request)
    {
        $periode = JournalClosing::find($request->periode);

        $salesAccount       = $this->mainQuery('PENJUALAN', $request, $periode);
        $sellingCapital     = $this->mainQuery('HARGA POKOK PENJUALAN', $request, $periode);
        $operatingCost      = $this->mainQuery('BIAYA OPERASIONAL', $request, $periode);
        $nonOperatingIncome = $this->mainQuery('PENDAPATAN NON OPERASIONAL', $request, $periode);
        $nonOperatingCost   = $this->mainQuery('BIAYA NON OPERASIONAL', $request, $periode);


        if ($periode) {
            $periodeTitle = 'Periode ' . $periode->name;
        } else {
            $periodeTitle = 'Periode Berjalan dari ' . dateStandar($request->start_date) .
                ' hingga ' . dateStandar($request->end_date);
        }

        $data = [
            'company'           => profileCompany(),
            'sales'             => $salesAccount,
            'sellingCapital'    => $sellingCapital,
            'operatingCost'     => $operatingCost,
            'nonOperatingIncome' => $nonOperatingIncome,
            'nonOperatingCost'  => $nonOperatingCost,
            'periode'           => $periodeTitle
        ];

        return view('pages.accounting.profit_loss.profit_loss_data', $data);
    }


    public function mainQuery($accountType, $request, $periode)
    {


        if ($request->periode == '-') {
            $start_date = startOfDay($request->start_date);
            $end_date = endOfDay($request->end_date);

            $filter = " AND journal_numbers.journal_closing_id IS NULL
             AND journal_numbers.date >='$start_date'
             AND journal_numbers.date <='$end_date'";
        } else {
            $filter = " AND journal_numbers.journal_closing_id = '$request->periode'
             AND journal_numbers.code !='$periode->code'";
        }


        $query = DB::table('journal_accounts')
            ->leftJoin(
                DB::raw("(SELECT journal_account_id,
                 IFNULL(SUM(IF(journal_transactions.type='debit', nominal, 0)), 0) total_debit,
                 IFNULL(SUM(IF(journal_transactions.type='credit', nominal, 0)), 0) total_credit
                 FROM journal_transactions
                 LEFT JOIN journal_accounts ON journal_transactions.journal_account_id=journal_accounts.id
                 LEFT JOIN journal_numbers ON journal_transactions.journal_number_id=journal_numbers.id
                 LEFT JOIN journal_account_types ON journal_accounts.journal_account_type_id=journal_account_types.id
                 WHERE journal_account_types.name='$accountType'
                 $filter
                 AND is_done = TRUE
                 GROUP BY journal_transactions.journal_account_id) as sub"),
                'journal_accounts.id',
                'sub.journal_account_id'
            )
            ->leftJoin('journal_account_types', 'journal_accounts.journal_account_type_id', 'journal_account_types.id')
            ->select(
                'journal_accounts.code',
                'journal_accounts.name as account_name',
                DB::raw("IFNULL(total_debit, 0) total_debit"),
                DB::raw("IFNULL(total_credit, 0) total_credit"),
            )->where('journal_account_types.name', $accountType)->get();

        return $query;
    }
}
