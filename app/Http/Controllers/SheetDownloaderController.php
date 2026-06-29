<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SheetDownloaderController extends Controller
{
    public function index(Request $request)
    {
        if (! Auth::user()->hasPermission('download-expense')) {
            abort(403);
        }

        // Current month by default
        $selectedMonth = $request->get('month', now()->format('Y-m'));

        $expenseQuery = Expense::with('user')->latest('date');

        if ($selectedMonth !== 'all') {
            $expenseQuery->whereYear('date', Carbon::parse($selectedMonth)->year)
                ->whereMonth('date', Carbon::parse($selectedMonth)->month);
        }

        // Search filter
        $search = $request->get('search');
        if ($search) {
            $expenseQuery->where('title', 'like', '%' . $search . '%');
        }

        $expenses = $expenseQuery->get();

        $members = User::whereHas('roles', fn($q) => $q->where('name', 'member'))->get();

        $memberTotals = $members->map(fn($m) => [
            'name'         => $m->name,
            'total_amount' => $m->total_amount ?? 0,
            'total_paid'   => $m->total_paid ?? 0,
            'remaining'    => $m->remaining ?? 0,
            'status'       => $m->payment_status ?? 'unpaid',
        ])->toArray();

        // Build months dropdown: current + past 11 months
        $months = collect(range(0, 11))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        return view('manager.expenses.table_sheet', compact(
            'expenses',
            'members',
            'memberTotals',
            'months',
            'selectedMonth',
            'search',
        ) + [
            'totalExpenses'        => $expenses->sum('amount'),
            'totalMemberAmount'    => $members->sum('total_amount'),
            'totalMemberPaid'      => $members->sum('total_paid'),
            'totalMemberRemaining' => $members->sum('remaining'),
        ]);
    }

    public function download(Request $request)
    {
        if (! Auth::user()->hasPermission('download-expense')) {
            abort(403);
        }

        $selectedMonth = $request->get('month', now()->format('Y-m'));

        $expenseQuery = Expense::with('user')->latest('date');

        if ($selectedMonth !== 'all') {
            $expenseQuery->whereYear('date', Carbon::parse($selectedMonth)->year)
                ->whereMonth('date', Carbon::parse($selectedMonth)->month);
        }

        $expenses  = $expenseQuery->get();
        $members   = User::whereHas('roles', fn($q) => $q->where('name', 'member'))->get();

        $label    = $selectedMonth === 'all'
            ? 'All Months'
            : Carbon::parse($selectedMonth)->format('F Y');

        $filename = 'expense-sheet-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($expenses, $members, $label) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $groupedExpenses = [];
            $itemNames       = [];

            foreach ($expenses as $expense) {
                $dateKey  = $expense->date->format('Y-m-d');
                $itemName = trim($expense->title) ?: 'Other';

                if (! in_array($itemName, $itemNames, true)) {
                    $itemNames[] = $itemName;
                }

                $groupedExpenses[$dateKey]['date']       = $expense->date->format('d/m/Y');
                $groupedExpenses[$dateKey][$itemName]    = ($groupedExpenses[$dateKey][$itemName] ?? 0) + $expense->amount;
                $groupedExpenses[$dateKey]['total']      = ($groupedExpenses[$dateKey]['total'] ?? 0) + $expense->amount;
            }

            ksort($groupedExpenses);

            fputcsv($file, [$label . ' - Saved']);
            fputcsv($file, []);
            fputcsv($file, array_merge(['Date'], $itemNames, ['Total']));

            $itemTotals = array_fill_keys($itemNames, 0);
            foreach ($groupedExpenses as $row) {
                $line = [$row['date']];
                foreach ($itemNames as $name) {
                    $line[]             = isset($row[$name]) ? number_format($row[$name], 2) : '';
                    $itemTotals[$name] += $row[$name] ?? 0;
                }
                $line[] = number_format($row['total'] ?? 0, 2);
                fputcsv($file, $line);
            }

            fputcsv($file, []);
            fputcsv($file, array_merge(
                ['Grand Total'],
                array_map(fn($n) => number_format($itemTotals[$n] ?? 0, 2), $itemNames),
                [number_format($expenses->sum('amount'), 2)]
            ));

            fputcsv($file, []);
            fputcsv($file, ['Members Summary']);
            fputcsv($file, ['Member Name', 'Paid Amount']);

            foreach ($members as $member) {
                fputcsv($file, [$member->name, number_format($member->total_paid ?? 0, 2)]);
            }

            $totalPaid        = $members->sum('total_paid');
            $totalExpenses    = $expenses->sum('amount');
            $remainingBalance = $totalPaid - $totalExpenses;

            fputcsv($file, []);
            fputcsv($file, ['TOTAL PAID', number_format($totalPaid, 2)]);
            fputcsv($file, []);
            fputcsv($file, ['Final Totals']);
            fputcsv($file, ['Total Expenses', number_format($totalExpenses, 2)]);
            fputcsv($file, ['Total Member Paid Amount', number_format($totalPaid, 2)]);
            fputcsv($file, [
                $remainingBalance < 0 ? 'Extra Balance' : 'Remaining Balance',
                number_format(abs($remainingBalance), 2),
            ]);

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
