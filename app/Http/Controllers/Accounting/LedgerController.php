<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalAccount;
use App\Models\ProfilCompany;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $period = $request->query('period');
            $journalAccountId = $request->query('journal_account_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $journalAccounts = JournalAccount::when($journalAccountId, function ($query) use ($journalAccountId) {
                return $query->where('id', $journalAccountId);
            })
                ->with(['journalTransactions' => function ($query) use ($period, $startDate, $endDate) {
                    // TODO: use the period
                    return $query
                        ->with('journalNumber')
                        ->whereHas('journalNumber', function ($query) use ($period, $startDate, $endDate) {
                            return $query->whereDate('date', '>=', $startDate)
                                ->whereDate('date', '<=', $endDate);
                        });
                }])
                ->get()
                ->toArray();
            foreach ($journalAccounts as &$journalAccount) {
                $totalDebit = 0;
                $totalCredit = 0;
                for ($i = 0; $i < sizeof($journalAccount['journal_transactions']); $i++) {
                    if ($journalAccount['journal_transactions'][$i]['type'] == 'debit') {
                        $totalDebit += $journalAccount['journal_transactions'][$i]['nominal'];
                    } elseif ($journalAccount['journal_transactions'][$i]['type'] == 'credit') {
                        $totalCredit += $journalAccount['journal_transactions'][$i]['nominal'];
                    }
                    if (isset($journalAccount['journal_transactions'][$i - 1])) {
                        $journalAccount['journal_transactions'][$i]['balance'] = $journalAccount['journal_transactions'][$i]['nominal'] + $journalAccount['journal_transactions'][$i - 1]['balance'];
                    } else {
                        $journalAccount['journal_transactions'][$i]['balance'] = $journalAccount['journal_transactions'][$i]['nominal'];
                    }
                }
                $totalBalance = $totalDebit + $totalCredit;
                $journalAccount['total_balance'] = $totalBalance;
                $journalAccount['total_debit'] = $totalDebit;
                $journalAccount['total_credit'] = $totalCredit;
            }

            $profileCompany =  ProfilCompany::where('status', 'active')->first();

            return view('pages.accounting.ledger.document', compact('journalAccounts', 'profileCompany'));
        }

        $journalAccounts = JournalAccount::all();
        return view('pages.accounting.ledger.index', compact('journalAccounts'));
    }
}
