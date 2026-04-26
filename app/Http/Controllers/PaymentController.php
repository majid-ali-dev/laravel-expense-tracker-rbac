<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        if (! $authUser->hasPermission('view-payment')) {
            abort(403);
        }

        if ($authUser->hasRole('manager') || $authUser->hasRole('super_admin')) {
            $users = $this->paymentUsers();

            $totalAmount = $users->sum(fn (User $user) => (float) $user->total_amount);
            $totalPaid = $users->sum(fn (User $user) => (float) $user->total_paid);
            $totalRemaining = $users->sum(fn (User $user) => (float) $user->remaining);
            $paidCount = $users->where('payment_status', 'paid')->count();
            $partialCount = $users->where('payment_status', 'partial')->count();
            $unpaidCount = $users->where('payment_status', 'unpaid')->count();

            return view('manager.payments.index', compact(
                'users',
                'totalAmount',
                'totalPaid',
                'totalRemaining',
                'paidCount',
                'partialCount',
                'unpaidCount'
            ));
        }

        $user = $authUser->load([
            'payments' => fn ($query) => $query
                ->select('id', 'user_id', 'paid_amount', 'month', 'updated_by', 'created_at')
                ->latest(),
        ]);

        return view('member.payments.index', compact('user'));
    }

    public function addPayment(User $user)
    {
        $this->authorizePaymentChange();

        // Check if user has total_amount set
        if ($user->total_amount <= 0) {
            return redirect()->route('payments.index')
                ->with('error', 'This user has no total amount assigned. Please set total amount first.');
        }

        $user->load('payments');
        $user->total_paid = $user->payments()->sum('paid_amount');
        $user->remaining = $user->total_amount - $user->total_paid;

        return view('manager.payments.add_pay', compact('user'));
    }

    public function pay(Request $request, User $user)
    {
        $this->authorizePaymentChange();

        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'gt:0', 'max:'.$user->remaining],
        ], [
            'paid_amount.required' => 'Payment amount is required.',
            'paid_amount.numeric' => 'Payment amount must be a number.',
            'paid_amount.gt' => 'Payment amount must be greater than 0.',
            'paid_amount.max' => 'Payment amount cannot exceed remaining balance of Rs '.number_format($user->remaining, 2),
        ]);

        $paidAmount = (float) $data['paid_amount'];

        Payment::create([
            'user_id' => $user->id,
            'paid_amount' => $paidAmount,
            'month' => strtolower(now()->format('M')),
            'updated_by' => auth()->id(),
        ]);

        // Calculate new totals
        $totalPaid = $user->payments()->sum('paid_amount');
        $remaining = $user->total_amount - $totalPaid;

        // Auto update status based on remaining
        if ($remaining <= 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0 && $remaining > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        // Update user's total_paid, remaining, payment_status
        $user->update([
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'payment_status' => $status,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment added successfully.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorizePaymentChange();

        $user = $payment->user;
        $payment->delete();

        // Recalculate totals after deletion
        $totalPaid = $user->payments()->sum('paid_amount');
        $remaining = $user->total_amount - $totalPaid;

        // Update status based on remaining
        if ($remaining <= 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0 && $remaining > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $user->update([
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'payment_status' => $status,
        ]);

        return back()->with('success', 'Payment record deleted successfully.');
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorizePaymentChange();

        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'gt:0'],
        ], [
            'paid_amount.required' => 'Payment amount is required.',
            'paid_amount.numeric' => 'Payment amount must be a number.',
            'paid_amount.gt' => 'Payment amount must be greater than 0.',
        ]);

        $payment->loadMissing('user');

        $paidAmount = (float) $data['paid_amount'];
        $allowedLimit = (float) $payment->user->remaining + (float) $payment->paid_amount;

        if ($paidAmount > $allowedLimit) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Updated amount cannot exceed the remaining balance for this user.',
            ]);
        }

        $payment->update([
            'paid_amount' => $paidAmount,
            'updated_by' => auth()->id(),
        ]);

        // Recalculate totals
        $user = $payment->user;
        $totalPaid = $user->payments()->sum('paid_amount');
        $remaining = $user->total_amount - $totalPaid;

        // Update status
        if ($remaining <= 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0 && $remaining > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $user->update([
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'payment_status' => $status,
        ]);

        return back()->with('success', 'Payment updated successfully.');
    }

    private function authorizePaymentChange(): void
    {
        if (! auth()->user()->hasPermission('create-payment')) {
            abort(403);
        }
    }

    private function paymentUsers()
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'member'))
            ->where('total_amount', '>', 0)
            ->with([
                'payments' => fn ($query) => $query
                    ->select('id', 'user_id', 'paid_amount', 'month', 'updated_by', 'created_at')
                    ->latest(),
            ])
            ->orderBy('name')
            ->simplePaginate(5); // Using simplePaginate(5)
    }
}
