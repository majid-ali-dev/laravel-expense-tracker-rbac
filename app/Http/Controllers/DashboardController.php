<?php

namespace App\Http\Controllers;

use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $permissions = $user->permissions();
        $roleNames = $user->roleNames();

        $expenseQuery = Expense::query();

        if (! $user->hasRole('manager') && ! $user->hasRole('super_admin')) {
            $expenseQuery->where('user_id', $user->id);
        }

        $expenseCount = $user->hasPermission('view-expense')
            ? (clone $expenseQuery)->count()
            : 0;

        $totalAmount = $user->hasPermission('view-expense')
            ? (clone $expenseQuery)->sum('amount')
            : 0;

        // Member specific data
        $paymentData = [];
        if ($user->hasRole('member')) {
            $paymentData = [
                'total_amount' => $user->total_amount ?? 0,
                'total_paid' => $user->total_paid ?? 0,
                'remaining' => $user->remaining ?? 0,
                'payment_status' => $user->payment_status ?? 'unpaid',
                'last_payment' => $user->payments()->latest()->first(),
                'payment_count' => $user->payments()->count(),
            ];
        }

        return view('dashboard.index', [
            'user' => $user,
            'roleNames' => $roleNames,
            'permissions' => $permissions,
            'canManageUsers' => $user->hasPermission('manage-users'),
            'canManageRoles' => $user->hasPermission('assign-roles'),
            'canViewExpenses' => $user->hasPermission('view-expense'),
            'canCreateExpenses' => $user->hasPermission('create-expense'),
            'canDownloadExpenses' => $user->hasPermission('download-expense'),
            'expenseCount' => $expenseCount,
            'totalAmount' => $totalAmount,
            'paymentData' => $paymentData,
        ]);
    }
}
