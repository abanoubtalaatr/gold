<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Services\WalletService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private WalletService $walletService)
    {
    }

    public function index(Request $request)
    {
        $wallet = $this->walletService->getWallet($request->user());
        $transactions = $this->walletService->getTransactions($request->user(), $request->only(['type', 'status']));

        return $this->successResponse([
            'wallet' => new WalletResource($wallet),
            'transactions' => WalletTransactionResource::collection($transactions),
        ]);
    }
}
