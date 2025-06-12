<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\SettlementRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Vendor\SettlementStatusNotification;

class VendorSettlementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/VendorSettlementRequests/Index', [
            'requests' => SettlementRequest::with(['wallet.user'])
                ->latest()
                ->paginate(15)
                ->through(fn($request) => [
                    'id' => $request->id,
                    'amount' => $request->amount,
                    'status' => $request->status,
                    'admin_notes' => $request->admin_notes,
                    'vendor_name' => $request->wallet->user->store_name_en ?? $request->wallet->user->name,
                    'created_at' => $request->created_at->format('Y-m-d H:i'),
                    'updated_at' => $request->updated_at->format('Y-m-d H:i'),
                ]),
        ]);
    }

    public function approve(SettlementRequest $settlement)
    {
        if ($settlement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

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

        $settlement->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Notify vendor
        $settlement->wallet->user->notify(
            new SettlementStatusNotification($settlement, 'approved')
        );

        return back()->with('success', 'Settlement request approved successfully');
    }

    public function reject(Request $request, SettlementRequest $settlement)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($settlement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $settlement->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
            'admin_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Notify vendor
        $settlement->wallet->user->notify(
            new SettlementStatusNotification($settlement, 'rejected', $request->reason)
        );

        return back()->with('success', 'Settlement request rejected successfully');
    }
}