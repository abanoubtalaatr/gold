<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\SettlementRequest;
use Inertia\Inertia;
class WalletController extends Controller
{
 public function index()
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([
            'user_id' => auth()->id()
        ], [
            'balance' => 0,
            'pending_balance' => 0,
            'total_earned' => 0
        ]);

        return Inertia::render('Vendor/Wallet/Index', [
            'wallet' => $wallet,
            'transactions' => $wallet->transactions()
                ->latest()
                ->paginate(10)
                ->through(fn ($transaction) => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i'),
                ]),
        ]);
    }

    public function transactions()
    {
        $transactions = auth()->user()->wallet->transactions()
            ->latest()
            ->paginate(15);

        return Inertia::render('Vendor/Wallet/Transactions', [
            'transactions' => $transactions
                ->through(fn ($transaction) => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i'),
                ]),
        ]);
    }

    public function requestSettlement(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $wallet = auth()->user()->wallet;

        if ($request->amount > $wallet->balance) {
            return back()->with('error', 'Insufficient balance');
        }

        SettlementRequest::create([
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Settlement request submitted successfully');
    }
}
