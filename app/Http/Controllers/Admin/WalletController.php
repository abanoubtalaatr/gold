<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Notifications\Client\WalletBalanceUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletController extends Controller
{
    // Show client wallet management
    public function show(User $client)
    {
        $client->load('city:id,name');
        
        // Get or create wallet for the client
        $wallet = $client->wallet()->firstOrCreate([
            'user_id' => $client->id
        ], [
            'balance' => 0,
            'pending_balance' => 0,
            'total_earned' => 0,
        ]);
        
        // Get wallet transactions
        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Wallet/Show', [
            'client' => $client,
            'transactions' => $transactions,
            'currentBalance' => $wallet->balance,
        ]);
    }

    // Update wallet balance
    public function updateBalance(Request $request, User $client)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:add,subtract',
            'description' => 'required|string|max:255',
        ]);

        // Get or create wallet for the client
        $wallet = $client->wallet()->firstOrCreate([
            'user_id' => $client->id
        ], [
            'balance' => 0,
            'pending_balance' => 0,
            'total_earned' => 0,
        ]);

        $currentBalance = $wallet->balance;
        
        if ($validated['type'] === 'add') {
            $newBalance = $currentBalance + $validated['amount'];
            $transactionType = 'credit';
        } else {
            $newBalance = max(0, $currentBalance - $validated['amount']);
            $transactionType = 'debit';
        }

        // Update wallet balance
        $wallet->update(['balance' => $newBalance]);

        // Create transaction record
        $wallet->transactions()->create([
            'type' => $transactionType,
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'status' => 'completed',
            'transactionable_type' => 'admin_adjustment',
            'transactionable_id' => Auth::id(),
        ]);

        // Send notification to the client
        $client->notify(new WalletBalanceUpdatedNotification(
            $transactionType,
            $validated['amount'],
            $validated['description'],
            $newBalance
        ));

        return back()->with('success', 'تم تحديث رصيد العميل بنجاح وإرسال إشعار له');
    }

    // Approve pending transaction
    public function approveTransaction(WalletTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'هذه المعاملة تم معالجتها بالفعل');
        }

        $wallet = $transaction->wallet;
        $client = $wallet->user;

        if ($transaction->type === 'credit') {
            $wallet->increment('balance', $transaction->amount);
        } else {
            $wallet->decrement('balance', $transaction->amount);
        }

        // Update transaction
        $transaction->update([
            'status' => 'completed',
            'transactionable_type' => 'admin_approval',
            'transactionable_id' => Auth::id(),
        ]);

        // Send notification to the client about transaction approval
        $client->notify(new WalletBalanceUpdatedNotification(
            $transaction->type,
            $transaction->amount,
            'تم قبول طلب المعاملة: ' . $transaction->description,
            $wallet->fresh()->balance
        ));

        return back()->with('success', 'تم قبول الطلب بنجاح وإرسال إشعار للعميل');
    }

    // Reject pending transaction
    public function rejectTransaction(Request $request, WalletTransaction $transaction)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'هذه المعاملة تم معالجتها بالفعل');
        }

        $wallet = $transaction->wallet;
        $client = $wallet->user;

        $transaction->update([
            'status' => 'failed',
            'description' => $transaction->description . ' - سبب الرفض: ' . $validated['rejection_reason'],
            'transactionable_type' => 'admin_rejection',
            'transactionable_id' => Auth::id(),
        ]);

        // Send notification to the client about transaction rejection
        // Note: We don't change the balance for rejected transactions, so we pass the current balance
        $client->notify(new WalletBalanceUpdatedNotification(
            $transaction->type,
            $transaction->amount,
            'تم رفض طلب المعاملة: ' . $transaction->description . ' - السبب: ' . $validated['rejection_reason'],
            $wallet->balance
        ));

        return back()->with('success', 'تم رفض الطلب بنجاح وإرسال إشعار للعميل');
    }
}