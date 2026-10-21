<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Services\Finance\FinanceReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingReportController extends Controller
{
    use Concerns;

    /** Per-account debit/credit totals from manual journal entries in the range. */
    private function ledger(string $from, string $to)
    {
        $sums = DB::table('journal_lines')->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.restaurant_id', $this->rid())->whereBetween('journal_entries.entry_date', [$from, $to])
            ->selectRaw('journal_lines.account_id, SUM(debit) as debit, SUM(credit) as credit')->groupBy('journal_lines.account_id')->get()->keyBy('account_id');
        return Account::where('restaurant_id', $this->rid())->orderBy('code')->get()->map(function ($a) use ($sums) {
            $a->debit = (float) ($sums[$a->id]->debit ?? 0);
            $a->credit = (float) ($sums[$a->id]->credit ?? 0);
            return $a;
        });
    }

    public function trialBalance(Request $request)
    {
        [$from, $to] = FinanceReports::range($request->query('from'), $request->query('to'));
        $rows = $this->ledger($from, $to)->filter(fn ($a) => $a->debit > 0 || $a->credit > 0)->values();
        $rows = $rows->map(function ($a) {
            $net = round($a->debit - $a->credit, 2);
            $a->dr = max($net, 0);
            $a->cr = max(-$net, 0);
            return $a;
        });
        return view('finance.reports.trial-balance', ['rows' => $rows, 'from' => $from, 'to' => $to, 'totalDr' => $rows->sum('dr'), 'totalCr' => $rows->sum('cr')]);
    }

    public function profitLoss(Request $request)
    {
        [$from, $to] = FinanceReports::range($request->query('from'), $request->query('to'));
        $r = new FinanceReports($this->rid(), $from, $to);
        $ledger = $this->ledger($from, $to);
        $manualIncome = $ledger->where('type', 'income')->map(fn ($a) => ['name' => $a->name, 'amount' => round($a->credit - $a->debit, 2)])->filter(fn ($x) => $x['amount'] != 0)->values();
        $manualExpense = $ledger->where('type', 'expense')->map(fn ($a) => ['name' => $a->name, 'amount' => round($a->debit - $a->credit, 2)])->filter(fn ($x) => $x['amount'] != 0)->values();

        $revenue = $r->revenue();
        $expenses = $r->expensesTotal();
        $purchases = $r->purchasesTotal();
        $totalIncome = round($revenue + $manualIncome->sum('amount'), 2);
        $totalExpense = round($expenses + $purchases + $manualExpense->sum('amount'), 2);
        return view('finance.reports.profit-loss', compact('from', 'to', 'revenue', 'expenses', 'purchases', 'manualIncome', 'manualExpense', 'totalIncome', 'totalExpense') + ['net' => round($totalIncome - $totalExpense, 2)]);
    }
}
