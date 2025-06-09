<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\SettlementRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Notifications\Admin\NewSettlementRequest;

class WalletController extends Controller
{
    public function index()
    {
        $vendorId = Auth::user()->vendor_id??Auth::user()->id;
        
        $wallet = User::find($vendorId)->wallet()->firstOrCreate([
            'user_id' => $vendorId
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
                ->through(fn($transaction) => [
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
        $transactions = Auth::user()->wallet->transactions()
            ->latest()
            ->paginate(15);

        return Inertia::render('Vendor/Wallet/Transactions', [
            'transactions' => $transactions
                ->through(fn($transaction) => [
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
        $minPayoutAmount = SystemSetting::first()->minimum_payout_amount ?? 100;
        $wallet = Auth::user()->wallet;
        
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                "min:{$minPayoutAmount}",
                "max:{$wallet->balance}"
            ],
        ], [
            'amount.max' => __('Insufficient balance. Your current balance is :balance SAR', ['balance' => $wallet->balance]),
            'amount.min' => __('Minimum payout amount is :amount SAR', ['amount' => $minPayoutAmount]),
        ]);

        $settlementRequest = SettlementRequest::create([
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        // Notify all admins
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin')
                ->orWhere('name', 'superadmin')
                ->whereNull('vendor_id');
        })->get();

        foreach ($admins as $admin) {
            Log::info('Admin notified', [
                'admin_id' => $admin->id, 
                'settlement_request_id' => $settlementRequest->id
            ]);
            $admin->notify(new NewSettlementRequest($settlementRequest));
        }

        return back()->with('success', __('Settlement request submitted successfully'));
    }
}
