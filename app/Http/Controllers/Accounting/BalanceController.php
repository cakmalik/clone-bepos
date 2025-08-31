<?php

namespace App\Http\Controllers\Accounting;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProfilCompany;
use App\Models\JournalClosing;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BalanceController extends Controller
{
    private $PROFIT_LOSS_ACCOUNT = [
        'PENJUALAN',
        'HARGA POKOK PENJUALAN',
        'BIAYA OPERASIONAL',
        'PENDAPATAN NON OPERASIONAL',
        'BIAYA NON OPERASIONAL',
    ];

    public function index()
    {
        $journal_closings = JournalClosing::all();
        return view('pages.accounting.balance.index', compact('journal_closings'));
    }

    public function print(Request $request)
    {
        try {
            $journal_closing_id = $request->query('journal_closing_id');
            $endDate = $request->query('end_date');
            $profitLoss = $request->query('profit_loss');

            $query = "SELECT ja.code as journal_account_code,
                       ja.name as journal_account_name,
                       nominal_summary,
                       jat.name as journal_account_type_name
                FROM (SELECT *, SUM(nominal_parsed) as nominal_summary
                      FROM (SELECT IF(type = 'debit', -nominal, nominal) as nominal_parsed, jt.*
                            FROM journal_transactions jt
                            LEFT JOIN journal_numbers jn on jt.journal_number_id = jn.id
                            WHERE DATE (jn.date) <= ?
                            ) as jt
                      GROUP BY jt.journal_account_id) as jt
                         LEFT JOIN journal_accounts ja on jt.journal_account_id = ja.id
                         LEFT JOIN journal_numbers jn on jt.journal_number_id = jn.id
                         LEFT JOIN journal_account_types jat on ja.journal_account_type_id = jat.id";

            if (!$profitLoss) {
                $query .= " WHERE ja.name NOT IN (" . join(',', array_map(fn($v) => "'" . $v . "'", $this->PROFIT_LOSS_ACCOUNT)) . ")";
            }

            $args = [Carbon::parse($endDate)->format('Y-m-d')];

            if ($journal_closing_id) {
                $query .= " WHERE jn.journal_closing_id = ? ";
                $args[] = $journal_closing_id;
            }

            $data = DB::select(
                $query,
                $args,
            );

            $data = collect($data)->groupBy('journal_account_type_name');
            $profileCompany =  ProfilCompany::where('status', 'active')->first();

            return view('pages.accounting.balance.print', [
                'data' => $data,
                'end_date' => Carbon::parse($endDate)->format('d/m/Y'),
                'profileCompany' => $profileCompany
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
    }
}
