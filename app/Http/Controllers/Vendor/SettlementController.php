<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\SettlementRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;
class SettlementController extends Controller
{
    public function index()
    {
        return Inertia::render('Vendor/Wallet/Settlements', [
            'requests' => SettlementRequest::with(['wallet.user'])
                ->latest()
                ->paginate(15)
                ->through(fn($request) => [
                    'id' => $request->id,
                    'amount' => $request->amount,
                    'status' => $request->status,
                    'created_at' => $request->created_at->format('Y-m-d H:i'),
                    'user' => $request->wallet->user->name,
                ]),
        ]);
    }

    public function process(Request $request, SettlementRequest $settlement)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($settlement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        if ($request->status === 'approved') {
            // Deduct from wallet
            $settlement->wallet->update([
                'balance' => $settlement->wallet->balance - $settlement->amount
            ]);

            $settlement->wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $settlement->amount,
                'description' => 'Settlement payout #' . $settlement->id,
                'status' => 'completed',
            ]);
        }

        $settlement->update([
            'status' => $request->status,
            'admin_notes' => $request->notes,
        ]);

        return back()->with('success', 'Settlement request processed');
    }
}