<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SheetDownloaderController extends Controller
{
    public function index()
    {
        if (! Auth::user()->hasPermission('download-expense')) {
            abort(403);
        }

        $expenses = Expense::with('user')->latest('date')->get();
        $members = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        // Create memberTotals array for the view
        $memberTotals = [];
        foreach ($members as $member) {
            $memberTotals[] = [
                'name' => $member->name,
                'total_amount' => $member->total_amount ?? 0,
                'total_paid' => $member->total_paid ?? 0,
                'remaining' => $member->remaining ?? 0,
                'status' => $member->payment_status ?? 'unpaid',
            ];
        }

        $totalExpenses = $expenses->sum('amount');
        $totalMemberAmount = $members->sum('total_amount');
        $totalMemberPaid = $members->sum('total_paid');
        $totalMemberRemaining = $members->sum('remaining');

        return view('manager.expenses.table_sheet', compact(
            'expenses',
            'members',
            'memberTotals',
            'totalExpenses',
            'totalMemberAmount',
            'totalMemberPaid',
            'totalMemberRemaining'
        ));
    }

    public function download()
    {
        if (! Auth::user()->hasPermission('download-expense')) {
            abort(403);
        }

        $expenses = Expense::with('user')->latest('date')->get();
        $members = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        $filename = 'expense-sheet-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($expenses, $members) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to support special characters
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            $groupedExpenses = [];
            $itemNames = [];

            foreach ($expenses as $expense) {
                $dateKey = $expense->date->format('Y-m-d');
                $itemName = trim($expense->title) ?: 'Other';

                if (! in_array($itemName, $itemNames, true)) {
                    $itemNames[] = $itemName;
                }

                $groupedExpenses[$dateKey]['date'] = $expense->date->format('d/m/Y');
                $groupedExpenses[$dateKey][$itemName] = ($groupedExpenses[$dateKey][$itemName] ?? 0) + $expense->amount;
                $groupedExpenses[$dateKey]['total'] = ($groupedExpenses[$dateKey]['total'] ?? 0) + $expense->amount;
            }

            ksort($groupedExpenses);

            // Title
            fputcsv($file, ['March - Saved']);
            fputcsv($file, []);

            // Headers
            fputcsv($file, array_merge(['Date'], $itemNames, ['Total']));

            // Expenses data
            $itemTotals = array_fill_keys($itemNames, 0);
            foreach ($groupedExpenses as $expenseRow) {
                $row = [$expenseRow['date']];
                foreach ($itemNames as $itemName) {
                    $value = isset($expenseRow[$itemName]) ? number_format($expenseRow[$itemName], 2) : '';
                    $row[] = $value;
                    $itemTotals[$itemName] += $expenseRow[$itemName] ?? 0;
                }
                $row[] = number_format($expenseRow['total'] ?? 0, 2);
                fputcsv($file, $row);
            }

            // Empty row before grand total
            fputcsv($file, []);
            fputcsv($file, array_merge(['Grand Total'], array_map(function ($itemName) use ($itemTotals) {
                return number_format($itemTotals[$itemName] ?? 0, 2);
            }, $itemNames), [number_format($expenses->sum('amount'), 2)]));

            // Empty row
            fputcsv($file, []);

            // Members summary header
            fputcsv($file, ['Members Summary']);
            fputcsv($file, ['Member Name', 'Paid Amount']);

            // Member rows
            foreach ($members as $member) {
                fputcsv($file, [
                    $member->name,
                    number_format($member->total_paid ?? 0, 2),
                ]);
            }

            // Member totals row
            $totalPaid = $members->sum('total_paid');

            fputcsv($file, []);
            fputcsv($file, ['TOTAL PAID', number_format($totalPaid, 2)]);

            // Final totals
            $totalExpenses = $expenses->sum('amount');
            $remainingBalance = $totalPaid - $totalExpenses;

            fputcsv($file, []);
            fputcsv($file, ['Final Totals']);
            fputcsv($file, ['Total Expenses', number_format($totalExpenses, 2)]);
            fputcsv($file, ['Total Member Paid Amount', number_format($totalPaid, 2)]);
            if ($remainingBalance < 0) {
                fputcsv($file, ['Extra Balance', number_format(abs($remainingBalance), 2)]);
            } else {
                fputcsv($file, ['Remaining Balance', number_format($remainingBalance, 2)]);
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
