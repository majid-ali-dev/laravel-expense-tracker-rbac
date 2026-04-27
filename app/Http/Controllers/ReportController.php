<?php

namespace App\Http\Controllers;

use App\Exports\ExpenseReportExport;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $payload = $this->buildReportPayload($request);

        return view('reports.expense-report', $payload);
    }

    public function download(Request $request)
    {
        $payload = $this->buildReportPayload($request);
        $fileName = 'monthly-expense-report-'.$payload['reportLabel'].'.xlsx';

        return (new ExpenseReportExport($payload))->download($fileName);
    }

    private function buildReportPayload(Request $request): array
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedMemberId = $request->input('member_id');
        $paymentStatus = $request->input('status');

        $rangeStart = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $rangeEnd = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();

        $memberQuery = User::whereHas('roles', fn($query) => $query->where('name', 'member'));
        $memberCount = $memberQuery->when($selectedMemberId, fn($query) => $query->where('id', $selectedMemberId))->count() ?: 1;

        $membersList = User::whereHas('roles', fn($query) => $query->where('name', 'member'))->orderBy('name')->get();

        $expenses = Expense::with('user')
            ->when($selectedMemberId, fn($query) => $query->where('user_id', $selectedMemberId))
            ->whereBetween('date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->orderBy('date')
            ->get();

        $payments = Payment::with(['user', 'updater'])
            ->when($selectedMemberId, fn($query) => $query->where('user_id', $selectedMemberId))
            ->whereBetween('created_at', [$rangeStart->toDateTimeString(), $rangeEnd->toDateTimeString()])
            ->orderByDesc('created_at')
            ->get();

        $weekRanges = $this->buildWeekRanges($rangeStart, $rangeEnd);
        $weeklySummary = collect($weekRanges)->map(function ($range) use ($expenses, $memberCount) {
            $weekGroceryTotal = $expenses
                ->filter(fn (Expense $expense) => $expense->date->between($range['start'], $range['end']) && $this->expenseCategory($expense->title) === 'Grocery')
                ->sum('amount');

            return [
                'week' => $range['week'],
                'start_date' => $range['start']->format('d/m/Y'),
                'end_date' => $range['end']->format('d/m/Y'),
                'total_grocery' => $weekGroceryTotal,
                'per_member_deduction' => $memberCount ? round($weekGroceryTotal / $memberCount, 2) : 0,
            ];
        })->values();

        $members = User::whereHas('roles', fn($query) => $query->where('name', 'member'))
            ->with('payments')
            ->when($selectedMemberId, fn($query) => $query->where('id', $selectedMemberId))
            ->get()
            ->map(fn (User $member) => $this->prepareMemberSummary($member, $weeklySummary));

        if ($paymentStatus) {
            $members = $members->filter(fn ($member) => $member['status'] === $paymentStatus)->values();
        }

        $dailyTotals = $expenses
            ->groupBy(fn (Expense $expense) => $expense->date->format('d/m/Y'))
            ->map(fn ($group) => $group->sum('amount'));

        $dailyRows = $expenses->map(function (Expense $expense) use ($dailyTotals) {
            $category = $this->expenseCategory($expense->title);

            return [
                'date' => $expense->date->format('d/m/Y'),
                'milk' => $category === 'Milk' ? $expense->amount : 0,
                'water' => $category === 'Water' ? $expense->amount : 0,
                'category_name' => $expense->title,
                'amount' => $expense->amount,
                'category' => $category,
                'total_day_expense' => $dailyTotals[$expense->date->format('d/m/Y')] ?? $expense->amount,
            ];
        });

        $totalGrocery = $weeklySummary->sum('total_grocery');
        $totalMemberCollection = $payments->sum('paid_amount');
        $remainingBalance = max(0, $totalMemberCollection - $totalGrocery);
        $extraBalance = max(0, $totalGrocery - $totalMemberCollection);

        $reportLabel = $rangeStart->format('F-Y');
        $sheetName = 'Monthly Expense Report - '.$rangeStart->format('F Y');

        return [
            'startDate' => $rangeStart->toDateString(),
            'endDate' => $rangeEnd->toDateString(),
            'selectedMemberId' => $selectedMemberId,
            'paymentStatus' => $paymentStatus,
            'rangeLabel' => $rangeStart->format('d M Y').' - '.$rangeEnd->format('d M Y'),
            'reportLabel' => strtolower(str_replace(' ', '-', $reportLabel)),
            'sheetName' => $sheetName,
            'reportMonth' => $rangeStart->format('F Y'),
            'dailyRows' => $dailyRows,
            'weeklySummary' => $weeklySummary->all(),
            'members' => $members->all(),
            'financialSummary' => [
                'total_grocery_expenses' => $totalGrocery,
                'total_member_collection' => $totalMemberCollection,
                'remaining_balance' => $remainingBalance,
                'extra_balance' => $extraBalance,
            ],
            'membersList' => $membersList,
        ];
    }

    private function buildWeekRanges(Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $monthStart = $rangeStart->copy()->startOfMonth();
        $ranges = [];

        for ($week = 1; $week <= 4; $week++) {
            $weekStart = $monthStart->copy()->addDays(($week - 1) * 7)->startOfDay();
            $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

            if ($weekStart->gt($rangeEnd)) {
                break;
            }

            $weekEnd = $weekEnd->min($rangeEnd);
            $weekStart = $weekStart->lt($rangeStart) ? $rangeStart : $weekStart;

            $ranges[] = [
                'week' => $week,
                'start' => $weekStart,
                'end' => $weekEnd,
            ];
        }

        return $ranges;
    }

    private function prepareMemberSummary(User $member, $weeklySummary): array
    {
        $payments = $member->payments;
        $latestPayment = $payments->sortByDesc('created_at')->first();
        $weeklyDeductions = $weeklySummary->map(fn ($week) => 'Week '.$week['week'].': Rs '.number_format($week['per_member_deduction'], 2))->implode(', ');
        $totalAssigned = $member->total_amount > 0 ? (float) $member->total_amount : $weeklySummary->sum('per_member_deduction');
        $paidAmount = (float) $member->total_paid;
        $remaining = max(0, $totalAssigned - $paidAmount);
        $status = $paidAmount >= $totalAssigned ? 'paid' : ($paidAmount === 0 ? 'unpaid' : 'partial');

        return [
            'id' => $member->id,
            'name' => $member->name,
            'total_assigned' => $totalAssigned,
            'weekly_deductions' => $weeklyDeductions,
            'paid_amount' => $paidAmount,
            'remaining' => $remaining,
            'status' => $status,
            'last_payment_date' => $latestPayment ? $latestPayment->created_at->format('d/m/Y') : '-',
        ];
    }

    private function expenseCategory(string $title): string
    {
        $slug = strtolower($title);

        if (str_contains($slug, 'milk')) {
            return 'Milk';
        }

        if (str_contains($slug, 'water')) {
            return 'Water';
        }

        $groceryKeywords = ['grocery', 'potato', 'potatoes', 'veg', 'vegetable', 'vegetables', 'egg', 'eggs', 'bread', 'rice', 'flour', 'food', 'fruit', 'fruits'];

        foreach ($groceryKeywords as $keyword) {
            if (str_contains($slug, $keyword)) {
                return 'Grocery';
            }
        }

        return 'Other';
    }
}
