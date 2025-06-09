<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminWalletController extends Controller
{
    public function index()
    {
        $user = User::where('email', 'admin@admin.com')->first();
        
        $wallet = Wallet::where('user_id', User::where('email', 'admin@admin.com')->first()->id)->first();
   
        return Inertia::render('Admin/Wallet/Index', [
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
}
