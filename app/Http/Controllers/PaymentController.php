<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user->hasPermission('view-payment')) {
            abort(403);
        }

        if ($user->hasRole('manager') || $user->hasRole('super_admin')) {
            $payments = Payment::with('user')->latest()->get();

            $totalAmount = $payments->sum('total_amount');
            $totalPaid = $payments->sum('paid_amount');
            $totalRemaining = $payments->sum('remaining_amount');

            $paidCount = $payments->where('status', 'paid')->count();
            $unpaidCount = $payments->where('status', 'unpaid')->count();

            return view('manager.payments.index', compact(
                'payments',
                'totalAmount',
                'totalPaid',
                'totalRemaining',
                'paidCount',
                'unpaidCount'
            ));
        }

        $payments = Payment::where('user_id', $user->id)->get();

        return view('member.payments.index', compact('payments'));
    }

    public function createPayment(Request $request)
    {
        if (! auth()->user()->hasPermission('create-payment')) {
            abort(403);
        }

        Payment::create([
            'user_id' => $request->user_id,
            'total_amount' => $request->total_amount,
            'remaining_amount' => $request->total_amount,
            'month' => $request->month,
        ]);

        return back()->with('success', 'Payment Assigned');
    }

    public function pay(Request $request, $id)
    {
        $user = auth()->user();

        if (! $user->hasPermission('pay-bills') && ! $user->hasRole('manager') && ! $user->hasRole('super_admin')) {
            abort(403);
        }

        $paymentQuery = Payment::query();

        if (! $user->hasRole('manager') && ! $user->hasRole('super_admin')) {
            $paymentQuery->where('user_id', $user->id);
        }

        $payment = $paymentQuery->findOrFail($id);

        $payAmount = $request->amount;

        $newPaid = $payment->paid_amount + $payAmount;
        $remaining = $payment->total_amount - $newPaid;

        if ($newPaid <= 0) {
            $status = 'unpaid';
        } elseif ($newPaid < $payment->total_amount) {
            $status = 'partial';
        } else {
            $status = 'paid';
            $remaining = 0;
        }

        $payment->update([
            'paid_amount' => $newPaid,
            'remaining_amount' => $remaining,
            'status' => $status,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payment Updated');
    }

    public function create()
    {
        if (! auth()->user()->hasPermission('create-payment')) {
            abort(403);
        }

        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        return view('manager.payments.create', compact('users'));
    }

    public function storeByManager(Request $request)
    {
        if (! auth()->user()->hasPermission('create-payment')) {
            abort(403);
        }

        Payment::create([
            'user_id' => $request->user_id,
            'total_amount' => $request->total_amount,
            'paid_amount' => 0,
            'remaining_amount' => $request->total_amount,
            'month' => $request->month,
            'status' => 'unpaid',
        ]);

        return redirect()->back()->with('success', 'Payment Assigned to User');
    }
}
