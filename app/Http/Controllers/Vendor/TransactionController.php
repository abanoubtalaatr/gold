<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = Auth::id();
        $filters = $request->only(['type']);

        $transactionsQuery = Transactions::query()
            ->where('user_id', $vendorId)
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('created_at', 'desc');

        $transactions = $transactionsQuery->paginate(10)->appends($filters);

        return Inertia::render('Vendor/Transactions/Index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'vendorDebt' => Auth::user()->debt ?? 0,
        ]);
    }
}