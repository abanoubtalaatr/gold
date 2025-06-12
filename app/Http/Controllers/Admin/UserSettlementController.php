<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\LiquidationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\User\LiquidationStatusNotification;

class UserSettlementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/UserSettlementRequests/Index', [
            'requests' => LiquidationRequest::with(['user'])
                ->latest()
                ->paginate(15)
                ->through(fn($request) => [
                    'id' => $request->id,
                    'amount' => $request->amount,
                    'status' => $request->status,
                    'admin_notes' => $request->admin_notes,
                    'user_name' => $request->user->name,
                    'bank_account_name' => $request->bank_account_name,
                    'bank_account_number' => $request->bank_account_number,
                    'bank_account_iban' => $request->bank_account_iban,
                    'bank_account_holder_name' => $request->bank_account_holder_name,
                    'created_at' => $request->created_at->format('Y-m-d H:i'),
                    'updated_at' => $request->updated_at->format('Y-m-d H:i'),
                ]),
        ]);
    }

    public function approve(LiquidationRequest $liquidation)
    {
        if ($liquidation->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $liquidation->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Notify user
        $liquidation->user->notify(
            new LiquidationStatusNotification($liquidation, 'approved')
        );

        return back()->with('success', 'Liquidation request approved successfully');
    }

    public function reject(Request $request, LiquidationRequest $liquidation)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($liquidation->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $liquidation->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
            'admin_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Notify user
        $liquidation->user->notify(
            new LiquidationStatusNotification($liquidation, 'rejected', $request->reason)
        );

        return back()->with('success', 'Liquidation request rejected successfully');
    }
}