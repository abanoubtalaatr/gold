<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Vendor;
use App\Models\WalletTransaction;
use App\Models\SettlementRequest;
use App\Models\User;
use App\Notifications\Vendor\SettlementStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function show(User $vendor)
    {
        $wallet = $vendor->wallet;

        // Initialize variables to avoid undefined variable errors
        $transactions = null;
        $settlementRequests = null;

        if ($wallet) {
            // Check if wallet has transactions
            if ($wallet->transactions !== null) {
                $transactions = $wallet->transactions()
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            }

            // Retrieve settlement requests
            $settlementRequests = $wallet->settlementRequests()
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Handle case where wallet does not exist
            // For example, set $transactions and $settlementRequests to empty collections or null
            $transactions = collect();
            $settlementRequests = collect();
        }

        
        return inertia('Admin/Vendor-Wallet/Index', [
            'wallet' => $wallet,
            'vendor' => $vendor,
            'transactions' => $transactions,
            'settlementRequests' => $settlementRequests,
        ]);
    }

    public function adjustBalance(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {
            $wallet = Wallet::lockForUpdate()->find($request->wallet_id);

            if ($request->type === 'debit' && $wallet->balance < $request->amount) {
                return back()->with('error', __('Insufficient balance for this debit operation'));
            }

            $transaction = new WalletTransaction([
                'amount' => $request->amount,
                'type' => $request->type,
                'description' => $request->reason,
            ]);

            if ($request->type === 'credit') {
                $wallet->increment('balance', $request->amount);
                $wallet->increment('total_earned', $request->amount);
            } else {
                $wallet->decrement('balance', $request->amount);
            }

            $wallet->transactions()->save($transaction);
            $wallet->save();

            return back()->with('success', __('Balance adjusted successfully'));
        });
    }

    public function approveSettlement(SettlementRequest $settlement)
    {
        return DB::transaction(function () use ($settlement) {
            // Lock the wallet and settlement for update
            $wallet = Wallet::lockForUpdate()->findOrFail($settlement->wallet_id);
           
            $settlement = SettlementRequest::lockForUpdate()->findOrFail($settlement->id);

            // Check if settlement is already processed
            if ($settlement->status !== 'pending') {
                return response()->json([
                    'message' => __('Settlement already processed'),
                    'status' => $settlement->status
                ], 400);
            }

            // Check if wallet has sufficient balance
            if ($wallet->balance < $settlement->amount) {
                return response()->json([
                    'message' => __('Insufficient wallet balance'),
                    'balance' => $wallet->balance,
                    'requested' => $settlement->amount
                ], 400);
            }

            // Debit the wallet - subtract the settlement amount from balance
            $wallet->decrement('balance', $settlement->amount);
            $wallet->decrement('pending_balance', $settlement->amount);
            $wallet->increment('total_earned', $settlement->amount);

            // Create transaction record
            $transaction = new WalletTransaction([
                'amount' => $settlement->amount,
                'type' => 'debit',
                'description' => 'Settlement processed - Request #' . $settlement->id,
            ]);
            $wallet->transactions()->save($transaction);

            // Update settlement status
            $settlement->update([
                'status' => 'approved',
                'processed_at' => now(),
                'admin_id' => Auth::id()
            ]);

            // Save wallet changes
            $wallet->save();

            // Notify the vendor
            $vendor = $wallet->user;
            $vendor->notify(new SettlementStatusNotification($settlement, 'approved'));

            return back()->with('success', __('Settlement approved successfully'));

        });
    }
    public function rejectSettlement(SettlementRequest $settlement, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($settlement->status !== 'pending') {
            return back()->with('error', __('This request has already been processed'));
        }

        $settlement->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
            'processed_at' => now(),
            'admin_id' => Auth::id(),
        ]);

        // Notify the vendor
        $vendor = $settlement->wallet->user;
        $vendor->notify(new SettlementStatusNotification($settlement, 'rejected', $request->reason));

        return back()->with('success', __('Settlement request rejected successfully'));
    }

    public function transactions(Wallet $wallet)
    {
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/Wallet/Transactions', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}