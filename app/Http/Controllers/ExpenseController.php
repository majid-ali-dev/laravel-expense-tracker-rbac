<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user->hasPermission('view-expense')) {
            abort(403);
        }

        $expenses = $this->expenseQueryFor($user)
            ->with('user')
            ->latest()
            ->get();

        return view('manager.expenses.index', compact('expenses'));
    }

    public function create()
    {
        if (! auth()->user()->hasPermission('create-expense')) {
            abort(403);
        }

        return view('manager.expenses.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->hasPermission('create-expense')) {
            abort(403);
        }

        Expense::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense Added');
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (! $user->hasPermission('edit-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)->findOrFail($id);

        return view('manager.expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (! $user->hasPermission('edit-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)->findOrFail($id);

        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date ?? $expense->date, // 🔥 FIX
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense Updated');
    }

    public function delete($id)
    {
        $user = auth()->user();

        if (! $user->hasPermission('delete-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)->findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense Deleted');
    }

    public function show($id)
    {
        $user = auth()->user();

        if (! $user->hasPermission('view-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)
            ->with('user')
            ->findOrFail($id);

        return view('manager.expenses.show', compact('expense'));
    }

    public function download()
    {
        $user = auth()->user();

        if (! $user->hasPermission('download-expense')) {
            abort(403);
        }

        $expenses = $this->expenseQueryFor($user)
            ->with(['user', 'updater'])
            ->latest()
            ->get();

        $filename = 'expenses-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($expenses) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Title', 'Amount', 'Date', 'Description']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->title,
                    $expense->amount,
                    optional($expense->date)->format('d-m-Y'),
                    $expense->description,
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function history()
    {
        $user = auth()->user();

        if (! $user->hasRole('manager') && ! $user->hasRole('super_admin')) {
            abort(403);
        }

        $expenses = Expense::with(['user', 'updater'])
            ->latest('date')
            ->latest()
            ->get();

        $historySections = $expenses
            ->groupBy(fn ($expense) => optional($expense->date)->format('Y-m'))
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'label' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                    'expenses' => $items,
                    'total' => $items->sum('amount'),
                ];
            })
            ->values();

        $overallTotal = $expenses->sum('amount');

        return view('manager.expenses.history', compact('historySections', 'overallTotal'));
    }

    protected function expenseQueryFor($user)
    {
        $query = Expense::query();

        if (! $user->hasRole('manager') && ! $user->hasRole('super_admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }
}
