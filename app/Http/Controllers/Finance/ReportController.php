<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceReports;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use Concerns;

    public const REPORTS = [
        'daily' => 'Sales by day',
        'items' => 'Sales by item',
        'categories' => 'Sales by category',
        'payments' => 'Sales by payment method',
        'top' => 'Top 10 items',
        'expense-vs-revenue' => 'Expenses vs revenue',
    ];

    private function build(FinanceReports $r, string $report): array
    {
        return match ($report) {
            'daily' => [['Date', 'Orders', 'Revenue'], $r->salesByDay()->map(fn ($x) => [$x->day, (int) $x->orders, round((float) $x->revenue, 2)])],
            'items' => [['Item', 'Qty sold', 'Revenue'], $r->salesByItem()->map(fn ($x) => [$x->item, (int) $x->qty, round((float) $x->revenue, 2)])],
            'top' => [['Item', 'Qty sold', 'Revenue'], $r->salesByItem(10)->map(fn ($x) => [$x->item, (int) $x->qty, round((float) $x->revenue, 2)])],
            'categories' => [['Category', 'Qty sold', 'Revenue'], $r->salesByCategory()->map(fn ($x) => [$x->category, (int) $x->qty, round((float) $x->revenue, 2)])],
            'payments' => [['Payment method', 'Orders', 'Revenue'], $r->salesByPaymentMethod()->map(fn ($x) => [$x->method, (int) $x->orders, round((float) $x->revenue, 2)])],
            'expense-vs-revenue' => [['Date', 'Revenue', 'Expenses', 'Net'], $r->expenseVsRevenueByDay()->map(fn ($x) => [$x->day, $x->revenue, $x->expenses, $x->net])],
        };
    }

    public function index(Request $request)
    {
        $report = array_key_exists((string) $request->query('report', ''), self::REPORTS) ? $request->query('report') : 'daily';
        [$from, $to] = FinanceReports::range($request->query('from'), $request->query('to'));
        $r = new FinanceReports($this->rid(), $from, $to);
        [$headers, $rows] = $this->build($r, $report);

        if ($request->query('format') === 'csv') {
            return response()->streamDownload(function () use ($headers, $rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $headers);
                foreach ($rows as $row) {
                    // Neutralise spreadsheet formula injection in text cells.
                    fputcsv($out, array_map(fn ($c) => is_string($c) && preg_match('/^[=+\-@]/', $c) ? "'" . $c : $c, $row));
                }
                fclose($out);
            }, "{$report}_{$from}_{$to}.csv", ['Content-Type' => 'text/csv']);
        }

        return view('finance.reports.hub', [
            'report' => $report, 'reports' => self::REPORTS, 'from' => $from, 'to' => $to, 'headers' => $headers, 'rows' => $rows,
            'kpi' => ['revenue' => $r->revenue(), 'orders' => $r->orderCount(), 'expenses' => $r->expensesTotal(), 'purchases' => $r->purchasesTotal()],
        ]);
    }
}
