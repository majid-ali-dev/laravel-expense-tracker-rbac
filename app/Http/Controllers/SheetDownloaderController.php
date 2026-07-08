<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SheetDownloaderController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermission('download-expense')) {
            abort(403);
        }

        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

       
        // EXPENSES 
        $expenses = Expense::with('user')
            ->whereBetween('date', [$from, $to])
            ->latest('date')
            ->get();

        // MEMBERS
        $members = User::whereHas('roles', fn($q) => $q->where('name', 'member'))->get();

        // PAYMENTS LOGIC
        $memberTotals = $members->map(function ($member) use ($from, $to) {

            $paid = Payment::where('user_id', $member->id)
                ->whereBetween('created_at', [$from, $to])
                ->sum('paid_amount');

            return [
                'name'       => $member->name,
                'total_paid' => $paid,
            ];
        });

        $totalExpenses   = $expenses->sum('amount');
        $totalMemberPaid = collect($memberTotals)->sum('total_paid');

        return view('manager.expenses.table_sheet', compact(
            'expenses',
            'members',
            'memberTotals',
            'from',
            'to',
            'totalExpenses',
            'totalMemberPaid'
        ));
    }

    public function download(Request $request)
    {
        if (!Auth::user()->hasPermission('download-expense')) {
            abort(403);
        }

        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

        $expenses = Expense::with('user')
            ->whereBetween('date', [$from, $to])
            ->get();

        $members = User::whereHas('roles', fn($q) => $q->where('name', 'member'))->get();

        $memberTotals = $members->map(function ($member) use ($from, $to) {

            return [
                'name' => $member->name,
                'total_paid' => Payment::where('user_id', $member->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->sum('paid_amount'),
            ];
        });

        $totalPaid     = $memberTotals->sum('total_paid');
        $totalExpenses = $expenses->sum('amount');

        $filename = 'expense-sheet-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($expenses, $memberTotals, $from, $to, $totalPaid, $totalExpenses) {

            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ["Expense Sheet ($from to $to)"]);
            fputcsv($file, []);

            fputcsv($file, ['Date', 'Title', 'Amount']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('d/m/Y'),
                    $expense->title,
                    number_format($expense->amount, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['TOTAL EXPENSES', number_format($totalExpenses, 2)]);

            fputcsv($file, []);
            fputcsv($file, ['Members Summary']);

            foreach ($memberTotals as $m) {
                fputcsv($file, [$m['name'], number_format($m['total_paid'], 2)]);
            }

            $balance = $totalPaid - $totalExpenses;

            fputcsv($file, []);
            fputcsv($file, ['TOTAL PAID', number_format($totalPaid, 2)]);
            fputcsv($file, ['BALANCE', number_format(abs($balance), 2)]);

            fclose($file);
        }, $filename);
    }
}
