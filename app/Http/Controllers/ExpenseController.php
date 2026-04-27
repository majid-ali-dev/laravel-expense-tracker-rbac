<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user->hasPermission('view-expense')) {
            abort(403);
        }

        $expenses = $this->expenseQueryFor($user)
            ->with('user')
            ->latest()
            ->simplePaginate(5);

        return view('manager.expenses.index', compact('expenses'));
    }

    public function create()
    {
        if (! Auth::user()->hasPermission('create-expense')) {
            abort(403);
        }

        return view('manager.expenses.create');
    }

    public function store(Request $request)
    {
        if (! Auth::user()->hasPermission('create-expense')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expenses', 'title')->where(fn ($query) => $query
                    ->where('user_id', Auth::id())
                    ->whereDate('date', $request->input('date'))),
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
        ], [
            'title.required' => 'Title field is required.',
            'title.string' => 'Title must be a valid text value.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'title.unique' => 'An expense with this title already exists for the selected date.',
            'amount.required' => 'Amount field is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.',
            'description.string' => 'Description must be a valid text value.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'date.required' => 'Date field is required.',
            'date.date' => 'Date must be a valid date.',
        ]);

        $expense = Expense::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
        ]);

        ExpenseHistory::create([
            'expense_id' => $expense->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'old_data' => null,
            'new_data' => $this->historyPayload($expense->fresh()),
            'changed_fields' => 'title,amount,date,description',
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense Added');
    }

    public function edit($id)
    {
        $user = Auth::user();

        if (! $user->hasPermission('edit-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)->findOrFail($id);

        return view('manager.expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (! $user->hasPermission('edit-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)->findOrFail($id);
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expenses', 'title')->ignore($expense->id)->where(fn ($query) => $query
                    ->where('user_id', $expense->user_id)
                    ->whereDate('date', $request->input('date'))),
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
        ], [
            'title.required' => 'Title field is required.',
            'title.string' => 'Title must be a valid text value.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'title.unique' => 'An expense with this title already exists for the selected date.',
            'amount.required' => 'Amount field is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.',
            'description.string' => 'Description must be a valid text value.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'date.required' => 'Date field is required.',
            'date.date' => 'Date must be a valid date.',
        ]);
        $oldData = $this->historyPayload($expense);

        $newValues = [
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
            'updated_by' => Auth::id(),
        ];

        $expense->update($newValues);
        $expense->refresh();

        $newData = $this->historyPayload($expense);
        $changedFields = $this->changedFields($oldData, $newData);

        if (! empty($changedFields)) {
            ExpenseHistory::create([
                'expense_id' => $expense->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'old_data' => $oldData,
                'new_data' => $newData,
                'changed_fields' => implode(',', $changedFields),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Expense Updated');
    }

    public function delete($id)
    {
        $user = Auth::user();

        if (! $user->hasPermission('delete-expense')) {
            abort(403);
        }

        $expense = $this->expenseQueryFor($user)->findOrFail($id);
        $oldData = $this->historyPayload($expense);

        ExpenseHistory::create([
            'expense_id' => $expense->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'old_data' => $oldData,
            'new_data' => null,
            'changed_fields' => 'title,amount,date,description',
        ]);

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense Deleted');
    }

    public function show($id)
    {
        $user = Auth::user();

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
        $user = Auth::user();

        if (! $user->hasPermission('download-expense')) {
            abort(403);
        }

        $expenses = $this->expenseQueryFor($user)
            ->with(['user', 'updater'])
            ->latest()
            ->get();

        $filename = 'expenses-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($expenses) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

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
        $user = Auth::user();

        if (! $user->hasRole('manager') && ! $user->hasRole('super_admin')) {
            abort(403);
        }

        $expenses = Expense::with(['user', 'updater', 'histories.user'])
            ->latest('date')
            ->latest()
            ->get();

        $liveExpenseIds = $expenses->pluck('id');

        $deletedExpenseCards = ExpenseHistory::with('user')
            ->whereNotNull('expense_id')
            ->whereNotIn('expense_id', $liveExpenseIds)
            ->orderBy('created_at')
            ->get()
            ->groupBy('expense_id')
            ->map(function ($histories, $expenseId) {
                $latestHistory = $histories->last();
                $snapshot = $latestHistory->new_data ?? $latestHistory->old_data ?? [];

                return (object) [
                    'id' => 'deleted-'.$expenseId,
                    'title' => $snapshot['title'] ?? '-',
                    'amount' => (float) ($snapshot['amount'] ?? 0),
                    'date' => ! empty($snapshot['date']) ? Carbon::parse($snapshot['date']) : null,
                    'description' => $snapshot['description'] ?? null,
                    'user' => (object) ['name' => $snapshot['created_by_name'] ?? '-'],
                    'updater' => null,
                    'created_at' => ! empty($snapshot['created_at']) ? Carbon::parse($snapshot['created_at']) : null,
                    'updated_at' => ! empty($snapshot['updated_at']) ? Carbon::parse($snapshot['updated_at']) : null,
                    'histories' => $histories->values(),
                    'is_deleted_entry' => $histories->contains(fn ($history) => $history->action === 'deleted'),
                ];
            })
            ->values();

        $expenseCards = $expenses->map(function ($expense) {
            return tap($expense, function ($item) {
                $item->is_deleted_entry = false;
                $item->setRelation('histories', $item->histories->sortBy('created_at')->values());
            });
        })->concat($deletedExpenseCards);

        $historySections = $expenseCards
            ->sortByDesc(fn ($expense) => optional($expense->date)?->timestamp ?? 0)
            ->groupBy(fn ($expense) => optional($expense->date)->format('Y-m'))
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'label' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                    'expenses' => $items->values(),
                    'total' => $items->sum('amount'),
                ];
            })
            ->values();

        $overallTotal = $expenseCards->sum('amount');

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

    protected function historyPayload(Expense $expense): array
    {
        $expense->loadMissing(['user', 'updater']);

        return [
            'title' => $expense->title,
            'amount' => (float) $expense->amount,
            'date' => optional($expense->date)->format('Y-m-d'),
            'description' => $expense->description,
            'created_by_id' => $expense->user_id,
            'created_by_name' => $expense->user->name ?? '-',
            'updated_by_id' => $expense->updated_by,
            'updated_by_name' => $expense->updater->name ?? '-',
            'created_at' => optional($expense->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($expense->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    protected function changedFields(array $oldData, array $newData): array
    {
        $fields = ['title', 'amount', 'date', 'description'];

        return collect($fields)
            ->filter(fn ($field) => (string) ($oldData[$field] ?? '') !== (string) ($newData[$field] ?? ''))
            ->values()
            ->all();
    }
}
